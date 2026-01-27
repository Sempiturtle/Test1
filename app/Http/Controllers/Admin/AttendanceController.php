<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Show Attendance Logs page
   public function index()
{
    $logs = Attendance::with('student')
        ->latest('time_in')
        ->take(20)
        ->get();

    $students = Student::orderBy('name')->get();

    return view('admin.attendance.index', compact('logs', 'students'));
}

    // Handle RFID Tap
    public function simulate(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required',
        ]);

        $student = Student::where('rfid_uid', $request->rfid_uid)->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'RFID not registered'
            ]);
        }

        $today = now('Asia/Manila')->toDateString();

        // Get today's attendance for this student
        $attendance = Attendance::where('student_id', $student->id)
            ->whereDate('time_in', $today)
            ->latest()
            ->first();

        if (!$attendance) {
            // FIRST TAP → TIME IN
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'rfid_uid' => $request->rfid_uid,
                'time_in' => now('Asia/Manila'),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Time In recorded',
                'attendance' => $this->formatAttendance($attendance, $student)
            ]);
        }

        if (!$attendance->time_out) {
            // SECOND TAP → TIME OUT
            $time_out = now('Asia/Manila');
            $duration = $attendance->time_in->diffInMinutes($time_out);

            $attendance->update([
                'time_out' => $time_out,
                'duration_minutes' => $duration,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Time Out recorded',
                'attendance' => $this->formatAttendance($attendance, $student)
            ]);
        }

        // Already tapped twice
        return response()->json([
            'status' => 'info',
            'message' => 'Attendance already completed today'
        ]);
    }

    // Format attendance for JSON response
    private function formatAttendance($attendance, $student)
    {
        return [
            'id' => $attendance->id,
            'student_name' => $student->name,
            'rfid_uid' => $attendance->rfid_uid,
            'time_in' => $attendance->time_in
                ? $attendance->time_in->timezone('Asia/Manila')->format('h:i:s A')
                : null,
            'time_out' => $attendance->time_out
                ? $attendance->time_out->timezone('Asia/Manila')->format('h:i:s A')
                : null,
            'duration_minutes' => $attendance->duration_minutes
        ];
    }
}

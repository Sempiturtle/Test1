<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Show attendance logs page
    public function index()
    {
        return view('admin.attendance.index', [
            'logs' => Attendance::with('student')->latest()->take(20)->get()
        ]);
    }

    // Handle RFID tap
    public function simulate(Request $request)
    {
        $rfid = $request->rfid_uid;
        $student = Student::where('rfid_uid', $rfid)->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'RFID not registered'
            ]);
        }

        $today = Carbon::today();

        // Look for today's attendance
        $attendance = Attendance::where('student_id', $student->id)
            ->whereDate('time_in', $today)
            ->latest()
            ->first();

        // Prevent rapid double tap
        if ($attendance && $attendance->updated_at->diffInSeconds(now()) < 5) {
            return response()->json([
                'status' => 'info',
                'message' => 'Please wait a few seconds before tapping again.'
            ]);
        }

        if (!$attendance) {
            // First tap → TIME IN
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'rfid_uid' => $rfid,
                'time_in' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Time In recorded',
                'attendance' => $this->attendanceResponse($attendance, $student)
            ]);
        }

        if ($attendance && !$attendance->time_out) {
            // Second tap → TIME OUT
            $attendance->update([
                'time_out' => now(),
                'duration_minutes' => $attendance->time_in->diffInMinutes(now())
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Time Out recorded',
                'attendance' => $this->attendanceResponse($attendance, $student)
            ]);
        }

        // Already completed
        return response()->json([
            'status' => 'info',
            'message' => 'Attendance already completed today'
        ]);
    }

    private function attendanceResponse($attendance, $student)
    {
        return [
            'id' => $attendance->id,
            'student_name' => $student->name,
            'rfid_uid' => $attendance->rfid_uid,
            'time_in' => $attendance->time_in?->format('h:i:s A'),
            'time_out' => $attendance->time_out?->format('h:i:s A'),
            'duration_minutes' => $attendance->duration_minutes
        ];
    }
}

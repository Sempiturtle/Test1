<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    // Show attendance logs page
    public function index()
    {
        $logs = Attendance::with('student')->latest()->take(20)->get(); // show latest 20
        return view('admin.attendance', compact('logs'));
    }

    // Simulate manual RFID tap
    public function simulate(Request $request)
    {
        $request->validate(['rfid_uid' => 'required']);
        $student = Student::where('rfid_uid', $request->rfid_uid)->first();

        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'RFID not registered']);
        }

        $attendance = Attendance::where('student_id', $student->id)
            ->whereDate('created_at', now())
            ->first();

        if (!$attendance) {
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'rfid_uid' => $student->rfid_uid,
                'session' => 'Peer Counseling',
                'time_in' => now()
            ]);

            return response()->json(['status' => 'success', 'message' => 'Time In recorded', 'attendance' => $attendance]);
        }

        if ($attendance->time_out) {
            return response()->json(['status' => 'error', 'message' => 'Attendance already completed today']);
        }

        $attendance->time_out = now();
        $attendance->save();

        return response()->json(['status' => 'success', 'message' => 'Time Out recorded', 'attendance' => $attendance]);
    }

    // Return latest logs for AJAX
    public function latestLogs()
    {
        $logs = Attendance::with('student')->latest()->take(20)->get();
        return response()->json($logs);
    }
}

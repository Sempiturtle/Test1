<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('admin.attendance.index', [
            'logs' => Attendance::with('student')->latest()->take(20)->get()
        ]);
    }

    public function simulate(Request $request)
    {
        $rfid = $request->rfid_uid;

        $student = Student::where('rfid_uid', $rfid)->first();
        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'RFID not registered']);
        }

        $today = Carbon::today();

        // Get today's attendance for this student
        $attendance = Attendance::where('student_id', $student->id)
            ->whereDate('time_in', $today)
            ->latest()
            ->first();

        if (!$attendance) {
            // First tap → time_in
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'rfid_uid' => $rfid,
                'time_in' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Time In recorded',
                'attendance' => [
                    'id' => $attendance->id,
                    'student_name' => $student->name,
                    'rfid_uid' => $rfid,
                    'time_in' => $attendance->time_in,
                    'time_out' => null,
                    'duration_minutes' => null
                ]
            ]);
        } elseif ($attendance && !$attendance->time_out) {
            // Second tap → time_out
            $attendance->time_out = now();
            $attendance->save();

            $duration = $attendance->time_in->diffInMinutes($attendance->time_out);

            return response()->json([
                'status' => 'success',
                'message' => 'Time Out recorded',
                'attendance' => [
                    'id' => $attendance->id,
                    'student_name' => $student->name,
                    'rfid_uid' => $rfid,
                    'time_in' => $attendance->time_in,
                    'time_out' => $attendance->time_out,
                    'duration_minutes' => $duration
                ]
            ]);
        } else {
            // Already completed
            return response()->json([
                'status' => 'info',
                'message' => 'You have already completed your attendance for today'
            ]);
        }
    }

    public function latestLogs()
    {
        return Attendance::with('student')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($log) => [
                'id' => $log->id,
                'student' => $log->student->name,
                'rfid_uid' => $log->rfid_uid,
                'time_in' => optional($log->time_in)->format('h:i A'),
                'time_out' => $log->time_out ? $log->time_out->format('h:i A') : null,
                'duration_minutes' => $log->duration_minutes
            ]);
    }
}

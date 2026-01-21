<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalStudents = Student::count();

        // Attendance today = students who have tapped at least once
        $attendanceToday = Attendance::whereDate('created_at', now())->count();

        // Last 7 days attendance for line chart
        $attendancePerDay = Attendance::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Donut chart: Present vs Absent
        $presentToday = $attendanceToday;
        $absentToday = max(0, $totalStudents - $attendanceToday);

        // Recent logs (last 10)
        $recentAttendances = Attendance::with('student')->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'attendanceToday',
            'attendancePerDay',
            'presentToday',
            'absentToday',
            'recentAttendances'
        ));
    }

    public function rfidTap(Request $request)
    {
        $uid = $request->rfid_uid;

        $student = Student::where('rfid_uid', $uid)->first();
        if (!$student) {
            return response()->json(['status' => 'error', 'message' => 'Student not found']);
        }

        // Check today's attendance
        $attendance = Attendance::where('student_id', $student->id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        if (!$attendance) {
            // No record yet → Time In
            Attendance::create([
                'student_id' => $student->id,
                'rfid_uid' => $uid,
                'time_in' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Time In recorded']);
        }

        if (!$attendance->time_out) {
            // Time Out
            $timeOut = now();
            $duration = $attendance->time_in->diffInMinutes($timeOut);

            $attendance->update([
                'time_out' => $timeOut,
                'duration_minutes' => $duration,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Time Out recorded (' . $duration . ' mins)'
            ]);
        }

        // Already clocked in and out
        return response()->json([
            'status' => 'info',
            'message' => 'You have already completed your attendance for today'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
{
    $today = now('Asia/Manila')->toDateString();

    $totalStudents = Student::count();

    // Attendance today (correct timezone)
    $attendanceToday = Attendance::whereDate('time_in', $today)->count();

    // Attendance per day (last 7 days)
    $attendancePerDay = Attendance::select(
            DB::raw('DATE(time_in) as date'),
            DB::raw('COUNT(*) as total')
        )
        ->where('time_in', '>=', now('Asia/Manila')->subDays(6))
        ->groupBy(DB::raw('DATE(time_in)'))
        ->orderBy('date')
        ->get();

    $presentToday = $attendanceToday;
    $absentToday = max(0, $totalStudents - $attendanceToday);

    // ✅ COURSE PARTICIPATION (THIS WAS MISSING)
    $courseParticipation = Attendance::join('students', 'attendances.student_id', '=', 'students.id')
        ->whereDate('attendances.time_in', $today)
        ->select('students.course', DB::raw('COUNT(*) as total'))
        ->groupBy('students.course')
        ->pluck('total', 'students.course');

    // Recent attendance logs
    $recentAttendances = Attendance::with('student')
        ->latest('time_in')
        ->take(10)
        ->get();

    return view('admin.dashboard', compact(
        'totalStudents',
        'attendanceToday',
        'attendancePerDay',
        'presentToday',
        'absentToday',
        'recentAttendances',
        'courseParticipation' // ✅ IMPORTANT
    ));
}

}

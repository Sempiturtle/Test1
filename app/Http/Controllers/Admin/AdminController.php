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
        $totalStudents = Student::count();

        $attendanceToday = Attendance::whereDate('time_in', now())->count();

        $attendancePerDay = Attendance::select(
            DB::raw('DATE(time_in) as date'),
            DB::raw('COUNT(*) as total')
        )
            ->where('time_in', '>=', now()->subDays(6))
            ->groupBy(DB::raw('DATE(time_in)'))
            ->orderBy('date')
            ->get();

        $presentToday = $attendanceToday;
        $absentToday = max(0, $totalStudents - $attendanceToday);

        $recentAttendances = Attendance::with('student')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'attendanceToday',
            'attendancePerDay',
            'presentToday',
            'absentToday',
            'recentAttendances'
        ));
    }
}

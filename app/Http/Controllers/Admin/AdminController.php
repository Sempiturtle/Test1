<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $today = now('Asia/Manila')->toDateString();

        // Basic Stats
        $totalStudents = Student::count();
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

        // Course Participation TODAY
        $courseParticipation = Attendance::join('students', 'attendances.student_id', '=', 'students.id')
            ->whereDate('attendances.time_in', $today)
            ->select('students.course', DB::raw('COUNT(*) as total'))
            ->groupBy('students.course')
            ->pluck('total', 'students.course');

        // ========== NEW MEANINGFUL METRICS ==========
        
        // Attendance Rate (Percentage)
        $attendanceRate = $totalStudents > 0 ? round(($attendanceToday / $totalStudents) * 100, 1) : 0;
        
        // This Week vs Last Week
        $thisWeekStart = now('Asia/Manila')->startOfWeek();
        $thisWeekEnd = now('Asia/Manila')->endOfWeek();
        $lastWeekStart = now('Asia/Manila')->subWeek()->startOfWeek();
        $lastWeekEnd = now('Asia/Manila')->subWeek()->endOfWeek();
        
        $attendanceThisWeek = Attendance::whereBetween('time_in', [$thisWeekStart, $thisWeekEnd])->count();
        $attendanceLastWeek = Attendance::whereBetween('time_in', [$lastWeekStart, $lastWeekEnd])->count();
        
        $weeklyChange = $attendanceLastWeek > 0 
            ? round((($attendanceThisWeek - $attendanceLastWeek) / $attendanceLastWeek) * 100, 1) 
            : 0;
        
        $weeklyTrend = $weeklyChange > 0 ? 'up' : ($weeklyChange < 0 ? 'down' : 'stable');

        // Students Currently Checked In (no time_out yet)
        $activeStudentsNow = Attendance::whereDate('time_in', $today)
            ->whereNull('time_out')
            ->count();

        // Peak Hour Today
        $peakHourToday = Attendance::selectRaw('HOUR(time_in) as hour, COUNT(*) as count')
            ->whereDate('time_in', $today)
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->first();

        $peakHour = $peakHourToday ? Carbon::createFromTime($peakHourToday->hour)->format('g:00 A') : 'N/A';
        $peakHourCount = $peakHourToday->count ?? 0;

        // Students Needing Attention (haven't attended in 7+ days)
        $studentsNeedingAttention = Student::whereDoesntHave('attendances', function($query) {
                $query->where('time_in', '>=', Carbon::now()->subDays(7));
            })
            ->whereHas('attendances') // Has attended before, but not recently
            ->with(['attendances' => function($query) {
                $query->latest('time_in')->limit(1);
            }])
            ->take(5)
            ->get()
            ->map(function($student) {
                $lastAttendance = $student->attendances->first();
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'course' => $student->course,
                    'last_attendance' => $lastAttendance ? $lastAttendance->time_in->diffForHumans() : 'Never',
                    'days_ago' => $lastAttendance ? $lastAttendance->time_in->diffInDays(now()) : 999
                ];
            });

        // Recent attendance logs (with student info)
        $recentAttendances = Attendance::with('student')
            ->latest('time_in')
            ->take(10)
            ->get();

        // Most Active Students This Week
        $mostActiveThisWeek = Attendance::select('student_id', DB::raw('COUNT(*) as attendance_count'))
            ->whereBetween('time_in', [$thisWeekStart, $thisWeekEnd])
            ->groupBy('student_id')
            ->orderBy('attendance_count', 'desc')
            ->with('student')
            ->take(5)
            ->get();

        // New Students This Month
        $newStudentsThisMonth = Student::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('admin.dashboard', compact(
            'totalStudents',
            'attendanceToday',
            'attendancePerDay',
            'presentToday',
            'absentToday',
            'courseParticipation',
            'attendanceRate',
            'attendanceThisWeek',
            'attendanceLastWeek',
            'weeklyChange',
            'weeklyTrend',
            'activeStudentsNow',
            'peakHour',
            'peakHourCount',
            'studentsNeedingAttention',
            'recentAttendances',
            'mostActiveThisWeek',
            'newStudentsThisMonth'
        ));
    }
}
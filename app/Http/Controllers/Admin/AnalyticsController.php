<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Overview Stats
        $totalStudents = Student::count();
        $totalAttendances = Attendance::count();
        $uniqueAttendees = Attendance::distinct('student_id')->count('student_id');
        $averageAttendanceRate = $totalStudents > 0 ? round(($uniqueAttendees / $totalStudents) * 100, 1) : 0;
        
        // Participation Analytics - Total attendance per student
        $participationAnalytics = $this->getParticipationAnalytics();
        
        // Most Active Students
        $mostActiveStudents = $this->getMostActiveStudents();
        
        // Peak Attendance Times
        $peakTimes = $this->getPeakAttendanceTimes();
        
        // Attendance Trends (Last 30 Days)
        $attendanceTrends = $this->getAttendanceTrends(30);
        
        // No-Show Analysis (Students who haven't attended recently)
        $noShowAnalysis = $this->getNoShowAnalysis();
        
        // Student Engagement Scores
        $engagementScores = $this->getStudentEngagementScores();
        
        // Top Engaged Students
        $topEngagedStudents = $this->getTopEngagedStudents(10);
        
        // At-Risk Students (Low engagement)
        $atRiskStudents = $this->getAtRiskStudents();

        // Course Participation
        $courseParticipation = $this->getCourseParticipation();

        // Weekly comparison
        $weeklyComparison = $this->getWeeklyComparison();

        return view('admin.analytics.index', compact(
            'totalStudents',
            'totalAttendances',
            'uniqueAttendees',
            'averageAttendanceRate',
            'participationAnalytics',
            'mostActiveStudents',
            'peakTimes',
            'attendanceTrends',
            'noShowAnalysis',
            'engagementScores',
            'topEngagedStudents',
            'atRiskStudents',
            'courseParticipation',
            'weeklyComparison'
        ));
    }

    // 1. Total attendance per student
    private function getParticipationAnalytics()
    {
        return Student::withCount('attendances')
            ->orderBy('attendances_count', 'desc')
            ->take(10)
            ->get()
            ->map(function($student) {
                $lastAttendance = $student->attendances()->latest('time_in')->first();
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'course' => $student->course,
                    'total_attendance' => $student->attendances_count,
                    'last_attendance' => $lastAttendance ? $lastAttendance->time_in->format('M d, Y') : 'Never',
                    'engagement_score' => $this->calculateEngagementScore($student)
                ];
            });
    }

    // Most active students
    private function getMostActiveStudents()
    {
        return Student::withCount('attendances')
            ->having('attendances_count', '>', 0)
            ->orderBy('attendances_count', 'desc')
            ->take(10)
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'course' => $student->course,
                    'total_attendance' => $student->attendances_count,
                    'this_month' => $student->attendances()->whereMonth('time_in', now()->month)->count()
                ];
            });
    }

    // 3. Peak attendance times (hour of day and day of week)
    private function getPeakAttendanceTimes()
    {
        // By hour
        $byHour = Attendance::selectRaw('HOUR(time_in) as hour, COUNT(*) as count')
            ->whereNotNull('time_in')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function($item) {
                $hour = $item->hour;
                $period = $hour >= 12 ? 'PM' : 'AM';
                $displayHour = $hour > 12 ? $hour - 12 : ($hour == 0 ? 12 : $hour);
                return [
                    'hour' => $displayHour . ':00 ' . $period,
                    'count' => $item->count
                ];
            });

        // By day of week
        $byDayOfWeek = Attendance::selectRaw('DAYNAME(time_in) as day_name, COUNT(*) as count')
            ->whereNotNull('time_in')
            ->groupBy('day_name')
            ->orderByRaw("FIELD(day_name, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->get();

        return [
            'by_hour' => $byHour,
            'by_day' => $byDayOfWeek,
            'peak_hour' => $byHour->sortByDesc('count')->first(),
            'peak_day' => $byDayOfWeek->sortByDesc('count')->first()
        ];
    }

    // 4. Attendance trends over time
    private function getAttendanceTrends($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        $trends = Attendance::selectRaw('DATE(time_in) as date, COUNT(*) as count')
            ->where('time_in', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill in missing dates with 0
        $allDates = collect();
        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($days - $i - 1)->format('Y-m-d');
            $trend = $trends->get($date);
            $allDates->push([
                'date' => $date,
                'count' => $trend ? $trend->count : 0,
                'formatted_date' => Carbon::parse($date)->format('M d')
            ]);
        }

        return $allDates;
    }

    // 5. No-show analysis (students who haven't attended recently)
    private function getNoShowAnalysis()
    {
        $totalStudents = Student::count();
        $activeThisMonth = Attendance::whereMonth('time_in', now()->month)
            ->distinct('student_id')
            ->count('student_id');
        
        $inactiveCount = $totalStudents - $activeThisMonth;
        $inactiveRate = $totalStudents > 0 ? round(($inactiveCount / $totalStudents) * 100, 1) : 0;

        // Students who never attended
        $neverAttended = Student::doesntHave('attendances')->count();

        // Students inactive for 30+ days
        $inactiveLongTerm = Student::whereDoesntHave('attendances', function($query) {
            $query->where('time_in', '>=', Carbon::now()->subDays(30));
        })->whereHas('attendances')->count();

        return [
            'total_students' => $totalStudents,
            'active_this_month' => $activeThisMonth,
            'inactive_count' => $inactiveCount,
            'inactive_rate' => $inactiveRate,
            'never_attended' => $neverAttended,
            'inactive_long_term' => $inactiveLongTerm
        ];
    }

    // 6. Student engagement scores
    private function getStudentEngagementScores()
    {
        $students = Student::withCount('attendances')->get();
        
        $scores = $students->map(function($student) {
            $score = $this->calculateEngagementScore($student);
            $level = $this->getEngagementLevel($score);
            
            return [
                'id' => $student->id,
                'name' => $student->name,
                'score' => $score,
                'level' => $level,
                'attendance_count' => $student->attendances_count
            ];
        });

        // Group by engagement level
        $distribution = [
            'high' => $scores->where('level', 'High')->count(),
            'medium' => $scores->where('level', 'Medium')->count(),
            'low' => $scores->where('level', 'Low')->count(),
        ];

        return [
            'distribution' => $distribution,
            'average_score' => round($scores->avg('score'), 1),
            'scores' => $scores->sortByDesc('score')
        ];
    }

    // Calculate engagement score for a student
    private function calculateEngagementScore($student)
    {
        $attendanceCount = $student->attendances_count ?? $student->attendances()->count();
        
        // Score based on total attendance
        $baseScore = min(50, $attendanceCount * 5);
        
        // Recent activity bonus (last 30 days)
        $recentAttendance = $student->attendances()
            ->where('time_in', '>=', Carbon::now()->subDays(30))
            ->count();
        
        $recentBonus = min(30, $recentAttendance * 10);
        
        // Consistency bonus (attended in last 7 days)
        $lastWeekAttendance = $student->attendances()
            ->where('time_in', '>=', Carbon::now()->subDays(7))
            ->count();
        
        $consistencyBonus = $lastWeekAttendance > 0 ? 20 : 0;
        
        $score = min(100, $baseScore + $recentBonus + $consistencyBonus);
        
        return round($score, 1);
    }

    // Get engagement level
    private function getEngagementLevel($score)
    {
        if ($score >= 70) return 'High';
        if ($score >= 40) return 'Medium';
        return 'Low';
    }

    // Top engaged students
    private function getTopEngagedStudents($limit = 10)
    {
        return Student::withCount('attendances')
            ->having('attendances_count', '>', 0)
            ->orderBy('attendances_count', 'desc')
            ->take($limit)
            ->get()
            ->map(function($student) {
                $lastAttendance = $student->attendances()->latest('time_in')->first();
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'course' => $student->course,
                    'attendance_count' => $student->attendances_count,
                    'engagement_score' => $this->calculateEngagementScore($student),
                    'last_attendance' => $lastAttendance ? $lastAttendance->time_in->format('M d, Y') : 'Never'
                ];
            });
    }

    // At-risk students (low engagement)
    private function getAtRiskStudents()
    {
        $students = Student::withCount('attendances')->get();
        
        return $students->filter(function($student) {
            $score = $this->calculateEngagementScore($student);
            $lastAttendance = $student->attendances()->latest('time_in')->first();
            $daysSinceLastAttendance = $lastAttendance ? 
                Carbon::parse($lastAttendance->time_in)->diffInDays(now()) : 999;
            
            // At risk if: score < 30 OR no attendance in 30+ days OR never attended
            return $score < 30 || $daysSinceLastAttendance > 30 || $student->attendances_count == 0;
        })->map(function($student) {
            $lastAttendance = $student->attendances()->latest('time_in')->first();
            $daysSince = $lastAttendance ? 
                Carbon::parse($lastAttendance->time_in)->diffInDays(now()) : null;
            
            return [
                'id' => $student->id,
                'name' => $student->name,
                'course' => $student->course,
                'attendance_count' => $student->attendances_count,
                'engagement_score' => $this->calculateEngagementScore($student),
                'days_since_last_attendance' => $daysSince,
                'last_attendance' => $lastAttendance ? $lastAttendance->time_in->format('M d, Y') : 'Never',
                'risk_level' => $this->getRiskLevel($this->calculateEngagementScore($student), $daysSince)
            ];
        })->sortByDesc(function($student) {
            return $student['days_since_last_attendance'] ?? 999;
        })->values();
    }

    // Get risk level
    private function getRiskLevel($score, $daysSince)
    {
        if ($daysSince === null || $daysSince > 60 || $score < 20) return 'Critical';
        if ($score < 40 || $daysSince > 30) return 'High';
        return 'Medium';
    }

    // Course participation
    private function getCourseParticipation()
    {
        return Attendance::join('students', 'attendances.student_id', '=', 'students.id')
            ->select('students.course', DB::raw('COUNT(DISTINCT attendances.student_id) as unique_students'), DB::raw('COUNT(*) as total_attendance'))
            ->groupBy('students.course')
            ->get()
            ->map(function($item) {
                return [
                    'course' => $item->course,
                    'unique_students' => $item->unique_students,
                    'total_attendance' => $item->total_attendance,
                    'avg_per_student' => $item->unique_students > 0 ? round($item->total_attendance / $item->unique_students, 1) : 0
                ];
            });
    }

    // Weekly comparison
    private function getWeeklyComparison()
    {
        $thisWeek = Attendance::whereBetween('time_in', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();

        $lastWeek = Attendance::whereBetween('time_in', [
            now()->subWeek()->startOfWeek(),
            now()->subWeek()->endOfWeek()
        ])->count();

        $change = $lastWeek > 0 ? round((($thisWeek - $lastWeek) / $lastWeek) * 100, 1) : 0;

        return [
            'this_week' => $thisWeek,
            'last_week' => $lastWeek,
            'change' => $change,
            'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable')
        ];
    }

    // Export analytics data
    public function export(Request $request)
    {
        $type = $request->get('type', 'pdf');
        
        // For now, redirect back - we'll implement export later
        return redirect()->back()->with('info', 'Export feature coming soon!');
    }
}
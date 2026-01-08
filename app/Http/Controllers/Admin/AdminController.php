<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $totalStudents = Student::count();
        $attendanceToday = Attendance::whereDate('created_at', today())->count();

        // Last 7 days attendance
        $weeklyAttendance = Attendance::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Students per course
        $studentsPerCourse = Student::selectRaw('course, COUNT(*) as total')
            ->groupBy('course')
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'attendanceToday',
            'weeklyAttendance',
            'studentsPerCourse'
        ));
    }

    // Students Page
    public function students()
    {
        $students = Student::latest()->get();
        return view('admin.students', compact('students'));
    }

    // Attendance Page
    public function attendance()
    {
        $logs = Attendance::with('student')->latest()->get();
        return view('admin.attendance', compact('logs'));
    }

    // Simulate RFID Tap
    public function simulateRFID(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required'
        ]);

        $student = Student::where('rfid_uid', $request->rfid_uid)->first();

        if (!$student) {
            return back()->with('error', 'RFID not found!');
        }

        Attendance::create([
            'student_id' => $student->id,
            'rfid_uid' => $student->rfid_uid,
            'session' => 'Peer Counseling',
            'time_in' => now()
        ]);

        return back()->with('success', 'Attendance recorded for '.$student->name);
    }
}

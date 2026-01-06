<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalStudents' => Student::count(),
            'attendanceToday' => Attendance::whereDate('created_at', today())->count(),
            'attendanceLogs' => Attendance::with('student')->latest()->get()
        ]);
    }

    public function simulateRFID(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required'
        ]);

        $student = Student::where('rfid_uid', $request->rfid_uid)->first();

        if (!$student) {
            return back()->with('error', 'RFID not registered');
        }

        $alreadyTapped = Attendance::where('student_id', $student->id)
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadyTapped) {
            return back()->with('error', 'Attendance already recorded today');
        }

        Attendance::create([
            'student_id' => $student->id,
            'rfid_uid' => $student->rfid_uid,
            'session' => 'Peer Counseling',
            'time_in' => now()
        ]);

        return back()->with('success', 'Attendance recorded successfully');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('admin.attendance', [
            'logs' => Attendance::with('student')->latest()->get()
        ]);
    }

    public function simulate(Request $request)
    {
        $request->validate(['rfid_uid' => 'required']);

        $student = Student::where('rfid_uid', $request->rfid_uid)->first();

        if (!$student) {
            return back()->with('error', 'RFID not registered');
        }

        if (Attendance::where('student_id', $student->id)
            ->whereDate('created_at', today())->exists()) {
            return back()->with('error', 'Already recorded today');
        }

        Attendance::create([
            'student_id' => $student->id,
            'rfid_uid' => $student->rfid_uid,
            'session' => 'Peer Counseling',
            'time_in' => now()
        ]);

        return back()->with('success', 'Attendance recorded');
    }
}

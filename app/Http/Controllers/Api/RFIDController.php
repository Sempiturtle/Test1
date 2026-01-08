<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;

class RFIDController extends Controller
{
    public function store(Request $request)
    {
        $student = Student::where('rfid_uid', $request->rfid_uid)->first();

        if (!$student) {
            return response()->json(['message' => 'RFID not registered'], 404);
        }

        Attendance::create([
            'student_id' => $student->id,
            'rfid_uid' => $student->rfid_uid,
            'session' => 'Peer Counseling',
            'time_in' => now()
        ]);

        return response()->json(['message' => 'Attendance recorded']);
    }
}

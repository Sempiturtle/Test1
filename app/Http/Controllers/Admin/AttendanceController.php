<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Show Attendance Logs page
    public function index(Request $request)
    {
        $query = Attendance::with('student');

        // Professional Academic Filtering
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('course', $request->course);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('time_in', $request->date);
        }

        $logs = $query->latest('time_in')->paginate(20)->withQueryString();
        $students = Student::orderBy('name')->get();

        return view('admin.attendance.index', compact('logs', 'students'));
    }

    // Handle RFID Tap
    public function simulate(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required',
        ]);

        $student = Student::where('rfid_uid', $request->rfid_uid)->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'RFID not registered'
            ]);
        }

        // Check for daily completion (block third tap)
        if ($this->checkDailyLimit($student->id)) {
            return response()->json([
                'status' => 'info',
                'message' => 'Daily protocol complete. Access locked.'
            ]);
        }

        // Find the most recent session for this student that hasn't timed out yet
        // We look for any session within the last 24 hours to be safe, regardless of timezone weirdness
        $attendance = Attendance::where('student_id', $student->id)
            ->whereNull('time_out')
            ->where('time_in', '>', now('Asia/Manila')->subHours(24))
            ->latest()
            ->first();

        if (!$attendance) {
            // FIRST TAP → TIME IN
            $attendance = Attendance::create([
                'student_id' => $student->id,
                'rfid_uid' => $request->rfid_uid,
                'time_in' => now('Asia/Manila'),
                'status' => 'present' // Ensure status is set
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Time In recorded',
                'attendance' => $this->formatAttendance($attendance, $student)
            ]);
        }

        // IF RECORD EXISTS AND NO TIME OUT → PROCESS TIME OUT
        // Anti-double tap protection (1 minute cooldown)
        if ($attendance->time_in->diffInMinutes(now('Asia/Manila')) < 1) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Cooldown active. Please wait 1 minute.'
            ]);
        }

        // SECOND TAP → TIME OUT
        $time_out = now('Asia/Manila');
        $duration = $attendance->time_in->diffInMinutes($time_out);

        $attendance->update([
            'time_out' => $time_out,
            'duration_minutes' => $duration,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Time Out recorded',
            'attendance' => $this->formatAttendance($attendance->fresh(), $student)
        ]);
    }

    // Check if student already finished for the day
    private function checkDailyLimit($studentId)
    {
        $today = now('Asia/Manila')->toDateString();
        return Attendance::where('student_id', $studentId)
            ->whereDate('time_in', $today)
            ->whereNotNull('time_out')
            ->exists();
    }

    public function updateNote(Request $request, Attendance $attendance)
    {
        $request->validate([
            'notes' => 'nullable|string',
            'category' => 'nullable|string',
            'severity' => 'required|in:low,medium,high'
        ]);

        $attendance->update($request->only('notes', 'category', 'severity'));

        // Automated Risk Trigger
        if ($request->severity === 'high') {
            $attendance->student->update([
                'is_at_risk' => true,
                'risk_level' => 'high'
            ]);
        }

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'ADD_CASE_NOTE',
            'details' => "Added note for: {$attendance->student->name} (Severity: {$request->severity})",
            'ip_address' => request()->ip()
        ]);

        return response()->json(['success' => true]);
    }

    // Format attendance for JSON response
    private function formatAttendance($attendance, $student)
    {
        return [
            'id' => $attendance->id,
            'student_name' => $student->name,
            'rfid_uid' => $attendance->rfid_uid,
            'time_in' => $attendance->time_in
                ? $attendance->time_in->timezone('Asia/Manila')->format('h:i:s A')
                : null,
            'time_out' => $attendance->time_out
                ? $attendance->time_out->timezone('Asia/Manila')->format('h:i:s A')
                : null,
            'duration_minutes' => $attendance->duration_minutes,
            'notes' => $attendance->notes,
            'category' => $attendance->category,
            'severity' => $attendance->severity,
        ];
    }
}

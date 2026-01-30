<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramSession;
use App\Models\Program;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $programs = Program::active()->get();
        $students = \App\Models\Student::orderBy('name')->get(); 
        $upcoming = \App\Models\Appointment::with('student')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();
            
        return view('admin.calendar.index', compact('programs', 'students', 'upcoming'));
    }

    public function events()
    {
        // 1. Program Sessions
        $sessions = ProgramSession::with('program')->get()->map(function ($session) {
            return [
                'id' => 'session-' . $session->id,
                'title' => $session->program->name,
                'start' => $session->scheduled_date->format('Y-m-d') . 'T' . $session->start_time,
                'end' => $session->scheduled_date->format('Y-m-d') . 'T' . ($session->end_time ?? $session->start_time),
                'extendedProps' => [
                    'type' => 'session',
                    'location' => $session->location,
                    'status' => $session->status,
                    'category' => $session->program->category,
                ],
                'backgroundColor' => $this->getCategoryColor($session->program->category),
                'borderColor' => 'transparent',
            ];
        });

        // 2. Appointments
        $appointments = \App\Models\Appointment::with('student', 'user')->get()->map(function ($appt) {
            $start = $appt->scheduled_at->format('Y-m-d\TH:i:s');
            // USE COPY TO PREVENT ALTERING START TIME
            $end = $appt->scheduled_at->copy()->addMinutes($appt->duration_minutes)->format('Y-m-d\TH:i:s');
            
            return [
                'id' => 'appt-' . $appt->id,
                'title' => ($appt->student->name ?? 'Unknown') . ' - Counseling',
                'start' => $start,
                'end' => $end,
                'extendedProps' => [
                    'type' => 'appointment',
                    'student_id' => $appt->student_id,
                    'counselor' => $appt->user->name ?? 'System',
                    'status' => $appt->status,
                    'notes' => $appt->notes
                ],
                'backgroundColor' => '#f59e0b', // Amber
                'borderColor' => 'transparent',
            ];
        });

        $allEvents = $sessions->merge($appointments);
        \Log::info('Calendar Events Requested', ['count' => $allEvents->count()]);
        
        return response()->json($allEvents);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15',
            'notes' => 'nullable|string'
        ]);

        \App\Models\Appointment::create([
            'student_id' => $validated['student_id'],
            'user_id' => auth()->id(),
            'scheduled_at' => $validated['scheduled_at'],
            'duration_minutes' => $validated['duration_minutes'],
            'notes' => $validated['notes'],
            'status' => 'Scheduled'
        ]);

        return response()->json(['success' => true]);
    }

    private function getCategoryColor($category)
    {
        $colors = [
            'mental health' => '#f43f5e', // rose
            'academic support' => '#6366f1', // indigo
            'guidance' => '#10b981', // emerald
            'wellness' => '#22d3ee', // cyan
        ];

        return $colors[strtolower($category)] ?? '#64748b';
    }
}

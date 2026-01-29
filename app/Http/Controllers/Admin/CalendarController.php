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
        return view('admin.calendar.index', compact('programs'));
    }

    public function events()
    {
        $sessions = ProgramSession::with('program')->get();
        
        $events = $sessions->map(function ($session) {
            return [
                'id' => $session->id,
                'title' => $session->program->name,
                'start' => $session->scheduled_date->format('Y-m-d') . 'T' . $session->start_time,
                'end' => $session->scheduled_date->format('Y-m-d') . 'T' . ($session->end_time ?? $session->start_time),
                'extendedProps' => [
                    'location' => $session->location,
                    'status' => $session->status,
                    'category' => $session->program->category,
                ],
                'backgroundColor' => $this->getCategoryColor($session->program->category),
                'borderColor' => 'transparent',
            ];
        });

        return response()->json($events);
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

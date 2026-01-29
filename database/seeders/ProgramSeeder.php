<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\ProgramSession;
use Carbon\Carbon;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            [
                'name' => 'Guidance Counseling Workshop',
                'description' => 'Professional skills training for student leadership and guidance.',
                'category' => 'guidance',
                'capacity' => 30,
            ],
            [
                'name' => 'Mental Health 101',
                'description' => 'Basic introduction to mental health awareness.',
                'category' => 'mental health',
                'capacity' => 50,
            ],
            [
                'name' => 'Academic Stress Management',
                'description' => 'Techniques to manage stress from academic workload.',
                'category' => 'academic support',
                'capacity' => 40,
            ],
            [
                'name' => 'Yoga and Mindfulness',
                'description' => 'Weekly session for yoga and mindfulness exercises.',
                'category' => 'wellness',
                'capacity' => 25,
            ],
        ];

        foreach ($programs as $p) {
            $program = Program::create($p);

            // Create some sessions for the calendar
            for ($i = -5; $i <= 10; $i++) {
                ProgramSession::create([
                    'program_id' => $program->id,
                    'scheduled_date' => Carbon::today()->addDays($i),
                    'start_time' => '14:00:00',
                    'end_time' => '16:00:00',
                    'location' => 'Room ' . rand(101, 505),
                    'capacity' => $program->capacity,
                    'status' => $i < 0 ? 'completed' : 'scheduled',
                ]);
            }
        }
    }
}

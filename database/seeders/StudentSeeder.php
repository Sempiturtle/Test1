<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    public function run()
    {
        Student::create([
            'student_id' => '2023-001',
            'name' => 'Juan Dela Cruz',
            'course' => 'BSIT',
            'rfid_uid' => 'RFID123456'
        ]);

        Student::create([
            'student_id' => '2023-002',
            'name' => 'Maria Santos',
            'course' => 'BSIT',
            'rfid_uid' => 'RFID654321'
        ]);

       
    }
}

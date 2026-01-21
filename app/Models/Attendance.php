<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'rfid_uid',
        'session',
        'time_in',
        'time_out',
        'duration_minutes',
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
    ];  

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

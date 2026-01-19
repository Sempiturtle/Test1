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
        'time_out'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

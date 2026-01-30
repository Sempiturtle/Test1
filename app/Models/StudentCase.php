<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\User;

class StudentCase extends Model
{
    protected $fillable = [
        'student_id',
        'user_id',
        'title',
        'description',
        'type',
        'severity',
        'status'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

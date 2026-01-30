<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'course',
        'rfid_uid',
        'risk_level',
        'last_follow_up_at',
        'is_at_risk',
        'risk_factors',
    ];

    protected $casts = [
        'last_follow_up_at' => 'datetime',
        'is_at_risk' => 'boolean',
    ];

    // Relationships
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function cases()
    {
        return $this->hasMany(StudentCase::class);
    }

    // Analytics helper methods
    public function getTotalAttendanceCount()
    {
        return $this->attendances()->count();
    }

    public function getLastAttendance()
    {
        return $this->attendances()->latest('time_in')->first();
    }

    public function getAttendanceThisMonth()
    {
        return $this->attendances()
            ->whereMonth('time_in', now()->month)
            ->whereYear('time_in', now()->year)
            ->count();
    }

    public function hasAttendedToday()
    {
        return $this->attendances()
            ->whereDate('time_in', today())
            ->exists();
    }
}
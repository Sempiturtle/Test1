<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'session_id',
        'rfid_uid',
        'session',
        'time_in',
        'time_out',
        'duration_minutes',
        'status',
        'notes',
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('time_in', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('time_in', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('time_in', now()->month)
                     ->whereYear('time_in', now()->year);
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('time_out');
    }

    public function scopeInSession($query)
    {
        return $query->whereNull('time_out');
    }

    // Helper methods
    public function isLate()
    {
        return $this->status === 'late';
    }

    public function isCompleted()
    {
        return !is_null($this->time_out);
    }
}
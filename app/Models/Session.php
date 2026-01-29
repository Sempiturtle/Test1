<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'scheduled_date',
        'start_time',
        'end_time',
        'location',
        'capacity',
        'status',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Relationships
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', now())
                     ->where('status', 'scheduled')
                     ->orderBy('scheduled_date');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', today());
    }

    // Analytics Methods
    public function getAttendanceCount()
    {
        return $this->attendances()->count();
    }

    public function getAttendanceRate()
    {
        if (!$this->capacity) return null;
        
        $attendanceCount = $this->getAttendanceCount();
        return round(($attendanceCount / $this->capacity) * 100, 1);
    }

    public function getLateCount()
    {
        return $this->attendances()->where('status', 'late')->count();
    }
}
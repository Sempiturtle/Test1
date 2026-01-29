<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function attendances()
    {
        return $this->hasManyThrough(Attendance::class, Session::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Analytics Methods
    public function getTotalAttendanceCount()
    {
        return $this->attendances()->count();
    }

    public function getUniqueStudentCount()
    {
        return $this->attendances()->distinct('student_id')->count('student_id');
    }

    public function getAverageAttendancePerSession()
    {
        $sessionCount = $this->sessions()->where('status', 'completed')->count();
        if ($sessionCount === 0) return 0;
        
        return round($this->getTotalAttendanceCount() / $sessionCount, 1);
    }
}
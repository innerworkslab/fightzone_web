<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'day_number',
        'type',
        'video_link',
        'duration',
        'is_active',
    ];

    protected $casts = [
        'course_id' => 'integer',
        'day_number' => 'integer',
        'duration' => 'string',
        'is_active' => 'boolean',
        'type' => 'string',
    ];

    protected $appends = [
        'duration_seconds',
        'formatted_duration',
    ];

    /**
     * Get the course this day belongs to
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get duration in seconds for calculations
     */
    public function getDurationSecondsAttribute()
    {
        if (!$this->duration) {
            return 0;
        }

        // Parse HH:MM:SS format
        $parts = explode(':', $this->duration);
        if (count($parts) === 3) {
            $hours = (int) $parts[0];
            $minutes = (int) $parts[1];
            $seconds = (int) $parts[2];
            return ($hours * 3600) + ($minutes * 60) + $seconds;
        }

        return 0;
    }

    /**
     * Get formatted duration string
     */
    public function getFormattedDurationAttribute()
    {
        $seconds = $this->duration_seconds;

        if ($seconds === 0) {
            return '00:00:00';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    /**
     * Scope for lesson days only
     */
    public function scopeLessons($query)
    {
        return $query->where('type', 'Lesson');
    }

    /**
     * Scope for rest days only
     */
    public function scopeRestDays($query)
    {
        return $query->where('type', 'Rest');
    }
}
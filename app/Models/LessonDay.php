<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_level_id',
        'day_number',
        'type',
        'name',
        'duration',
        'is_active',
    ];

    protected $casts = [
        'course_level_id' => 'integer',
        'day_number' => 'integer',
        'duration' => 'string',
        'type' => 'string',
    ];

    protected $appends = [
        'duration_seconds',
        'formatted_duration',
    ];

    /**
     * Get the course level this lesson day belongs to
     */
    public function courseLevel()
    {
        return $this->belongsTo(CourseLevel::class);
    }

    /**
     * Get the course through course level
     */
    public function course()
    {
        return $this->hasOneThrough(Course::class, CourseLevel::class, 'id', 'id', 'course_level_id', 'course_id');
    }

    public function videos()
    {
        return $this->hasMany(LessonDayVideo::class);
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

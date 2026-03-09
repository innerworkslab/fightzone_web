<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonDayVideo extends Model
{
    //
    protected $fillable = [
        'lesson_day_id',
        'name',
        'description',
        'thumbnail_url',
        'url',
        'duration',
        'type'
    ];

    protected $casts = [
        'lesson_day_id' => 'integer',
        'duration' => 'string',
    ];

    protected $appends = [
        'duration_seconds',
        'formatted_duration',
    ];

    public function lessonDay()
    {
        return $this->belongsTo(LessonDay::class);
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
}

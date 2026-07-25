<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LessonDay;

class CourseLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'description',
        'level',
        'price',
        'is_active',
    ];

    protected $casts = [
        'course_id' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'level' => 'string',
    ];

    /**
     * Get the course this level belongs to
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get total number of lesson days
     */
    public function getTotalLessonDaysCountAttribute()
    {
        return $this->lessonDays()->count();
    }

    /**
     * Get all lesson days for this level
     */
    public function lessonDays()
    {
        return $this->hasMany(LessonDay::class)->orderBy('day_number');
    }

    /**
     * Get lesson days only (not rest days)
     */
    public function lessons()
    {
        return $this->lessonDays()->where('type', 'Lesson');
    }

    /**
     * Get rest days only
     */
    public function restDays()
    {
        return $this->lessonDays()->where('type', 'Rest');
    }

    /**
     * Get total duration of all lesson days
     */
    public function getTotalDurationAttribute()
    {
        return $this->lessonDays()
            ->where('type', 'Lesson')
            ->sum('duration_seconds');
    }

    /**
     * Get total number of days (including rest days)
     */
    public function getTotalDaysAttribute()
    {
        return $this->lessonDays()->count();
    }

    /**
     * Get formatted total duration
     */
    public function getFormattedTotalDurationAttribute()
    {
        $seconds = $this->total_duration;

        if ($seconds === 0) {
            return '00:00:00';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}

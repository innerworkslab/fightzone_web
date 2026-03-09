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
        'name',
        'is_active',
    ];

    protected $casts = [
        'course_level_id' => 'integer',
        'day_number' => 'integer',
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

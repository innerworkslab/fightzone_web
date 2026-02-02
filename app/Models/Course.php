<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'course_category_id',
        'level',
        'price',
        'is_active',
    ];

    protected $casts = [
        'course_category_id' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'level' => 'string',
    ];

    /**
     * Get the course category
     */
    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    /**
     * Get all course days
     */
    public function courseDays()
    {
        return $this->hasMany(CourseDay::class)->orderBy('day_number');
    }

    /**
     * Get active course days only
     */
    public function activeCourseDays()
    {
        return $this->courseDays()->where('is_active', true);
    }

    /**
     * Get total duration of all lesson days
     */
    public function getTotalDurationAttribute()
    {
        return $this->courseDays()
            ->where('type', 'Lesson')
            ->sum('duration_seconds');
    }

    /**
     * Get total number of lesson days
     */
    public function getLessonDaysCountAttribute()
    {
        return $this->courseDays()
            ->where('type', 'Lesson')
            ->count();
    }

    /**
     * Get total number of days (including rest days)
     */
    public function getTotalDaysAttribute()
    {
        return $this->courseDays()->count();
    }
}
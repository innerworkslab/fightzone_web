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
        'is_active',
    ];

    protected $casts = [
        'course_category_id' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the course category
     */
    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    /**
     * Get all course levels
     */
    public function courseLevels()
    {
        return $this->hasMany(CourseLevel::class);
    }

    /**
     * Get active course levels only
     */
    public function activeCourseLevels()
    {
        return $this->courseLevels()->where('is_active', true);
    }

    /**
     * Get course level by level name
     */
    public function getLevel($level)
    {
        return $this->courseLevels()->where('level', ucfirst(strtolower($level)))->first();
    }

    /**
     * Get beginner level
     */
    public function beginnerLevel()
    {
        return $this->courseLevels()->where('level', 'Beginner')->first();
    }

    /**
     * Get intermediate level
     */
    public function intermediateLevel()
    {
        return $this->courseLevels()->where('level', 'Intermediate')->first();
    }

    /**
     * Get expert level
     */
    public function expertLevel()
    {
        return $this->courseLevels()->where('level', 'Expert')->first();
    }
}
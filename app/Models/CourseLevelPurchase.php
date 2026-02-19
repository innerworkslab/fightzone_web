<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLevelPurchase extends Model
{
    protected $fillable = [
        'user_id',
        'course_level_id',
        'purchase_id',
        'valid_from',
        'valid_until',
        'finished_lesson_days_count',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'course_level_id' => 'integer',
        'purchase_id' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'finished_lesson_days_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courseLevel()
    {
        return $this->belongsTo(CourseLevel::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function lessonDayCompletions()
    {
        return $this->hasMany(LessonDayCompletion::class);
    }

    public function lessonDayVideoCompletions()
    {
        return $this->hasMany(LessonDayVideoCompletion::class);
    }
}

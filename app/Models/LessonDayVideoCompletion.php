<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonDayVideoCompletion extends Model
{
    protected $fillable = [
        'course_level_purchase_id',
        'lesson_day_video_id',
        'completed_at',
    ];

    protected $casts = [
        'course_level_purchase_id' => 'integer',
        'lesson_day_video_id' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function courseLevelPurchase()
    {
        return $this->belongsTo(CourseLevelPurchase::class);
    }

    public function lessonDayVideo()
    {
        return $this->belongsTo(LessonDayVideo::class);
    }
}

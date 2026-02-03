<?php

namespace App\Services;

use App\Models\CourseLevel;

use App\Repositories\LessonDay\LessonDayRepositoryInterface;

class LessonDayService
{
    public function __construct(protected LessonDayRepositoryInterface $repo){}

    public function create(array $data)
    {
        $courseLevel = CourseLevel::find($data['course_level_id']);
        if (!$courseLevel) {
            throw new \RuntimeException('Course level not found');
        }

        if($courseLevel->lessonDays()->where('day_number', $data['day_number'])->exists()) {
            throw new \RuntimeException('Lesson day already exists');
        }

        return $this->repo->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repo->update($id, $data);
    }
}

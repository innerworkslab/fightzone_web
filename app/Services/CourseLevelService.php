<?php

namespace App\Services;

use App\Models\Course;

use App\Repositories\CourseLevel\CourseLevelRepositoryInterface;

class CourseLevelService
{
    public function __construct(protected CourseLevelRepositoryInterface $levelRepo)
    {

    }

    public function all(bool $onlyActive = true, ?array $filters = [], ?int $page = null, ?int $limit = null)
    {
        $query = $this->levelRepo->all($onlyActive, $filters, $limit);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function find($id)
    {
        return $this->levelRepo->find($id);
    }

    public function create(array $data)
    {
        $course = Course::find($data['course_id']);
        if (!$course) {
            throw new \RuntimeException('Course not found');
        }

        if($course->courseLevels()->where('level', $data['level'])->exists()) {
            throw new \RuntimeException('Course level already exists');
        }

        return $this->levelRepo->create($data);
    }

    public function update($id, array $data)
    {
        $course = Course::find($data['course_id']);
        if (!$course) {
            throw new \RuntimeException('Course not found');
        }

        if($course->courseLevels()->where('id', '<>', $id)->where('level', $data['level'])->exists()) {
            throw new \RuntimeException('Course level already exists');
        }

        $item = $this->find($id);
        if (!$item) {
            throw new \RuntimeException('Course level not found');
        }

        return $this->levelRepo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->levelRepo->delete($id);
    }

    public function toggleActive($id)
    {
        return $this->levelRepo->toggleActive($id);
    }

    public function getLessons(int $courseId, $levelId)
    {
        return $this->levelRepo->getLessonDays($courseId, $levelId);
    }
}

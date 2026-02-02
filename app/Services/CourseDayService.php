<?php

namespace App\Services;

use App\Repositories\CourseDay\CourseDayRepositoryInterface;
use App\Models\Course;

class CourseDayService
{
    public function __construct(protected CourseDayRepositoryInterface $repo)
    {

    }

    public function all(?array $filters = [], ?int $page = null, ?int $limit = null)
    {
        $query = $this->repo->all($filters, $limit);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function findByCourse($courseId, ?int $page = null, ?int $limit = null)
    {
        $query = $this->repo->findByCourse($courseId, $limit);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function create(array $data)
    {
        // Validate that the course exists
        $course = Course::find($data['course_id']);
        if (!$course) {
            throw new \RuntimeException('Course not found');
        }

        // Auto-assign day number if not provided
        if (!isset($data['day_number'])) {
            $maxDayNumber = $course->courseDays()->max('day_number') ?? 0;
            $data['day_number'] = $maxDayNumber + 1;
        } else {
            // Validate day number uniqueness for this course
            $existingDay = $this->repo->getByDayNumber($data['course_id'], $data['day_number']);
            if ($existingDay) {
                throw new \RuntimeException("Day number {$data['day_number']} already exists for this course");
            }
        }

        return $this->repo->create($data);
    }

    public function update($id, array $data)
    {
        $courseDay = $this->repo->find($id);
        if (!$courseDay) {
            throw new \RuntimeException('Course day not found');
        }

        // If changing day number, validate uniqueness
        if (isset($data['day_number']) && $data['day_number'] != $courseDay->day_number) {
            $existingDay = $this->repo->getByDayNumber($courseDay->course_id, $data['day_number']);
            if ($existingDay && $existingDay->id != $id) {
                throw new \RuntimeException("Day number {$data['day_number']} already exists for this course");
            }
        }

        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }

    public function toggleActive($id)
    {
        return $this->repo->toggleActive($id);
    }

    public function reorderDays($courseId, array $dayOrder)
    {
        // Validate that all days belong to the course
        foreach ($dayOrder as $dayId) {
            $day = $this->repo->find($dayId);
            if (!$day || $day->course_id != $courseId) {
                throw new \RuntimeException("Day ID {$dayId} does not belong to course {$courseId}");
            }
        }

        return $this->repo->reorderDays($courseId, $dayOrder);
    }

    public function getByDayNumber($courseId, $dayNumber)
    {
        return $this->repo->getByDayNumber($courseId, $dayNumber);
    }

    /**
     * Get course days summary for a course
     */
    public function getCourseSummary($courseId)
    {
        $days = $this->repo->findByCourse($courseId)->get();

        return [
            'total_days' => $days->count(),
            'lesson_days' => $days->where('type', 'Lesson')->count(),
            'rest_days' => $days->where('type', 'Rest')->count(),
            'total_duration_seconds' => $days->where('type', 'Lesson')->sum('duration_seconds'),
            'days' => $days
        ];
    }
}
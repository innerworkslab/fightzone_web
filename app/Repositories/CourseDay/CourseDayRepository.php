<?php

namespace App\Repositories\CourseDay;

use App\Models\CourseDay;

class CourseDayRepository implements CourseDayRepositoryInterface
{
    public function all(?array $filters = [], ?int $limit = null)
    {
        $query = CourseDay::query()
            ->with('course.category')
            ->orderBy('course_id')
            ->orderBy('day_number');

        $normalized = [];
        foreach ($filters ?? [] as $k => $v) {
            if (is_int($k) && is_array($v)) {
                foreach ($v as $fk => $fv) {
                    $normalized[$fk] = $fv;
                }
            } elseif (is_string($k)) {
                $normalized[$k] = $v;
            }
        }

        foreach ($normalized as $key => $value) {
            if ($key === 'course_name') {
                $query->whereHas('course', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            } elseif ($key === 'category_name') {
                $query->whereHas('course.category', function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%");
                });
            } else {
                $query->where($key, 'like', "%{$value}%");
            }
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function find($id)
    {
        return CourseDay::with('course.category')->find($id);
    }

    public function findByCourse($courseId, ?int $limit = null)
    {
        $query = CourseDay::where('course_id', $courseId)
            ->with('course.category')
            ->orderBy('day_number');

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function create(array $data): CourseDay
    {
        return CourseDay::create($data);
    }

    public function update($id, array $data): CourseDay
    {
        $courseDay = $this->find($id);
        if (!$courseDay) {
            throw new \RuntimeException('Course day not found');
        }

        $courseDay->fill($data);
        $courseDay->save();
        return $courseDay;
    }

    public function delete($id)
    {
        $courseDay = $this->find($id);
        if (!$courseDay) {
            throw new \RuntimeException('Course day not found');
        }

        return $courseDay->delete();
    }

    public function toggleActive($id): CourseDay
    {
        $courseDay = $this->find($id);
        if (!$courseDay) {
            throw new \RuntimeException('Course day not found');
        }

        $courseDay->is_active = !$courseDay->is_active;
        $courseDay->save();
        return $courseDay;
    }

    public function getByDayNumber($courseId, $dayNumber)
    {
        return CourseDay::where('course_id', $courseId)
            ->where('day_number', $dayNumber)
            ->with('course.category')
            ->first();
    }

    public function reorderDays($courseId, array $dayOrder)
    {
        $updatedDays = [];
        foreach ($dayOrder as $newDayNumber => $dayId) {
            $day = $this->find($dayId);
            if ($day && $day->course_id == $courseId) {
                $day->day_number = $newDayNumber + 1; // Convert to 1-based indexing
                $day->save();
                $updatedDays[] = $day;
            }
        }
        return $updatedDays;
    }
}
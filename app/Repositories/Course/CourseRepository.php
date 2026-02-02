<?php

namespace App\Repositories\Course;

use App\Models\Course;

class CourseRepository implements CourseRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null)
    {
        $query = Course::query()
            ->with('category')
            ->orderBy('name');

        if ($onlyActive) {
            $query->where('is_active', true);
        }

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
            if ($key === 'category_name') {
                $query->whereHas('category', function ($q) use ($value) {
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
        return Course::find($id);
    }

    public function findWithDetails($id)
    {
        return Course::with(['category', 'courseDays' => function ($query) {
            $query->orderBy('day_number');
        }])->find($id);
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }

    public function update($id, array $data): Course
    {
        $course = $this->find($id);
        if (!$course) {
            throw new \RuntimeException('Course not found');
        }

        $course->fill($data);
        $course->save();
        return $course;
    }

    public function delete($id)
    {
        $course = $this->find($id);
        if (!$course) {
            throw new \RuntimeException('Course not found');
        }

        return $course->delete();
    }

    public function toggleActive($id): Course
    {
        $course = $this->find($id);
        if (!$course) {
            throw new \RuntimeException('Course not found');
        }

        $course->is_active = !$course->is_active;
        $course->save();
        return $course;
    }

    public function getByCategory($categoryId, bool $onlyActive = true)
    {
        $query = Course::where('course_category_id', $categoryId)
            ->with('category')
            ->orderBy('name');

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        return $query;
    }

    public function getByLevel($level, bool $onlyActive = true)
    {
        $query = Course::where('level', $level)
            ->with('category')
            ->orderBy('name');

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        return $query;
    }
}
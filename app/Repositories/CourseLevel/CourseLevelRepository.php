<?php

namespace App\Repositories\CourseLevel;

use App\Models\CourseLevel;

class CourseLevelRepository implements CourseLevelRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null)
    {
        $query = CourseLevel::query()
        // ->with(['course'])
        ->orderBy('level');

        if($onlyActive) {
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
            $query->where($key, 'like', "%{$value}%");
        }
        if ($limit) {
            $query->limit($limit);
        }
        return $query;
    }

    public function find($id)
    {
        return CourseLevel::with(['course','lessonDays'])->find($id);
    }

    public function create(array $data): CourseLevel
    {
        return CourseLevel::create($data);
    }

    public function update($id, array $data): CourseLevel
    {
        $courseLevel = $this->find($id);
        if (!$courseLevel) {
            throw new \RuntimeException('Course level not found');
        }
        $courseLevel->fill($data);
        $courseLevel->save();
        return $courseLevel;
    }

    public function delete($id)
    {
        $courseLevel = $this->find($id);
        if (!$courseLevel) {
            throw new \RuntimeException('Course level not found');
        }
        return $courseLevel->delete();
    }

    public function toggleActive($id): CourseLevel
    {
        $courseLevel = $this->find($id);
        if (!$courseLevel) {
            throw new \RuntimeException('Course level not found');
        }
        $courseLevel->is_active = !$courseLevel->is_active;
        $courseLevel->save();
        return $courseLevel;
    }

    public function getLessonDays(int $courseId, int $levelId)
    {
        return CourseLevel::with('lessonDays')->find($levelId);
    }
}

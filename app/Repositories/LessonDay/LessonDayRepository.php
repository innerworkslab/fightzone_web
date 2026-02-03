<?php

namespace App\Repositories\LessonDay;

use App\Models\LessonDay;

class LessonDayRepository implements LessonDayRepositoryInterface
{
    public function all(?array $filters = [], ?int $limit = null)
    {
        $query = LessonDay::query()->orderBy('day_number');

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
        return LessonDay::find($id);
    }

    public function create(array $data): LessonDay
    {
        return LessonDay::create($data);
    }

    public function update($id, array $data): LessonDay
    {
        $lessonDay = $this->find($id);
        if (!$lessonDay) {
            throw new \RuntimeException('Lesson day not found');
        }
        $lessonDay->fill($data);
        $lessonDay->save();
        return $lessonDay;
    }

    public function delete($id)
    {
        $lessonDay = $this->find($id);
        if (!$lessonDay) {
            throw new \RuntimeException('Lesson day not found');
        }
        return $lessonDay->delete();
    }

    public function toggleActive($id): LessonDay
    {
        $lessonDay = $this->find($id);
        if (!$lessonDay) {
            throw new \RuntimeException('Lesson day not found');
        }
        $lessonDay->is_active = !$lessonDay->is_active;
        $lessonDay->save();
        return $lessonDay;
    }
}

<?php

namespace App\Repositories\LessonDay;

use App\Models\LessonDay;
use App\Models\LessonDayVideo;

class LessonDayRepository implements LessonDayRepositoryInterface
{
    public function all(?array $filters = [], ?int $limit = null)
    {
        $query = LessonDay::query()->withCount('videos')->orderBy('day_number');

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
        return LessonDay::with('videos')->find($id);
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

    public function attachVideoToLessonDay(int $lessonDayId, array $data)
    {
        $lessonDay = $this->find($lessonDayId);
        if (!$lessonDay) {
            throw new \RuntimeException('Lesson day not found');
        }
        $data['lesson_day_id'] = $lessonDayId;
        return LessonDayVideo::updateOrCreate([
            'lesson_day_id' => $data['lesson_day_id'],
            'url' => $data['url']
        ], $data);
    }

    public function findLessonDayVideo(int $id)
    {
        return LessonDayVideo::find($id);
    }

    public function updateLessonDayVideo(int $id, array $data)
    {
        return $this->findLessonDayVideo($id)->update($data);
    }
}

<?php

namespace App\Repositories\LessonDay;

use App\Models\LessonDay;

interface LessonDayRepositoryInterface
{
    public function all(?array $filters = [], ?int $limit = null);

    public function find($id);

    public function create(array $data): LessonDay;

    public function update($id, array $data): LessonDay;

    public function delete($id);

    public function attachVideoToLessonDay(int $lessonDayId, array $data);

    public function findLessonDayVideo(int $id);

    public function updateLessonDayVideo(int $id, array $data);

    public function deleteLessonDayVideo(int $id);
}

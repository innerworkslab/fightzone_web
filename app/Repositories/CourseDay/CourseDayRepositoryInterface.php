<?php

namespace App\Repositories\CourseDay;

use App\Models\CourseDay;

interface CourseDayRepositoryInterface
{
    public function all(?array $filters = [], ?int $limit = null);

    public function find($id);

    public function findByCourse($courseId, ?int $limit = null);

    public function create(array $data): CourseDay;

    public function update($id, array $data): CourseDay;

    public function delete($id);

    public function toggleActive($id): CourseDay;

    public function getByDayNumber($courseId, $dayNumber);

    public function reorderDays($courseId, array $dayOrder);
}
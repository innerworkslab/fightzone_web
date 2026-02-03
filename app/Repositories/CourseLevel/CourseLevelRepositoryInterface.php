<?php

namespace App\Repositories\CourseLevel;

use App\Models\CourseLevel;

interface CourseLevelRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null);

    public function find($id);

    public function create(array $data): CourseLevel;

    public function update($id, array $data): CourseLevel;

    public function delete($id);

    public function toggleActive($id): CourseLevel;

    public function getLessonDays(int $courseId, int $levelId);
}

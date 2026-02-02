<?php

namespace App\Repositories\Course;

use App\Models\Course;

interface CourseRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null);

    public function find($id);

    public function findWithDetails($id);

    public function create(array $data): Course;

    public function update($id, array $data): Course;

    public function delete($id);

    public function toggleActive($id): Course;

    public function getByCategory($categoryId, bool $onlyActive = true);

    public function getByLevel($level, bool $onlyActive = true);
}
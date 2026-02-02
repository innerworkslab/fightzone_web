<?php

namespace App\Repositories\CourseCategory;

use App\Models\CourseCategory;

interface CourseCategoryRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null);

    public function find($id);

    public function create(array $data): CourseCategory;

    public function update($id, array $data): CourseCategory;

    public function delete($id);

    public function toggleActive($id): CourseCategory;
}
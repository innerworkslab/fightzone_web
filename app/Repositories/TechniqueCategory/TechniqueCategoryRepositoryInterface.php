<?php

namespace App\Repositories\TechniqueCategory;

use App\Models\TechniqueCategory;

interface TechniqueCategoryRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null);

    public function find($id);

    public function create(array $data): TechniqueCategory;

    public function update($id, array $data): TechniqueCategory;

    public function delete($id);

    public function toggleActive($id): TechniqueCategory;
}

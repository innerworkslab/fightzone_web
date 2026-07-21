<?php

namespace App\Repositories\Technique;

use App\Models\Technique;

interface TechniqueRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null);

    public function find($id);

    public function create(array $data): Technique;

    public function update($id, array $data): Technique;

    public function delete($id);

    public function toggleActive($id): Technique;
}

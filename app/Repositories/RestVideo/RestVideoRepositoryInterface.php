<?php

namespace App\Repositories\RestVideo;

use App\Models\RestVideo;

interface RestVideoRepositoryInterface
{
    public function all(bool $onlyActive = true, ?array $filters = [], ?int $limit = null);

    public function find($id);

    public function create(array $data): RestVideo;

    public function update($id, array $data): RestVideo;

    public function delete($id);

    public function toggleActive($id): RestVideo;
}

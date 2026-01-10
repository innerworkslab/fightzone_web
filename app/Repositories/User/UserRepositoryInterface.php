<?php

namespace App\Repositories\User;

interface UserRepositoryInterface
{
    public function all(?array $filters = [], ?int $limit = null);

    public function find($id);

    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);

    public function toggleActive($id);
}

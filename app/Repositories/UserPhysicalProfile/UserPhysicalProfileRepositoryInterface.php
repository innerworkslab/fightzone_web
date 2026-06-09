<?php

namespace App\Repositories\UserPhysicalProfile;

use App\Models\UserPhysicalProfile;

interface UserPhysicalProfileRepositoryInterface
{
    public function find($id);

    public function findByUserId($userId);

    public function create(array $data): UserPhysicalProfile;

    public function update($id, array $data): UserPhysicalProfile;

    public function delete($id);
}

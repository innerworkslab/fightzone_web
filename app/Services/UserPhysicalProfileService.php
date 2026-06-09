<?php

namespace App\Services;

use App\Repositories\UserPhysicalProfile\UserPhysicalProfileRepositoryInterface;

class UserPhysicalProfileService
{
    public function __construct(protected UserPhysicalProfileRepositoryInterface $repo)
    {

    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function findByUserId($userId)
    {
        return $this->repo->findByUserId($userId);
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }
}

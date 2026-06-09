<?php

namespace App\Repositories\UserPhysicalProfile;

use App\Models\UserPhysicalProfile;

class UserPhysicalProfileRepository implements UserPhysicalProfileRepositoryInterface
{
    public function find($id)
    {
        return UserPhysicalProfile::find($id);
    }

    public function findByUserId($userId)
    {
        return UserPhysicalProfile::where('user_id', $userId)->first();
    }

    public function create(array $data): UserPhysicalProfile
    {
        return UserPhysicalProfile::create($data);
    }

    public function update($id, array $data): UserPhysicalProfile
    {
        $model = $this->find($id);
        if (!$model) {
            throw new \RuntimeException('User physical profile not found');
        }
        $model->fill($data);
        $model->save();
        return $model;
    }

    public function delete($id)
    {
        $model = $this->find($id);
        if (!$model) {
            throw new \RuntimeException('User physical profile not found');
        }
        return $model->delete();
    }
}

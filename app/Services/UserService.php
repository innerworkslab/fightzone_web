<?php

namespace App\Services;

use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(protected UserRepositoryInterface $repo){}

    public function all(?array $filters = [], ?int $page = null, ?int $limit = null)
    {
        $query = $this->repo->all($filters, $limit);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function create(array $data)
    {
        if (! isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        if(isset($data['is_verified'])){
            $data['email_verified_at'] = now();
        }

        return $this->repo->create($data);
    }

    public function update($id, array $data)
    {
        if (! isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        if(isset($data['is_verified'])){
            $data['email_verified_at'] = now();
        }
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }

    public function toggleActive($id)
    {
        return $this->repo->toggleActive($id);
    }
}

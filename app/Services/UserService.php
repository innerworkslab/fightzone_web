<?php

namespace App\Services;

use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $repo,
        protected RegistrationPointBalanceService $registrationPointBalanceService
    ){}

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

        if(array_key_exists('is_verified', $data)){
            $data['email_verified_at'] = filter_var($data['is_verified'], FILTER_VALIDATE_BOOLEAN) ? now() : null;
        }

        $user = $this->repo->create($data);

        if ($user->is_verified) {
            $this->registrationPointBalanceService->award($user);
        }

        return $user;
    }

    public function update($id, array $data)
    {
        $item = $this->repo->find($id);
        if (! $item) {
            return null;
        }

        $wasVerified = (bool) $item->is_verified;

        if (! isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        if(array_key_exists('is_verified', $data)){
            $data['email_verified_at'] = filter_var($data['is_verified'], FILTER_VALIDATE_BOOLEAN) ? now() : null;
        }

        $user = $this->repo->update($id, $data);

        if (! $wasVerified && $user?->is_verified) {
            $this->registrationPointBalanceService->award($user);
        }

        return $user;
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

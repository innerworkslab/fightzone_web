<?php

namespace App\Services;

use App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface;

class PaymentMethodService
{
    public function __construct(protected PaymentMethodRepositoryInterface $repo){}

    public function all(bool $onlyActive=true, ?array $filters = [], ?int $page, ?int $limit)
    {
        $query = $this->repo->all($onlyActive, $filters, $limit);

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

    public function toggleActive($id)
    {
        return $this->repo->toggleActive($id);
    }
}

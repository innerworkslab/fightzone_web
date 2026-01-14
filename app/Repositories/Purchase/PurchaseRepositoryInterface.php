<?php

namespace App\Repositories\Purchase;

use App\Models\Purchase;

interface PurchaseRepositoryInterface
{
    public function all(?array $filters=[], ?string $status = null, ?int $limit = null);

    public function listForUser(int $userId, ?int $limit = null);

    public function create(array $data): Purchase;

    public function findForUpdate(int $id): ?Purchase;

    public function findById(int $id): ?Purchase;

    public function update(Purchase $purchase, array $data): Purchase;
}

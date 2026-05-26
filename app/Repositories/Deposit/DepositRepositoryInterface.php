<?php

namespace App\Repositories\Deposit;

use App\Models\Deposit;

interface DepositRepositoryInterface
{
    public function all(?array $filters=[], ?string $status = null, ?int $limit = null);

    public function listForUser(int $userId, ?int $limit = null);

    public function create(array $data): ?Deposit;

    public function findForUpdate(int $id): ?Deposit;

    public function findById(int $id): ?Deposit;

    public function update(Deposit $deposit, array $data): Deposit;
}

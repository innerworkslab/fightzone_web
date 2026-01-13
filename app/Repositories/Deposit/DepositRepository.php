<?php

namespace App\Repositories\Deposit;

use App\Models\Deposit;

class DepositRepository implements DepositRepositoryInterface
{
    public function all(?array $filters=[], ?string $status = null, ?int $limit = null)
    {
        $query = Deposit::query()->with('user')->orderBy('id','desc');

        $normalized = [];
        foreach ($filters ?? [] as $k => $v) {
            if (is_int($k) && is_array($v)) {
                foreach ($v as $fk => $fv) {
                    $normalized[$fk] = $fv;
                }
            } elseif (is_string($k)) {
                $normalized[$k] = $v;
            }
        }

        foreach ($normalized as $key => $value) {
            $query->where($key, 'like', "%{$value}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function listForUser(int $userId, ?int $limit = null)
    {
        $query = Deposit::orderBy('id', 'desc')
        ->where('user_id',$userId);

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function create(array $data): Deposit
    {
        return Deposit::create($data);
    }

    public function findForUpdate(int $id): ?Deposit
    {
        return Deposit::where('id', $id)->lockForUpdate()->first();
    }

    public function findById(int $id): ?Deposit
    {
        return Deposit::with([
            'user',
            'paymentMethod',
            'admin'
        ])->find($id);
    }

    public function update(Deposit $deposit, array $data): Deposit
    {
        $deposit->fill($data);
        $deposit->save();
        return $deposit;
    }
}

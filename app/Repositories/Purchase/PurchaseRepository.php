<?php

namespace App\Repositories\Purchase;

use App\Models\Purchase;

class PurchaseRepository implements PurchaseRepositoryInterface
{
    public function all(?array $filters=[], ?string $status = null, ?int $limit = null)
    {
        $query = Purchase::query()
            ->with(['user', 'purchasable'])
            ->orderBy('id','desc');

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
        $query = Purchase::with(['purchasable'])
        ->orderBy('id', 'desc')
        ->where('user_id', $userId);

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function create(array $data): Purchase
    {
        return Purchase::create($data);
    }

    public function findForUpdate(int $id): ?Purchase
    {
        return Purchase::where('id', $id)->lockForUpdate()->first();
    }

    public function findById(int $id): ?Purchase
    {
        return Purchase::with([
            'user',
            'purchasable',
            'admin'
        ])->find($id);
    }

    public function update(Purchase $purchase, array $data): Purchase
    {
        $purchase->fill($data);
        $purchase->save();
        return $purchase;
    }
}

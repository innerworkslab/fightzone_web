<?php

namespace App\Services;

use App\Models\PackagePurchase;
use App\Repositories\Package\PackageRepositoryInterface;

class PackageService
{
    public function __construct(protected PackageRepositoryInterface $repo){}

    public function all(bool $onlyActive=true, ?array $filters = [], ?int $page = null, ?int $limit = null)
    {
        $query = $this->repo->all($onlyActive, $filters, $limit);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function allForUser(int $userId, bool $onlyActive=true, ?array $filters = [], ?int $page = null, ?int $limit = null)
    {
        $query = $this->repo->all($onlyActive, $filters, $limit);

        if ($page) {
            $packages = $query->paginate($limit ?? config('common.list_count'));
            $packages->setCollection(
                $this->withUserPurchaseValidity($packages->getCollection(), $userId)
            );

            return $packages;
        }

        return $this->withUserPurchaseValidity($query->get(), $userId);
    }

    private function withUserPurchaseValidity($packages, int $userId)
    {
        $packageIds = $packages->pluck('id')->all();

        if (empty($packageIds)) {
            return $packages;
        }

        $purchasesByPackage = PackagePurchase::where('user_id', $userId)
            ->whereIn('package_id', $packageIds)
            ->orderByDesc('valid_until')
            ->orderByDesc('id')
            ->get()
            ->unique('package_id')
            ->keyBy('package_id');

        $now = now();

        return $packages->transform(function ($package) use ($purchasesByPackage, $now) {
            $packagePurchase = $purchasesByPackage->get($package->id);

            $package->valid_from = $packagePurchase?->valid_from;
            $package->valid_until = $packagePurchase?->valid_until;
            $package->is_within_validity = false;

            if ($packagePurchase && $packagePurchase->valid_from && $packagePurchase->valid_until) {
                $package->is_within_validity = $now->between($packagePurchase->valid_from, $packagePurchase->valid_until);
            }

            return $package;
        });
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

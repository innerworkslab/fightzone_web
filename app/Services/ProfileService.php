<?php

namespace App\Services;

use App\Repositories\Profile\ProfileRepositoryInterface;

class ProfileService
{
    public function __construct(protected ProfileRepositoryInterface $repo)
    {

    }

    /**
     * Get complete user profile with balance, deposits, and purchases
     */
    public function getProfile(int $userId)
    {
        $user = $this->repo->getUserProfile($userId);

        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        return $user;
    }

    /**
     * Get user profile details only
     */
    public function getProfileDetails(int $userId)
    {

        $user = $this->repo->getUserProfile($userId);

        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        $pointBalance = $this->repo->getUserPointBalance($userId);
        $deposits = $this->repo->getUserDeposits($userId, 10)->get();
        $purchases = $this->repo->getUserPurchases($userId, 10)->get();

        return [
            'user' => $user,
            'point_balance' => $pointBalance ? $pointBalance->points : 0,
            'recent_deposits' => $deposits,
            'recent_purchases' => $purchases,
        ];
    }

    /**
     * Get user point balance
     */
    public function getPointBalance(int $userId)
    {
        $balance = $this->repo->getUserPointBalance($userId);

        return [
            'points' => $balance ? $balance->points : 0,
        ];
    }

    /**
     * Get user deposit history
     */
    public function getDepositHistory(int $userId, ?int $page = null, ?int $limit = null)
    {
        $query = $this->repo->getUserDeposits($userId, $limit);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    /**
     * Get user purchase history
     */
    public function getPurchaseHistory(int $userId, ?int $page = null, ?int $limit = null)
    {
        $query = $this->repo->getUserPurchases($userId, $limit);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }
}

<?php

namespace App\Repositories\Profile;

use App\Models\User;

interface ProfileRepositoryInterface
{
    public function getUserProfile(int $userId): ?User;

    public function getUserPointBalance(int $userId);

    public function getUserDeposits(int $userId, ?int $limit = null);

    public function getUserPurchases(int $userId, ?int $limit = null);
}

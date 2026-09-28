<?php

namespace App\Repositories\Profile;

use App\Models\User;
use App\Models\PointBalance;
use App\Models\Deposit;
use App\Models\Purchase;
use App\Models\CourseLevel;

class ProfileRepository implements ProfileRepositoryInterface
{
    public function getUserProfile(int $userId): ?User
    {
        return User::find($userId);
    }

    public function getUserPointBalance(int $userId)
    {
        return PointBalance::where('user_id', $userId)->first();
    }

    public function getUserDeposits(int $userId, ?int $limit = null)
    {
        $query = Deposit::where('user_id', $userId)
            ->with(['paymentMethod', 'admin'])
            ->orderBy('id', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function getUserPurchases(int $userId, ?int $limit = null)
    {
        $query = Purchase::where('user_id', $userId)
            ->with(['purchasable', 'admin', 'courseLevelPurchase', 'packagePurchase'])
            ->where(function ($query) {
                $query->whereNotIn('purchasable_type', ['course_level', CourseLevel::class])
                    ->orWhereHasMorph('purchasable', [CourseLevel::class], function ($query) {
                        $query->where('is_active', true);
                    });
            })
            ->orderBy('id', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }
}

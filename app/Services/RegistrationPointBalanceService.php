<?php

namespace App\Services;

use App\Models\PointBalance;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegistrationPointBalanceService
{
    public const STARTING_POINTS = 10000000;
    public const TRANSACTION_TYPE = 'registration_bonus';

    public function award(User|int $user): PointBalance
    {
        $userId = $user instanceof User ? (int) $user->id : $user;

        return DB::transaction(function () use ($user, $userId) {
            $existingBalance = PointBalance::where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            $alreadyAwarded = PointTransaction::where('user_id', $userId)
                ->where('type', self::TRANSACTION_TYPE)
                ->exists();

            if ($alreadyAwarded) {
                return $existingBalance ?? PointBalance::create([
                    'user_id' => $userId,
                    'points' => 0,
                ]);
            }

            return PointBalance::adjustPointsForUser(
                $userId,
                self::STARTING_POINTS,
                $user instanceof User ? $user : null,
                null,
                self::TRANSACTION_TYPE,
                'Registration confirmed starting balance'
            );
        });
    }
}

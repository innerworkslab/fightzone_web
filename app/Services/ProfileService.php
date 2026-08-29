<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Repositories\Profile\ProfileRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;

use App\Models\CourseLevelPurchase;

class ProfileService
{
    public function __construct(
        protected ProfileRepositoryInterface $user,
        protected UserRepositoryInterface $users
    )
    {

    }

    /**
     * Get complete user profile with balance, deposits, and purchases
     */
    public function getProfile(int $userId)
    {
        $user = $this->user->getUserProfile($userId);

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

        $user = $this->user->getUserProfile($userId);

        if (!$user) {
            throw new \RuntimeException('User not found');
        }

        $pointBalance = $this->user->getUserPointBalance($userId);
        $deposits = $this->user->getUserDeposits($userId, 10)->get();
        $purchases = $this->user->getUserPurchases($userId, 10)->get();

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
        $balance = $this->user->getUserPointBalance($userId);

        return [
            'points' => $balance ? $balance->points : 0,
        ];
    }

    /**
     * Get user deposit history
     */
    public function getDepositHistory(int $userId, ?int $page = null, ?int $limit = null)
    {
        $query = $this->user->getUserDeposits($userId, $limit);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    /**
     * Get user purchase history
     */
    public function getPurchaseHistory(int $userId, ?int $page = null, ?int $limit = null)
    {
        $query = $this->user->getUserPurchases($userId, $limit);

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function updatePassword(int $userId, $oldPassword, $newPassword)
    {
        $user = $this->user->getUserProfile($userId);
        if(Hash::check($oldPassword, $user->getAuthPassword())){
            $user->password = $newPassword;
            $user->save();
            return true;
        }
        return false;
    }

    public function updatePhoneNumber(int $userId, string $password, string $newPhoneNumber)
    {
        $user = $this->user->getUserProfile($userId);
        if(Hash::check($password, $user->getAuthPassword())){
            $user->phone_number = $newPhoneNumber;
            $user->save();
            return true;
        }
        return false;
    }

    public function updateProfileName(int $userId, string $password, string $newName)
    {
        $user = $this->user->getUserProfile($userId);
        if(Hash::check($password, $user->getAuthPassword())){
            $user->name = $newName;
            $user->save();
            return true;
        }
        return false;
    }

    /**
     * Revoke every device session before soft-deleting the account.
     */
    public function deleteAccount(User $user, string $currentPassword): bool
    {
        if (! Hash::check($currentPassword, $user->getAuthPassword())) {
            return false;
        }

        DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            $user->delete();
        });

        return true;
    }

    public function isPhoneNumberTaken(int $userId, string $phoneNumber)
    {
        return $this->users->checkSamePhoneNumberExistence($userId, $phoneNumber);
    }

    public function fetchUserPurchasedCourseLevels(int $userId, ?int $page = null, ?int $limit = null)
    {
        $query = CourseLevelPurchase::with([
            'courseLevel.course.category',            
        ])
        ->where('user_id', $userId)
        ->orderByDesc('id');

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }
}

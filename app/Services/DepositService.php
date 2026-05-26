<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Carbon\Carbon;

use App\Models\PointBalance;
use App\Models\Admin;
use App\Models\User;
use App\Models\Deposit;

use App\Services\ThirdParty\Firebase\FirebaseNotificationService;

use App\Repositories\Deposit\DepositRepositoryInterface;

class DepositService
{
    public function __construct(protected DepositRepositoryInterface $repo)
    {

    }

    public function all(?array $filters = [], ?string $status, ?int $page, ?int $limit)
    {
        $query = $this->repo->all(
            $filters,
            $status,
            $limit
        );

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function listForUser(int $userId,?int $page, ?int $limit)
    {
        $query = $this->repo->listForUser(
            $userId,
            $limit
        );

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function createDeposit(int $userId, int $paymentMethodId, float $amount)
    {
        $trId = Str::random(8);
        $deposit = $this->repo->create([
            'user_id' => $userId,
            'payment_method_id' => $paymentMethodId,
            'transaction_id' => $trId,
            'amount' => $amount,
        ]);

        $time = now()->format('Y-m-d H:i');
        if($deposit){
            (new FirebaseNotificationService($deposit, \App\Models\Admin::all(), $deposit->user_id, 'user'))
            ->send([
                'title' => 'New user deposit',
                'preview' => "User {$deposit->user->name} deposited {$deposit->amount} on {$time}."
            ]);
            return $deposit;
        }else{
            $user = \App\Models\User::find($userId);
            (new FirebaseNotificationService(null, \App\Models\Admin::all(), $userId, 'user'))
            ->send([
                'title' => 'New user deposit failed',
                'preview' => "User {$user->name} deposit of amount {$amount} failed on {$time}."
            ]);
            return null;
        }
    }

    public function updateDeposit(int $id, array $data)
    {
        $deposit = $this->repo->findById($id);
        if(!$deposit){
            throw new \RuntimeException('Deposit not found');
        }
        return $this->repo->update($deposit, $data);
    }

    public function detail(int $id)
    {
        $deposit = $this->repo->findById($id);
        if(!$deposit){
            throw new \RuntimeException('Deposit not found');
        }
        return $deposit;
    }

    public function confirmDeposit(Admin $admin, int $depositId): Deposit
    {
        return DB::transaction(function () use ($admin, $depositId) {
            $deposit = $this->repo->findForUpdate($depositId);

            if (! $deposit) {
                throw new \RuntimeException('Deposit not found');
            }

            if ($deposit->status !== 'pending') {
                throw new \RuntimeException('Deposit is not pending');
            }

            $deposit = $this->repo->update($deposit, [
                'status' => 'confirmed',
                'admin_id' => $admin->id,
                'confirmed_at' => Carbon::now(),
            ]);

            $rate = (float) config('payments.points_per_unit', 1);
            $minor = (int) config('payments.currency_minor_unit', 100);

            $points = (int) floor(($deposit->amount / $minor) * $rate);

            if ($points > 0) {
                PointBalance::adjustPointsForUser(
                    $deposit->user_id,
                    $points,
                    'deposit',
                    $deposit->id,
                    'deposit',
                    "Deposit confirmed: {$deposit->transaction_id}"
                );

                (new FirebaseNotificationService($deposit, $deposit->user, $deposit->user_id, 'user'))
                ->send([
                    'title' => 'Topup success',
                    'preview' => "Your Topup balance is updated. You now have {$deposit->user->pointBalance->points} total points"
                ]);

                (new FirebaseNotificationService($deposit, \App\Models\Admin::all(), $admin->id, 'admin'))
                ->send([
                    'title' => 'User topup confirmed',
                    'preview' => "Topup balance of user {$deposit->user->name} is confirmed by {$admin->name}. Amount confirmed is: {$deposit->amount}"
                ]);
            }

            return $deposit;
        });
    }

    public function rejectDeposit(Admin $admin, int $depositId, ?string $note = null): Deposit
    {
        return DB::transaction(function () use ($admin, $depositId, $note) {
            $deposit = $this->repo->findForUpdate($depositId);

            if (! $deposit) {
                throw new \RuntimeException('Deposit not found');
            }

            if ($deposit->status !== 'pending') {
                throw new \RuntimeException('Deposit is not pending');
            }

            $deposit = $this->repo->update($deposit, [
                'status' => 'rejected',
                'admin_id' => $admin->id,
                'admin_note' => $note,
                'confirmed_at' => Carbon::now(),
            ]);

            return $deposit;
        });
    }
}

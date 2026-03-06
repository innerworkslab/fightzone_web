<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Events\WalkinConfirmed;
use App\Events\WalkinScanFailed;
use App\Events\WalkinScanStarted;
use App\Models\PackagePurchase;
use App\Models\UserWalkin;

class WalkinService
{
    /** QR payload validity in seconds (e.g. 5 minutes) */
    public const QR_PAYLOAD_TTL = 300;

    /** Cache key prefix for one-time QR usage */
    private const USED_PAYLOAD_PREFIX = 'walkin_used:';

    /**
     * Generate a signed QR payload for a package purchase so the client can display it as QR.
     * Each payload includes a unique nonce and is valid for one walk-in only.
     *
     * @param int $userId
     * @param int $packagePurchaseId
     * @return string Encrypted payload string to be encoded in QR
     * @throws \RuntimeException
     */
    public function generateQrPayload(int $userId, int $packagePurchaseId): string
    {
        $purchase = PackagePurchase::where('id', $packagePurchaseId)
            ->where('user_id', $userId)
            ->first();

        if (! $purchase) {
            throw new \RuntimeException('Package purchase not found');
        }

        if ($purchase->completed) {
            throw new \RuntimeException('This package has no remaining walk-in days');
        }

        if ($purchase->remaining_days < 1) {
            throw new \RuntimeException('No remaining walk-in days');
        }

        $payload = [
            'package_purchase_id' => $purchase->id,
            'user_id' => $userId,
            'exp' => now()->addSeconds(self::QR_PAYLOAD_TTL)->timestamp,
            'nonce' => Str::random(32),
        ];

        return Crypt::encryptString(json_encode($payload));
    }

    /**
     * Get the user's current valid package purchase (at most one due to purchase guard).
     */
    public function getCurrentValidPackagePurchase(int $userId): ?PackagePurchase
    {
        return PackagePurchase::with(['package', 'purchase'])
            ->where('user_id', $userId)
            ->valid()
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * Confirm a walk-in from a scanned QR payload (called by admin).
     * Each QR payload is one-time use; duplicate scans are rejected.
     * Decrements remaining_days, creates UserWalkin, marks purchase completed if no days left,
     * and broadcasts result to the user for real-time feedback.
     *
     * @param string $qrPayload The encrypted string from the QR code
     * @param int|null $adminId Optional admin who confirmed
     * @return array{ package_purchase: PackagePurchase, user_walkin: UserWalkin, remaining_days: int, completed: bool }
     * @throws \RuntimeException
     */
    public function confirmWalkin(string $qrPayload, ?int $adminId = null): array
    {
        // Decrypt first so we can notify the user even on expired / used scans.
        $decoded = $this->decryptQrPayload($qrPayload);

        $userId = (int) $decoded['user_id'];
        $packagePurchaseId = (int) $decoded['package_purchase_id'];
        $nonce = (string) $decoded['nonce'];
        $exp = (int) $decoded['exp'];

        event(new WalkinScanStarted(
            userId: $userId,
            packagePurchaseId: $packagePurchaseId,
            nonce: $nonce,
            expiresAtUnix: $exp,
        ));

        if ($exp < time()) {
            event(new WalkinScanFailed(
                userId: $userId,
                packagePurchaseId: $packagePurchaseId,
                nonce: $nonce,
                reason: 'expired',
                message: 'QR code expired. Please refresh and try again.',
            ));
            throw new \RuntimeException('Invalid or expired QR code. Please ask the user to refresh the code.');
        }

        $usedKey = self::USED_PAYLOAD_PREFIX . $nonce;
        if (! Cache::add($usedKey, true, self::QR_PAYLOAD_TTL)) {
            event(new WalkinScanFailed(
                userId: $userId,
                packagePurchaseId: $packagePurchaseId,
                nonce: $nonce,
                reason: 'used',
                message: 'This QR code was already used. Please refresh and try again.',
            ));
            throw new \RuntimeException('This QR code has already been used for a walk-in. Please ask the user to refresh the code.');
        }

        try {
            return $this->processWalkin($decoded);
        } catch (\RuntimeException $e) {
            event(new WalkinScanFailed(
                userId: $userId,
                packagePurchaseId: $packagePurchaseId,
                nonce: $nonce,
                reason: $this->mapFailureReason($e->getMessage()),
                message: $e->getMessage(),
            ));
            Cache::forget($usedKey);
            throw $e;
        } catch (\Throwable $e) {
            event(new WalkinScanFailed(
                userId: $userId,
                packagePurchaseId: $packagePurchaseId,
                nonce: $nonce,
                reason: 'error',
                message: 'Walk-in failed due to a server error. Please try again.',
            ));
            Cache::forget($usedKey);
            throw $e;
        }
    }

    /**
     * Process the walk-in transaction.
     *
     * @return array{ package_purchase: PackagePurchase, user_walkin: UserWalkin, remaining_days: int, completed: bool }
     */
    private function processWalkin(array $decoded): array
    {
        return DB::transaction(function () use ($decoded): array {
            $purchase = PackagePurchase::where('id', $decoded['package_purchase_id'])
                ->where('user_id', $decoded['user_id'])
                ->lockForUpdate()
                ->first();

            if (! $purchase) {
                throw new \RuntimeException('Package purchase not found');
            }

            if ($purchase->completed) {
                throw new \RuntimeException('This package has no remaining walk-in days');
            }

            if ($purchase->remaining_days < 1) {
                throw new \RuntimeException('No remaining walk-in days');
            }

            $now = now();
            $userWalkin = UserWalkin::create([
                'package_purchase_id' => $purchase->id,
                'walkin_at' => $now,
                'confirmed_at' => $now,
            ]);

            $newRemaining = $purchase->remaining_days - 1;
            $completed = $newRemaining <= 0;

            $purchase->update([
                'remaining_days' => $newRemaining,
                'completed' => $completed,
            ]);

            $purchase->refresh();

            event(new WalkinConfirmed(
                userId: (int) $purchase->user_id,
                packagePurchaseId: $purchase->id,
                userWalkinId: $userWalkin->id,
                remainingDays: $newRemaining,
                completed: $completed,
            ));

            return [
                'package_purchase' => $purchase,
                'user_walkin' => $userWalkin,
                'remaining_days' => $newRemaining,
                'completed' => $completed,
            ];
        });
    }

    /**
     * Decrypt and minimally validate QR payload.
     * This does NOT enforce expiry so we can still notify the user on an expired scan.
     *
     * @return array{ package_purchase_id: int, user_id: int, exp: int, nonce: string }
     */
    private function decryptQrPayload(string $qrPayload): array
    {
        try {
            $json = Crypt::decryptString($qrPayload);
        } catch (\Throwable) {
            throw new \RuntimeException('Invalid or expired QR code. Please ask the user to refresh the code.');
        }

        $data = json_decode($json, true);
        if (! is_array($data)
            || ! isset($data['package_purchase_id'], $data['user_id'], $data['exp'], $data['nonce'])
        ) {
            throw new \RuntimeException('Invalid or expired QR code. Please ask the user to refresh the code.');
        }

        return [
            'package_purchase_id' => (int) $data['package_purchase_id'],
            'user_id' => (int) $data['user_id'],
            'exp' => (int) $data['exp'],
            'nonce' => (string) $data['nonce'],
        ];
    }

    private function mapFailureReason(string $message): string
    {
        $m = strtolower($message);

        if (str_contains($m, 'not found')) {
            return 'not_found';
        }
        if (str_contains($m, 'already been used')) {
            return 'used';
        }
        if (str_contains($m, 'expired')) {
            return 'expired';
        }
        if (str_contains($m, 'no remaining')) {
            return 'no_remaining';
        }
        if (str_contains($m, 'no remaining walk-in days')) {
            return 'no_remaining';
        }

        return 'rejected';
    }
}

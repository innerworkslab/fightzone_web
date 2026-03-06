<?php

namespace App\Http\Controllers\API\v1\User\Shop;

use App\Http\Controllers\Controller;
use App\Services\WalkinService;

class WalkinController extends Controller
{
    public function __construct(protected WalkinService $walkinService)
    {
    }

    /**
     * Get QR payload for the current user's active package purchase.
     * There is at most one valid package purchase per user at a time.
     * Returns package_purchase, qr_payload, and expires_in_seconds in one response.
     */
    public function qrPayload()
    {
        $userId = ApiUser()->id;

        $packagePurchase = $this->walkinService->getCurrentValidPackagePurchase($userId);

        if (! $packagePurchase) {
            ResponseData(
                ['error' => 'No active package purchase. Please purchase a package first.'],
                404,
                false,
                'No active package purchase',
                'data'
            );
        }

        try {
            $qrPayload = $this->walkinService->generateQrPayload($userId, $packagePurchase->id);
        } catch (\RuntimeException $e) {
            ResponseData(['error' => $e->getMessage()], 422, false, $e->getMessage(), 'data');
        }

        ResponseData([
            'package_purchase' => $packagePurchase,
            'qr_payload' => $qrPayload,
            'expires_in_seconds' => WalkinService::QR_PAYLOAD_TTL,
        ]);
    }
}

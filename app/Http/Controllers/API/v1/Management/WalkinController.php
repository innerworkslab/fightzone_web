<?php

namespace App\Http\Controllers\API\v1\Management;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Services\WalkinService;

class WalkinController extends Controller
{
    public function __construct(protected WalkinService $walkinService)
    {
    }

    /**
     * Confirm a walk-in from scanned QR payload.
     * Request body: { "qr_payload": "..." }
     * Decrements remaining_days, creates UserWalkin, and broadcasts to the user's channel.
     */
    public function confirm(Request $request)
    {
        $data = $request->validate([
            'qr_payload' => 'required|string',
        ]);

        $admin = $request->user();
        $adminId = $admin ? $admin->id : null;

        try {
            $result = $this->walkinService->confirmWalkin($data['qr_payload'], $adminId);
        } catch (\RuntimeException $e) {
            ResponseData(
                ['error' => $e->getMessage()],
                422,
                false,
                $e->getMessage(),
                'data'
            );
        }

        ResponseData([
            'package_purchase' => $result['package_purchase']->load(['user', 'package']),
            'user_walkin' => $result['user_walkin'],
            'remaining_days' => $result['remaining_days'],
            'completed' => $result['completed'],
        ]);
    }

    public function index(Request $request)
    {
        $data = $this->walkinService->getDailyWalkins(            
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }
}

<?php

namespace App\Http\Controllers\API\v1\Management;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Http\Controllers\Controller;

use App\Services\DepositService;

class DepositController extends Controller
{
    public function __construct(protected DepositService $service)
    {

    }

    public function index(Request $request)
    {
        $filters = [];
        if ($request->user_id) {
            array_push($filters, ['user_id' => $request->user_id]);
        }
        if ($request->payment_method_id) {
            array_push($filters, ['payment_method_id' => $request->payment_method_id]);
        }
        if ($request->transaction_id) {
            array_push($filters, ['transaction_id' => $request->transaction_id]);
        }
        if ($request->amount) {
            array_push($filters, ['amount' => $request->amount]);
        }

        $data = $this->service->all(
            $filters,
            $request->status?? null,
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }

    public function show($id)
    {
        $deposit = $this->service->detail($id);

        ResponseData($deposit);
    }

    public function confirm(Request $request, $id)
    {
        $admin = $request->user();
        $deposit = $this->service->confirmDeposit($admin, (int) $id);
        ResponseData($deposit);
    }

    public function reject(Request $request, $id)
    {
        $admin = $request->user();
        $data = $request->validate([
            'note' => 'nullable|string',
        ]);
        $deposit = $this->service->rejectDeposit($admin, (int) $id, $data['note'] ?? null);

        ResponseData($deposit);
    }
}

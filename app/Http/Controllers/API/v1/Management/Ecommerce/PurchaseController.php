<?php

namespace App\Http\Controllers\API\v1\Management\Ecommerce;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\PurchaseService;

class PurchaseController extends Controller
{
    public function __construct(protected PurchaseService $service)
    {

    }

    public function index(Request $request)
    {
        $filters = [];
        if ($request->user_id) {
            array_push($filters, ['user_id' => $request->user_id]);
        }
        if ($request->purchasable_type) {
            array_push($filters, ['purchasable_type' => $request->purchasable_type]);
        }
        if ($request->purchasable_id) {
            array_push($filters, ['purchasable_id' => $request->purchasable_id]);
        }

        $data = $this->service->all(
            $filters,
            $request->status ?? null,
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }

    public function show($id)
    {
        $purchase = $this->service->detail($id);

        ResponseData($purchase);
    }

    public function confirm(Request $request, $id)
    {
        $admin = $request->user();
        $purchase = $this->service->confirmPurchase($admin, (int) $id);
        ResponseData($purchase);
    }

    public function reject(Request $request, $id)
    {
        $admin = $request->user();
        $data = $request->validate([
            'note' => 'nullable|string',
        ]);
        $purchase = $this->service->rejectPurchase($admin, (int) $id, $data['note'] ?? null);

        ResponseData($purchase);
    }
}

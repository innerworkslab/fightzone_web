<?php

namespace App\Http\Controllers\API\v1\User\Shop;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Controller;

use App\Services\PurchaseService;

class PurchaseController extends Controller
{
    protected PurchaseService $service;


    public function __construct(PurchaseService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->service->listForUser(
            ApiUser()->id,
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchasable_type' => ['required', 'string', Rule::in(config('common.purchasable_types'))],
            'purchasable_id' => 'required|integer',
            'quantity' => 'sometimes|integer|min:1|max:100',
        ]);

        $userId = ApiUser()->id;

        $data = $request->all();
        $quantity = $request->quantity ?? 1;

        $purchase = $this->service->createPurchase(
            $userId,
            $data['purchasable_type'],
            $data['purchasable_id'],
            $quantity
        );

        ResponseData($purchase);
    }

    public function show($id)
    {
        $purchase = $this->service->detail($id);

        // Ensure user can only view their own purchases
        if ($purchase->user_id !== ApiUser()->id) {
            abort(403, 'Unauthorized');
        }

        ResponseData($purchase);
    }
}

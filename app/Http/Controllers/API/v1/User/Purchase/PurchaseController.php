<?php

namespace App\Http\Controllers\API\v1\User\Purchase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Controller;
use App\Models\CourseLevel;

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
        $courseLevelTypes = ['course_level', CourseLevel::class];

        $request->validate([
            'purchasable_type' => ['required', 'string', Rule::in(config('common.purchasable_types'))],
            'purchasable_id' => 'required|integer',
            'quantity' => 'sometimes|integer|min:1|max:100',
            // 'certificate' => [Rule::requiredIf(fn () => in_array($request->purchasable_type, $courseLevelTypes, true)), 'image', 'max:5120'],
            'certificate' => 'sometimes|image|max:5120',
            'note' => 'sometimes|string',
        ]);

        $userId = ApiUser()->id;

        $data = $request->all();
        $quantity = $request->quantity ?? 1;
        $certificatePath = $request->hasFile('certificate')
            ? $request->file('certificate')->store('purchase-certificates', 'public')
            : null;

        try {
            $purchase = $this->service->createPurchase(
                $userId,
                $data['purchasable_type'],
                $data['purchasable_id'],
                $quantity,
                $certificatePath,
                $request->note ?? null
            );
        } catch (\Throwable $e) {
            if ($certificatePath) {
                Storage::disk('public')->delete($certificatePath);
            }
            throw $e;
        }

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

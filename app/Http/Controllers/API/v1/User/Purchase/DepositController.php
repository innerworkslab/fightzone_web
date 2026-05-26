<?php

namespace App\Http\Controllers\API\v1\User\Purchase;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\DepositService;

class DepositController extends Controller
{
    protected DepositService $service;

    public function __construct(DepositService $service)
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
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            // 'transaction_id' => 'required',
            'screenshot' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $userId = ApiUser()->id;

        $deposit = $this->service->createDeposit(
            $userId,
            $request->payment_method_id,
            $request->amount,
        );

        if($deposit){
            $uploaded = UploadFileToServer($request, "screenshot", "users/{$userId}/deposit_screenshots");
            $data['screenshot_path'] = $uploaded['file_path'];
            $deposit = $this->service->updateDeposit($deposit->id, $data);

            ResponseData($deposit);
        }else{
            ResponseMessage("Error occurred when depositing, please try again", 500, false);
        }
    }
}

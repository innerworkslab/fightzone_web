<?php

namespace App\Http\Controllers\API\v1\Management;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\PaymentMethodService;

class PaymentMethodController extends Controller
{
    public function __construct(protected PaymentMethodService $service){}

    public function index(Request $request)
    {
        $filters = [];
        if ($request->name) {
            array_push($filters, ['name' => $request->name]);
        }
        if ($request->holder) {
            array_push($filters, ['holder' => $request->holder]);
        }
        if ($request->acc_number) {
            array_push($filters, ['account_number' => $request->acc_number]);
        }

        $data = $this->service->all(
            false,
            $filters,
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'holder' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'logo' => 'sometimes|image|max:2048',
            'is_active' => 'sometimes|boolean'
        ]);

        $item = $this->service->create($validated);

        if ($request->hasFile('logo')) {
            $uploaded = UploadFileToServer($request, 'logo', "payment-method-logos/{$item->id}");
            $path = $uploaded['file_path'];
            $item = $this->service->update($item->id, ['logo_path' => $path]);
        }

        ResponseData($item, 201);
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        if (! $item) ResponseMessage('Payment method not found', 404);
        ResponseData($item);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'holder' => 'sometimes|string|max:255',
            'account_number' => 'sometimes|string|max:255',
            'logo' => 'sometimes|image|max:2048',
            'is_active' => 'sometimes|boolean'
        ]);

        $item = $this->service->find($id);
        if (! $item) ResponseMessage('Payment method not found', 404);

        if ($request->hasFile('logo')) {
            // delete old logo if exists
            if ($item->getLogoPath()) {
                DeleteFileFromServer($item->getLogoPath());
            }
            $uploaded = UploadFileToServer($request, 'logo', "payment-method-logos/{$id}");
            $path = $uploaded['file_path'];
            $validated['logo_path'] = $path;
        }

        $updated = $this->service->update($id, $validated);
        if (! $updated) ResponseMessage('Payment method not found', 404);
        ResponseData($updated);
    }

    public function destroy($id)
    {
        $item = $this->service->find($id);
        if (! $item) ResponseMessage('Payment method not found', 404);

        // delete logo file if exists
        if ($item->logo_path) {
            DeleteFileFromServer($item->logo_path);
        }

        $deleted = $this->service->delete($id);
        if (! $deleted) ResponseMessage('Payment method not found', 404);
        ResponseMessage('Payment method deleted');
    }

    public function toggle($id)
    {
        $item = $this->service->toggleActive($id);
        if (! $item) ResponseMessage('Payment method not found', 404);
        ResponseData($item);
    }
}

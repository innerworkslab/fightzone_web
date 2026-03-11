<?php

namespace App\Http\Controllers\API\v1\Management\Packages;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\PackageService;

class PackageController extends Controller
{
    public function __construct(protected PackageService $service){}

    public function index(Request $request)
    {
        $filters = [];
        if ($request->name) {
            array_push($filters, ['name' => $request->name]);
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
            'price' => 'required|numeric',
            'days' => 'required|integer'
        ]);

        $item = $this->service->create($validated);

        ResponseData($item, 201);
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        if (! $item) ResponseMessage('Package not found', 404);
        ResponseData($item);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric',
            'days' => 'sometimes|integer'
        ]);

        $item = $this->service->find($id);
        if (! $item) ResponseMessage('Package not found', 404);

        $updated = $this->service->update($id, $validated);
        if (! $updated) ResponseMessage('Package not found', 404);
        ResponseData($updated);
    }

    public function destroy($id)
    {
        $item = $this->service->find($id);
        if (! $item) ResponseMessage('Package not found', 404);

        $deleted = $this->service->delete($id);
        if (! $deleted) ResponseMessage('Package not found', 404);
        ResponseMessage('Package deleted');
    }

    public function toggle($id)
    {
        $item = $this->service->toggleActive($id);
        if (! $item) ResponseMessage('Package not found', 404);
        ResponseData($item);
    }
}

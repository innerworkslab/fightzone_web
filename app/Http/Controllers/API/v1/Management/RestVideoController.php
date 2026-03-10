<?php

namespace App\Http\Controllers\API\v1\Management;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\RestVideoService;

class RestVideoController extends Controller
{
    //
    public function __construct(protected RestVideoService $service)
    {

    }

    public function index(Request $request)
    {
        $filters = [];
        if ($request->name) {
            array_push($filters, ['name' => $request->name]);
        }

        $onlyActive = ($request->only_active)? true: false;
        $data = $this->service->all(
            $onlyActive,
            $filters,
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|string|max:255',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail_url' => 'nullable|string',
            'duration' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $item = $this->service->create($validated);

        ResponseData($item, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'url' => 'required|string|max:255',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail_url' => 'nullable|string',
            'duration' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Rest video not found', 404);

        $updated = $this->service->update($id, $validated);
        if (!$updated) ResponseMessage('Rest video not found', 404);
        ResponseData($updated);
    }

    public function destroy($id)
    {
        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Rest video not found', 404);

        $deleted = $this->service->delete($id);
        if (!$deleted) ResponseMessage('Rest video not found', 404);
        ResponseMessage('Rest video deleted');
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Rest video not found', 404);
        ResponseData($item);
    }

    public function toggle($id)
    {
        $item = $this->service->toggleActive($id);
        if (!$item) ResponseMessage('Rest video not found', 404);
        ResponseData($item);
    }
}

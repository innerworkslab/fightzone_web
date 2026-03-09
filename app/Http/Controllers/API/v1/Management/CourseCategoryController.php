<?php

namespace App\Http\Controllers\API\v1\Management;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\CourseCategoryService;

class CourseCategoryController extends Controller
{
    public function __construct(protected CourseCategoryService $service)
    {

    }

    public function index(Request $request)
    {
        $filters = [];
        if ($request->name) {
            array_push($filters, ['name' => $request->name]);
        }

        $data = $this->service->all(
            false, // include inactive
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
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'sometimes|file'
        ]);

        $item = $this->service->create($validated, $request->hasFile('image')?$request->file('image'):null );

        ResponseData($item, 201);
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Course category not found', 404);
        ResponseData($item);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Course category not found', 404);

        $updated = $this->service->update($id, $validated);
        if (!$updated) ResponseMessage('Course category not found', 404);
        ResponseData($updated);
    }

    public function destroy($id)
    {
        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Course category not found', 404);

        $deleted = $this->service->delete($id);
        if (!$deleted) ResponseMessage('Course category not found', 404);
        ResponseMessage('Course category deleted');
    }

    public function toggle($id)
    {
        $item = $this->service->toggleActive($id);
        if (!$item) ResponseMessage('Course category not found', 404);
        ResponseData($item);
    }
}

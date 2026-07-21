<?php

namespace App\Http\Controllers\API\v1\Management\Techniques;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Services\TechniqueCategoryService;

class TechniqueCategoryController extends Controller
{
    public function __construct(protected TechniqueCategoryService $service) {}

    public function index(Request $request)
    {
        $filters = [];
        if ($request->search) {
            $filters[] = ['name' => $request->search];
        }
        if ($request->name) {
            $filters[] = ['name' => $request->name];
        }
        if ($request->is_active && $request->is_active !== 'all') {
            $filters[] = ['is_active' => $request->boolean('is_active')];
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
            'is_active' => 'boolean',
        ]);

        ResponseData($this->service->create($validated), 201);
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Technique category not found', 404);
        ResponseData($item);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'is_active' => 'boolean',
        ]);

        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Technique category not found', 404);
        ResponseData($this->service->update($id, $validated));
    }

    public function destroy($id)
    {
        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Technique category not found', 404);
        $this->service->delete($id);
        ResponseMessage('Technique category deleted');
    }

    public function toggle($id)
    {
        $item = $this->service->toggleActive($id);
        if (!$item) ResponseMessage('Technique category not found', 404);
        ResponseData($item);
    }
}

<?php

namespace App\Http\Controllers\API\v1\Management;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\CourseService;

class CourseController extends Controller
{
    public function __construct(protected CourseService $service)
    {

    }

    public function index(Request $request)
    {
        $filters = [];
        if ($request->name) {
            array_push($filters, ['name' => $request->name]);
        }
        if ($request->category_name) {
            array_push($filters, ['category_name' => $request->category_name]);
        }
        if ($request->level) {
            array_push($filters, ['level' => $request->level]);
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
            'course_category_id' => 'required|exists:course_categories,id',
            'is_active' => 'boolean',
        ]);

        $data = $request->only([
            'name',
            'description',
            'course_category_id',
            'is_active'
        ]);

        $item = $this->service->create($data);

        ResponseData($item, 201);
    }

    public function show($id)
    {
        $item = $this->service->findWithDetails($id);
        if (!$item) ResponseMessage('Course not found', 404);
        ResponseData($item);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'course_category_id' => 'sometimes|exists:course_categories,id',
            'is_active' => 'boolean',
        ]);

        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Course not found', 404);

        $data = $request->only([
            'name',
            'description',
            'course_category_id',
            'is_active'
        ]);

        $updated = $this->service->update($id, $data);
        if (!$updated) ResponseMessage('Course not found', 404);

        ResponseData($updated);
    }

    public function destroy($id)
    {
        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Course not found', 404);

        $deleted = $this->service->delete($id);
        if (!$deleted) ResponseMessage('Course not found', 404);
        ResponseMessage('Course deleted');
    }

    public function toggle($id)
    {
        $item = $this->service->toggleActive($id);
        if (!$item) ResponseMessage('Course not found', 404);
        ResponseData($item);
    }
}

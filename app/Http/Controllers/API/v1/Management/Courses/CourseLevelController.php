<?php

namespace App\Http\Controllers\API\v1\Management\Courses;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Controller;

use App\Enums\CourseLevels;

use App\Services\CourseLevelService;
use App\Services\LessonDayService;
use App\Services\LessonDayVideoService;

class CourseLevelController extends Controller
{
    public function __construct(
        protected CourseLevelService $service,
        protected LessonDayService $lessonDayService,
        protected LessonDayVideoService $videoService
    )
    {

    }

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
            'course_id' => 'required|exists:courses,id',
            'level' => ['required', Rule::enum(CourseLevels::class)],
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'lesson_days' => 'sometimes|json',
        ]);

        $item = $this->service->create($validated, ($request->lesson_days)? json_decode($validated['lesson_days'], true): []);

        ResponseData($item, 201);
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Course level not found', 404);
        ResponseData($item);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'course_id' => 'sometimes|exists:courses,id',
            'level' => ['sometimes', Rule::enum(CourseLevels::class)],
            'price' => 'sometimes|numeric|min:0',
            'is_active' => 'boolean',
            'lesson_days' => 'sometimes|json',
        ]);

        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Course level not found', 404);

        $updated = $this->service->update($id, $validated, ($request->lesson_days)? json_decode($request->lesson_days, true): []);
        if (!$updated) ResponseMessage('Course level not found', 404);

        ResponseData($updated);
    }

    public function destroy($id)
    {
        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Course level not found', 404);
        $deleted = $this->service->delete($id);
        if (!$deleted) ResponseMessage('Course level not found', 404);
        ResponseMessage('Course level deleted');
    }

    public function toggle($id)
    {
        $item = $this->service->toggleActive($id);
        if (!$item) ResponseMessage('Course level not found', 404);
        ResponseData($item);
    }
}

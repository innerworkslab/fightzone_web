<?php

namespace App\Http\Controllers\API\v1\Management;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Controller;

use App\Enums\CourseLevels;

use App\Services\CourseLevelService;
use App\Services\LessonDayService;

class CourseLevelController extends Controller
{
    public function __construct(protected CourseLevelService $service, protected LessonDayService $lessonDayService)
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
            'lesson_days' => 'required|json',
        ]);

        $item = $this->service->create($validated);

        $lessonDays = json_decode($validated['lesson_days'], true);
        if(count($lessonDays) < 1){
            ResponseMessage("Lesson days must be present", 400);
        }
        foreach ($lessonDays as $lessonDay) {
            $this->lessonDayService->create([
                'name' => isset($lessonDay['name']) ? $lessonDay['name'] : "Day " . $lessonDay['day_number'],
                'course_level_id' => $item->id,
                'day_number' => $lessonDay['day_number'],
                'type' => $lessonDay['type'],
                'video_url' => isset($lessonDay['video_url']) ? $lessonDay['video_url'] : null,
                'duration' => isset($lessonDay['duration']) ? $lessonDay['duration'] : null,
            ]);
        }

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

        $updated = $this->service->update($id, $validated);
        if (!$updated) ResponseMessage('Course level not found', 404);

        $lessonDays = json_decode($request->lesson_days, true);
        if(count($lessonDays) > 0){
            foreach($lessonDays as $lessonDay){
                if(isset($lessonDay['id'])){
                    $this->lessonDayService->update($lessonDay['id'], [
                        'name' => isset($lessonDay['name']) ? $lessonDay['name'] : "Day " . $lessonDay['day_number'],
                        // 'course_level_id' => $item->id,
                        // 'day_number' => $lessonDay['day_number'],
                        'type' => $lessonDay['type'],
                        'video_url' => isset($lessonDay['video_url']) ? $lessonDay['video_url'] : null,
                        'duration' => isset($lessonDay['duration']) ? $lessonDay['duration'] : null,
                    ]);
                }else{
                    $this->lessonDayService->create([
                        'name' => isset($lessonDay['name']) ? $lessonDay['name'] : "Day " . $lessonDay['day_number'],
                        'course_level_id' => $item->id,
                        'day_number' => $lessonDay['day_number'],
                        'type' => $lessonDay['type'],
                        'video_url' => isset($lessonDay['video_url']) ? $lessonDay['video_url'] : null,
                        'duration' => isset($lessonDay['duration']) ? $lessonDay['duration'] : null,
                    ]);
                }
            }
        }

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

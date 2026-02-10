<?php

namespace App\Http\Controllers\API\v1\Management;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Controller;

use App\Enums\LessonDayTypes;

use App\Services\LessonDayService;

class LessonDayController extends Controller
{
    //
    public function __construct(
        protected LessonDayService $lessonDayService
    )
    {

    }

    public function index($levelId)
    {
        $data = $this->lessonDayService->all($levelId);

        ResponseData($data);
    }

    public function show($levelId, $lessonDayId)
    {
        $data = $this->lessonDayService->find($lessonDayId);

        ResponseData($data);
    }

    public function store(Request $request, $levelId)
    {
        $request->validate([
            'day_number' => 'required|numeric',
            'type' => ['required', Rule::enum(LessonDayTypes::class)],
            'name' => 'sometimes|string',
            'duration' => 'sometimes|string',
            'videos' => 'sometimes|json'
        ]);

        $createdLessonDay = $this->lessonDayService->create($levelId, [
            'course_level_id' => $levelId,
            'name' => $request->name ? $request->name : "Day " . $request->day_number,
            'day_number' => $request->day_number,
            'type' => $request->type,
            'duration' => $request->duration ? $request->duration : null,
        ], ($request->videos)? json_decode($request->videos, true): []);

        ResponseData($createdLessonDay);
    }

    public function update(Request $request, $levelId, $lessonDayId)
    {
        $request->validate([
            'type' => ['sometimes', Rule::enum(LessonDayTypes::class)],
            'name' => 'sometimes|string',
            'duration' => 'sometimes|string',
            'videos' => 'sometimes|json'
        ]);

        $updatedLessonDay = $this->lessonDayService->update($lessonDayId, [
            'name' => $request->name,
            'type' => $request->type,
            'duration' => $request->duration ? $request->duration : null,
        ], ($request->videos)? json_decode($request->videos, true): []);

        ResponseData($updatedLessonDay);
    }

    public function destroy($levelId, $lessonDayId)
    {
        $this->lessonDayService->delete($lessonDayId);
        ResponseMessage("Lesson day deleted");
    }
}

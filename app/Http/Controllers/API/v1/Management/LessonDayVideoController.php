<?php

namespace App\Http\Controllers\API\v1\Management;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Controller;

use App\Enums\LessonDayVideoTypes;

use App\Services\LessonDayVideoService;

class LessonDayVideoController extends Controller
{
    //
    public function __construct(protected LessonDayVideoService $service)
    {

    }

    public function store(Request $request)
    {
        $request->validate([
            'lesson_day_id' => 'required|exists:lesson_days,id',
            'url' => 'required|string',
            'name' => 'sometimes',
            'description' => 'sometimes',
            'duration' => 'sometimes',
            'type' => ['required', Rule::enum(LessonDayVideoTypes::class)]
        ]);

        $this->service->attachVideoToLesson($request->lesson_day_id, [$request->all()]);
        ResponseMessage("Lesson day video created", 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // 'lesson_day_id' => 'required|exists:lesson_days,id',
            'url' => 'required|string',
            'name' => 'sometimes',
            'description' => 'sometimes',
            'duration' => 'sometimes',
            'type' => ['required', Rule::enum(LessonDayVideoTypes::class)]
        ]);

        $this->service->update($id, $request->all());
        ResponseMessage("Lesson day video updated", 200);
    }
}

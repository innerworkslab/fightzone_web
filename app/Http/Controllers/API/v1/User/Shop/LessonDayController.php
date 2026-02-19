<?php

namespace App\Http\Controllers\API\v1\User\Shop;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\LessonDayService;

class LessonDayController extends Controller
{
    //
    public function __construct(protected LessonDayService $service)
    {

    }

    public function show(Request $request, $courseId, $levelId, $id)
    {
        $userId = ApiUser()->id;
        $data = $this->service->findWithCompletionForUser(
            (int) $courseId,
            (int) $levelId,
            (int) $id,
            (int) $userId
        );
        ResponseData($data);
    }
}

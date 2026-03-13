<?php

namespace App\Http\Controllers\API\v1\User\Consumption;

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

    /**
     * Mark a lesson video as completed
     */
    public function markVideoCompletion(Request $request, $lessonDayVideoId)
    {
        $userId = ApiUser()->id;

        try {
            $result = $this->service->markVideoCompletion((int) $userId, (int) $lessonDayVideoId);
            ResponseData($result, 200);
        } catch (\RuntimeException $e) {
            ResponseMessage($e->getMessage(), 404);
        } catch (\Exception $e) {
            ResponseMessage($e->getMessage(), 500);
        }
    }

    /**
     * Get previous and next videos relative to the currently playing lesson day video.
     */
    public function prevNextVideo(Request $request, $lessonDayVideoId)
    {
        $userId = ApiUser()->id;

        try {
            $result = $this->service->getPrevNextVideoForUser((int) $userId, (int) $lessonDayVideoId);
            ResponseData($result, 200);
        } catch (\RuntimeException $e) {
            ResponseMessage($e->getMessage(), 404);
        } catch (\Exception $e) {
            ResponseMessage($e->getMessage(), 500);
        }
    }
}

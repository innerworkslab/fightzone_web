<?php

namespace App\Http\Controllers\API\v1\User\Shop;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\CourseDayService;

class CourseDayController extends Controller
{
    public function __construct(protected CourseDayService $service)
    {

    }

    /**
     * Get course days for a specific course
     */
    public function index(Request $request, $courseId)
    {
        $data = $this->service->findByCourse(
            $courseId,
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }

    /**
     * Get specific course day
     */
    public function show($courseId, $dayId)
    {
        $courseDay = $this->service->find($dayId);

        if (!$courseDay || $courseDay->course_id != $courseId) {
            ResponseMessage('Course day not found', 404);
        }

        ResponseData($courseDay);
    }

    /**
     * Get course day by day number
     */
    public function getByDayNumber($courseId, $dayNumber)
    {
        $courseDay = $this->service->getByDayNumber($courseId, $dayNumber);

        if (!$courseDay) {
            ResponseMessage('Course day not found', 404);
        }

        ResponseData($courseDay);
    }

    /**
     * Get course summary (total days, duration, etc.)
     */
    public function getSummary($courseId)
    {
        $summary = $this->service->getCourseSummary($courseId);

        ResponseData($summary);
    }
}
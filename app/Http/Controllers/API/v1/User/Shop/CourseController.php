<?php

namespace App\Http\Controllers\API\v1\User\Shop;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\CourseService;
use App\Services\CourseLevelService;

class CourseController extends Controller
{
    public function __construct(protected CourseService $service, protected CourseLevelService $levelService)
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
            true, // only active
            $filters,
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }

    public function show($id)
    {
        $userId = ApiUser()->id;
        $course = $this->service->findWithCourseLevelPurchaseStatus($id, $userId);

        if (!$course) {
            ResponseMessage('Course not found', 404);
        }

        ResponseData($course);
    }

    public function getByCategory($categoryId, Request $request)
    {
        $data = $this->service->getByCategory(
            $categoryId,
            true, // only active
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }

    public function getByLevel($level, Request $request)
    {
        // Validate level
        $validLevels = ['beginner', 'intermediate', 'expert'];
        if (!in_array($level, $validLevels)) {
            ResponseMessage('Invalid course level', 400);
        }

        $data = $this->service->getByLevel(
            $level,
            true, // only active
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }

    public function getCourseLevelLessonDays($id, $levelId)
    {
        $userId = ApiUser()->id;
        $data = $this->levelService->getLessons((int) $id, (int) $levelId, (int) $userId);

        ResponseData($data);
    }
}

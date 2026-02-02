<?php

namespace App\Http\Controllers\API\v1\User\Shop;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\CourseCategoryService;

class CourseCategoryController extends Controller
{
    public function __construct(protected CourseCategoryService $service)
    {

    }

    public function index(Request $request)
    {
        $filters = [];
        if ($request->name) {
            array_push($filters, ['name' => $request->name]);
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
        $category = $this->service->find($id);

        if (!$category) {
            ResponseMessage('Course category not found', 404);
        }

        ResponseData($category);
    }
}
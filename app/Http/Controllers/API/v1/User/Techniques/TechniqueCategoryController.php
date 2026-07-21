<?php

namespace App\Http\Controllers\API\v1\User\Techniques;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Services\TechniqueCategoryService;

class TechniqueCategoryController extends Controller
{
    public function __construct(protected TechniqueCategoryService $service) {}

    public function index(Request $request)
    {
        $data = $this->service->all(true, [], $request->page, $request->limit);

        ResponseData($data);
    }
}

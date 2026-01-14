<?php

namespace App\Http\Controllers\API\v1\User\Shop;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\PackageService;

class PackageController extends Controller
{
    public function __construct(protected PackageService $service){}

    public function index(Request $request)
    {
        $filters = [];
        if ($request->name) {
            array_push($filters, ['name' => $request->name]);
        }

        $data = $this->service->all(
            true,
            $filters,
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }
}

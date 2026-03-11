<?php

namespace App\Http\Controllers\API\v1\User\Misc;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\FeaturedImageService;

class FeaturedImageController extends Controller
{
    //
    public function __construct(protected FeaturedImageService $service){}

    public function index(Request $request)
    {
        $data = $this->service->all(
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }
}

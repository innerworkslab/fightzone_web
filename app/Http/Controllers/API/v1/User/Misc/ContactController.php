<?php

namespace App\Http\Controllers\API\v1\User\Misc;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Services\ContactService;

class ContactController extends Controller
{
    public function __construct(protected ContactService $service) {}

    public function index(Request $request)
    {
        $filters = [];
        if ($request->type && $request->type !== 'all') {
            $filters[] = ['type' => $request->type];
        }

        $data = $this->service->all(true, $filters, $request->page, $request->limit);

        ResponseData($data);
    }
}

<?php

namespace App\Http\Controllers\API\v1\Management;

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

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|file'
        ]);

        $admin = $this->service->create($request->image);
        ResponseData($admin, 201);
    }

    public function show($id)
    {
        $image = $this->service->find($id);
        if (! $image) ResponseMessage('Featured image not found', 404);
        ResponseData($image);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|file'
        ]);

        $image = $this->service->update($id, $request->image);
        if (! $image) ResponseMessage('Featured image not found', 404);
        ResponseData($image);
    }

    public function destroy($id)
    {
        $deleted = $this->service->delete($id);
        if (! $deleted) ResponseMessage('Featured image not found', 404);
        ResponseMessage('Featured image deleted');
    }
}

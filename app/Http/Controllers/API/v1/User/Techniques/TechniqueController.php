<?php

namespace App\Http\Controllers\API\v1\User\Techniques;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Services\TechniqueService;

class TechniqueController extends Controller
{
    public function __construct(protected TechniqueService $service) {}

    public function index(Request $request)
    {
        $filters = [];
        if ($request->technique_category_id) {
            $filters[] = ['technique_category_id' => $request->technique_category_id];
        }
        if ($request->category_id) {
            $filters[] = ['technique_category_id' => $request->category_id];
        }
        if ($request->search) {
            $filters[] = ['name' => $request->search];
        }

        $data = $this->service->all(true, $filters, $request->page, $request->limit);

        ResponseData($data);
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        if (!$item || !$item->is_active || !$item->category?->is_active) {
            ResponseMessage('Technique not found', 404);
        }

        ResponseData($item);
    }

    public function getByCategory(Request $request, $id)
    {
        $filters = [
            ['technique_category_id' => $id],
        ];
        if ($request->search) {
            $filters[] = ['name' => $request->search];
        }

        $data = $this->service->all(true, $filters, $request->page, $request->limit);

        ResponseData($data);
    }
}

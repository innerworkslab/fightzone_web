<?php

namespace App\Http\Controllers\API\v1\Management\SystemConfig;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Controller;
use App\Services\ContactService;

class ContactController extends Controller
{
    public function __construct(protected ContactService $service) {}

    public function index(Request $request)
    {
        $filters = [];
        if ($request->search) {
            $filters[] = ['search' => $request->search];
        }
        if ($request->name) {
            $filters[] = ['name' => $request->name];
        }
        if ($request->type && $request->type !== 'all') {
            $filters[] = ['type' => $request->type];
        }
        if ($request->is_active && $request->is_active !== 'all') {
            $filters[] = ['is_active' => $request->boolean('is_active')];
        }

        $data = $this->service->all(
            false,
            $filters,
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['url', 'phone_number'])],
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        ResponseData($this->service->create($validated), 201);
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Contact not found', 404);
        ResponseData($item);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => ['sometimes', Rule::in(['url', 'phone_number'])],
            'name' => 'sometimes|string|max:255',
            'contact' => 'sometimes|string|max:255',
            'is_active' => 'boolean',
        ]);

        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Contact not found', 404);
        ResponseData($this->service->update($id, $validated));
    }

    public function destroy($id)
    {
        $item = $this->service->find($id);
        if (!$item) ResponseMessage('Contact not found', 404);
        $this->service->delete($id);
        ResponseMessage('Contact deleted');
    }

    public function toggle($id)
    {
        $item = $this->service->toggleActive($id);
        if (!$item) ResponseMessage('Contact not found', 404);
        ResponseData($item);
    }
}

<?php

namespace App\Http\Controllers\API\v1\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(protected UserService $service){}

    public function index(Request $request)
    {
        $filters = [];
        if ($request->phone_number) {
            array_push($filters, ['phone_number' => $request->phone_number]);
        }
        if ($request->name) {
            array_push($filters, ['name' => $request->name]);
        }

        $data = $this->service->all(
            $filters,
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number',
            'password' => 'required|string|min:6',
            'is_active' => 'sometimes|boolean',
            'is_verified' => 'sometimes|boolean'
        ]);

        $user = $this->service->create($validated);
        ResponseData($user, 201);
    }

    public function show($id)
    {
        $user = $this->service->find($id);
        if (! $user) ResponseMessage('User not found', 404);
        ResponseData($user);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|unique:users,phone_number,' . $id,
            'password' => 'sometimes|string|min:6',
            'is_active' => 'sometimes|boolean',
            'is_verified' => 'sometimes|boolean'
        ]);

        $user = $this->service->update($id, $validated);
        if (! $user) ResponseMessage('User not found', 404);
        ResponseData($user);
    }

    public function destroy($id)
    {
        $deleted = $this->service->delete($id);
        if (! $deleted) ResponseMessage('User not found', 404);
        ResponseMessage('User deleted');
    }

    public function toggle($id)
    {
        $user = $this->service->toggleActive($id);
        if (! $user) ResponseMessage('User not found', 404);
        ResponseData($user);
    }
}

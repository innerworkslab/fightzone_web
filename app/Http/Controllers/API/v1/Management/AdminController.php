<?php

namespace App\Http\Controllers\API\v1\Management;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\AdminService;

class AdminController extends Controller
{
    public function __construct(protected AdminService $service){}

    public function index(Request $request)
    {
        $filters = [];
        if($request->username){
            array_push($filters, ['username'=>$request->username]);
        }
        if($request->name){
            array_push($filters, ['name'=>$request->name]);
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
            'username' => 'required|string|unique:admins,username',
            'password' => 'required|string|min:6',
            'is_active' => 'sometimes|boolean'
        ]);

        $admin = $this->service->create($validated);
        ResponseData($admin, 201);
    }

    public function show($id)
    {
        $admin = $this->service->find($id);
        if (! $admin) ResponseMessage('Admin not found', 404);
        ResponseData($admin);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|unique:admins,username,' . $id,
            'password' => 'sometimes|string|min:6',
            'is_active' => 'sometimes|boolean'
        ]);

        $admin = $this->service->update($id, $validated);
        if (! $admin) ResponseMessage('Admin not found', 404);
        ResponseData($admin);
    }

    public function destroy($id)
    {
        $deleted = $this->service->delete($id);
        if (! $deleted) ResponseMessage('Admin not found', 404);
        ResponseMessage('Admin deleted');
    }

    public function toggle($id)
    {
        $admin = $this->service->toggleActive($id);
        if (! $admin) ResponseMessage('Admin not found', 404);
        ResponseData($admin);
    }
}

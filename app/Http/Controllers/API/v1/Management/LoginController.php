<?php

namespace App\Http\Controllers\API\v1\Management;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\Auth\LoginService;

class LoginController extends Controller
{
    //
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:admins,username',
            'password' => 'required'
        ]);

        $data = (new LoginService())->login([
            'username' => $request->username,
            'password'=>$request->password
        ], 'admin');

        ResponseData($data);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        ResponseMessage("User logged out");
    }
}

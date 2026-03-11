<?php

namespace App\Http\Controllers\API\v1\Management\Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\Auth\LoginService;
use App\Services\ThirdParty\Firebase\StoreFcmTokenService;

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

        if($request->fcm_token){
            (new StoreFcmTokenService())->run($request->fcm_token, $data['user']['id'], 'user');
        }

        ResponseData($data);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        ResponseMessage("User logged out");
    }
}

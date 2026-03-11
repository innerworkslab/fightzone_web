<?php

namespace App\Http\Controllers\API\v1\User\Auth;

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
            'phone_number' => 'required|exists:users,phone_number',
            'password' => 'required'
        ]);

        $data = (new LoginService())->login([
            'phone_number' => $request->phone_number,
            'password'=>$request->password
        ], 'user');

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

<?php

namespace App\Http\Controllers\API\v1\User\Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\Auth\RegistrationService;
use App\Services\ThirdParty\Firebase\StoreFcmTokenService;

class RegisterController extends Controller
{
    //
    public function __construct(private RegistrationService $service) {}

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone_number' => 'required|unique:users,phone_number',
            'password' => 'required|confirmed|min:6'
        ]);

        $data = $request->all();        

        $user = $this->service->registerLocalUser($data);
        $success = $this->service->verifyPhoneNumber($request->phone_number, '000000', null, $data);

        $tokenData = $this->service->generateSanctumTokenFromPhoneNumber($request->phone_number);
        if($request->fcm_token){
            (new StoreFcmTokenService())->run($request->fcm_token, $tokenData['user']['id'], 'user');    
        }
        
        ResponseData($tokenData);
    }

    public function verifyRegistration(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|exists:users,phone_number',
            'otp' => 'required',
            'password' => 'required|confirmed'
        ]);

        $data['password'] = $request->password;

        $success = $this->service->verifyPhoneNumber($request->phone_number, $request->otp, null, $data);
        if($success){
            $tokenData = $this->service->generateSanctumTokenFromPhoneNumber($request->phone_number);
            (new StoreFcmTokenService())->run($request->fcm_token, $tokenData['user']['id'], 'user');
            ResponseData($tokenData);
        }
    }
}

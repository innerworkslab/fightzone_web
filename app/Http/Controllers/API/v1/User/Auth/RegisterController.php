<?php

namespace App\Http\Controllers\API\v1\User\Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\Auth\RegistrationService;
use App\Services\Auth\SanctumTokenService;

class RegisterController extends Controller
{
    //
    public function __construct(private RegistrationService $service) {}

    public function register(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|unique:users,phone_number',
            'password' => 'required|confirmed'
        ]);

        $user = $this->service->registerLocalUser($request->all());
        ResponseData($user);
    }

    public function verifyRegistration(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|exists:users,phone_number',
            'otp' => 'required'
        ]);

        $success = $this->service->verifyPhoneNumber($request->phone_number, $request->otp);
        if($success){
            $data = $this->service->generateSanctumTokenFromPhoneNumber($request->phone_number);
            ResponseData($data);
        }
    }
}

<?php

namespace App\Services\Auth;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use App\Models\User;

use App\Services\Auth\SanctumTokenService;

class RegistrationService
{
    public function registerLocalUser(array $data)
    {
        // 1. Create user
        $user = User::create($data);

        // 2. Generate OTP
        $user->generateOtp();

        // 3. Generate verification token
        $user->email_verification_token = Str::random(64);
        $user->save();

        // 4. Build verification link
        $link = url("/register/verify/{$user->id}/{$user->email_verification_token}");

        // 5. Send OTP via sms
        // here is the implmentation for SMS sending

        return $user;
    }

    public function verifyPhoneNumber($phone_number, $otp, ?string $token=null)
    {
        $user = User::where('phone_number', $phone_number)->first();

        if (! $user) {
            // abort(404, "User not found");
            ResponseMessage("User not found", 404, false);
        }

        if ($user->hasVerifiedAccount()) {
            // abort(400, "phone_number already verified");
            ResponseMessage("Phone number already verified");
        }

        $result = $user->verifyOtp($otp, $token);

        if ($result !== true) {
            // abort(400, $result);
            ResponseMessage($result, 400);
        }

        return true;
    }

    public function verifyPhoneNumberWithToken(int $id, string $token)
    {
        $user = User::find($id);
        if (! $user) {
            // abort(404, "User not found");
            ResponseMessage("User not found", 404, false);
        }
        if ($user->email_verification_token === $token){
            if($user->markAccountAsVerified()){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function generateSanctumTokenFromPhoneNumber(string $phone_number)
    {
        $user = User::where("phone_number", $phone_number)->first();
        if (! $user) {
            ResponseMessage("User not found", 404, false);
        }
        $token = (new SanctumTokenService())->generateSanctumToken($user);
        return [
            "user" => $user,
            "token" => $token
        ];
    }
}

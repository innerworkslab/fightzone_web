<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Admin;

use App\Services\Auth\SanctumTokenService;
use Illuminate\Http\Request;

class LoginService
{
    public function loginWtihPhoneNumber(string $phone_number, string $password)
    {
        $user = User::where('phone_number', $phone_number)->first();
        if(!$user)
            ResponseMessage("User not found", 404);
        if(!(Hash::check($password, $user->getAuthPassword()))){
            ResponseMessage("Password not match", 403);
        }
        $token = (new SanctumTokenService())->generateSanctumToken($user);
        return [
            "user" => $user,
            "token" => $token
        ];
    }

    /**
     * Generic login for multiple model types (user|admin).
     * For users: provide ['phone_number' => ..., 'password' => ...]
     * For admins: provide ['username' => ..., 'password' => ...]
     */
    public function login(array $credentials, string $type = 'user')
    {
        $model = null;
        switch (strtolower($type)) {
            case 'admin':
                if (empty($credentials['username']) || empty($credentials['password'])) {
                    ResponseMessage('Username and password are required', 400);
                }
                $model = Admin::where('username', $credentials['username'])->first();
                break;
            case 'user':
            default:
                if (empty($credentials['phone_number']) || empty($credentials['password'])) {
                    ResponseMessage('Phone number and password are required', 400);
                }
                $model = User::where('phone_number', $credentials['phone_number'])->first();
                break;
        }

        if (! $model) {
            ResponseMessage("Login user not found", 404);
        }

        if (! (Hash::check($credentials['password'], $model->getAuthPassword()))) {
            ResponseMessage('Password not match', 403);
        }

        $token = (new SanctumTokenService())->generateSanctumToken($model);

        return [
            'user' => $model,
            'token' => $token
        ];
    }
}

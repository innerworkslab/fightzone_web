<?php

namespace App\Http\Controllers\API\v1\User\Profile;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\ProfileService;

class ProfileController extends Controller
{
    protected ProfileService $service;

    public function __construct(ProfileService $service)
    {
        $this->service = $service;
    }

    /**
     * Get complete user profile with balance, recent deposits, and recent purchases
     */
    public function index(Request $request)
    {
        $userId = ApiUser()->id;
        $data = $this->service->getProfile($userId);

        ResponseData($data);
    }

    /**
     * Get user profile details only
     */
    public function show(Request $request)
    {
        $userId = ApiUser()->id;
        $user = $this->service->getProfileDetails($userId);

        ResponseData($user);
    }

    /**
     * Get user point balance
     */
    public function balance(Request $request)
    {
        $userId = ApiUser()->id;
        $balance = $this->service->getPointBalance($userId);

        ResponseData($balance);
    }

    /**
     * Get user deposit history
     */
    public function deposits(Request $request)
    {
        $userId = ApiUser()->id;
        $data = $this->service->getDepositHistory(
            $userId,
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }

    /**
     * Get user purchase history
     */
    public function purchases(Request $request)
    {
        $userId = ApiUser()->id;
        $data = $this->service->getPurchaseHistory(
            $userId,
            $request->page,
            $request->limit
        );

        ResponseData($data);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:6'
        ]);

        $userId = ApiUser()->id;
        $success = $this->service->updatePassword(
            $userId,
            $request->current_password,
            $request->new_password
        );
        ($success)? ResponseMessage("Password successfully updated"): ResponseMessage("Failed to update password. Please provide current password correctly", 403);
    }

    public function changeNameOrPhone(Request $request)
    {
        $request->validate([
            'password' => 'required',
            'name' => 'sometimes',
            'phone_number' => 'sometimes'
        ]);

        $nameUpdated = false;
        $phoneUpdated = false;

        $userId = ApiUser()->id;

        if($request->name){
            $nameUpdated = $this->service->updateProfileName(
                $userId,
                $request->password,
                $request->name
            );
            if(!$nameUpdated){
                ResponseMessage("Please provide current password correctly", 403);
            }
        }

        if($request->phone_number){
            if($this->service->isPhoneNumberTaken($userId, $request->phone_number))
                ResponseMessage("The phone number already taken by another user", 422);
            $phoneUpdated = $this->service->updatePhoneNumber(
                $userId,
                $request->password,
                $request->phone_number
            );
            if(!$phoneUpdated){
                ResponseMessage("Please provide current password correctly", 403);
            }
        }

        $msg = '';
        if($nameUpdated){
            $msg .= "Profile name ";
        }
        if($phoneUpdated){
            $msg .= "Phone number ";
        }
        $msg .= "updated.";

        ResponseMessage($msg);
    }

    public function getPurchasedClasses(Request $request)
    {
        return $this->service->fetchUserPurchasedCourseLevels(
            ApiUser()->id,
            $request->page,
            $request->limit
        );
    }
}

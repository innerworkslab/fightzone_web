<?php

namespace App\Services\ThirdParty\Firebase;

use Illuminate\Support\Facades\Log;

use App\Models\PersonFcmToken;

class StoreFcmTokenService
{
    public function run(string $token, int $personId, string $personableType)
    {
        if ($token && $token != "null") {
            $personToken = PersonFcmToken::firstOrCreate(
                [
                    'fcm_token' => $token,
                    'personable_id' => $personId,
                    'personable_type' => $personableType,

                ],
                [
                    'fcm_token' => $token,
                    'personable_id' => $personId,
                    'personable_type' => $personableType
                ]
            );
            return $personToken;
        }
    }
}

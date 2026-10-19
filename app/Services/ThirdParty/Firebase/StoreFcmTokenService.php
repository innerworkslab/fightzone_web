<?php

namespace App\Services\ThirdParty\Firebase;

use App\Models\PersonFcmToken;

class StoreFcmTokenService
{
    public function run(string $token, int $personId, string $personableType)
    {
        if ($token && $token != "null") {
            return PersonFcmToken::updateOrCreate(
                ['fcm_token' => $token],
                [
                    'personable_id' => $personId,
                    'personable_type' => $personableType,
                ]
            );
        }
    }
}

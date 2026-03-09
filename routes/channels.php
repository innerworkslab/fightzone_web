<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| Private channel for user-specific events (e.g. walk-in confirmed).
| Client app subscribes with auth:api (Sanctum) to receive real-time updates.
*/

Broadcast::routes(['middleware' => ['auth:api']]);

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
}
);

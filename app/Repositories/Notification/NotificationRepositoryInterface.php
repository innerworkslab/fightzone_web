<?php

namespace App\Repositories\Notification;

use Illuminate\Http\Request;

interface NotificationRepositoryInterface
{
    public function getNotifications($personId, $personType, $status, ?int $limit=null);

    public function markNotificationAsRead(int $id);
}

<?php

namespace App\Http\Controllers\API\v1\User\Profile;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\NotificationService;

class NotificationController extends Controller
{
    //
    public function __construct(protected NotificationService $service)
    {

    }

    public function index(Request $request)
    {
        $notifications = $this->service->getNotifications(
            ApiUser()->id,
            'user',
            $request->status,
            $request->page,
            $request->limit
        );

        ResponseData($notifications);
    }

    public function getUnreadCount()
    {
        $count = $this->service->getUnreadNotificationCount(
            ApiUser()->id,
            'user'
        );

        ResponseData([
            'total_unread_noti_count' => $count
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $this->service->markAsRead(
            $id, 
            ApiUser()->id,
            'user'
        );

        ResponseMessage("OK");
    }

    public function markAllAsRead(Request $request)
    {
        $request->validate([
            'ids' => 'required'
        ]);

        $ids = (gettype($request->ids) == 'array')? $request->ids: json_decode($request->ids);
        foreach ($ids as $key => $id) {
            // code...
            $this->service->markAsRead(
                $id,
                ApiUser()->id,
                'user'
            );
        }
        ResponseMessage("OK");
    }
}

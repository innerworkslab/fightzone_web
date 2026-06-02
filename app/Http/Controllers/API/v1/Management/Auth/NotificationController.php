<?php

namespace App\Http\Controllers\API\v1\Management\Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Services\NotificationService;

use App\Services\ThirdParty\Firebase\FirebaseNotificationService;

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
            'admin',
            $request->status,
            $request->page,
            $request->limit
        );

        ResponseData($notifications);
    }

    public function testNoti(Request $request)
    {
	(new FirebaseNotificationService(\App\Models\Admin::first(), \App\Models\Admin::all(), ApiUser()->id, 'admin'))
            ->send([
                'title' => "Test noti",
                'preview' => "This is test noti"
            ]);

         ResponseMessage("Test noti broadcasted");
    }

    public function getUnreadCount()
    {
        $count = $this->service->getUnreadNotificationCount(
            ApiUser()->id,
            'admin'
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
            'admin'
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
                'admin'
            );
        }
        ResponseMessage("OK");
    }
}

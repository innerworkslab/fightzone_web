<?php

namespace App\Repositories\Notification;

use Illuminate\Database\Eloquent\Relations\Relation;

use App\Models\NotificationPerson;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function getNotifications($personId, $personType, $status, ?int $limit=null)
    {
        $model = Relation::getMorphedModel($personType);
        $person = $model::find($personId);
        $query = NotificationPerson::with([
            'personable',
        ])
            ->join('notifications', 'notification_people.notification_id', 'notifications.id')
            ->where('notification_people.personable_id', $personId)
            ->where('notification_people.personable_type', $personType) // Filter for 'user' personable_type here
            ->select(
                'notification_people.id as personalized_notification_id',
                'notifications.id as notification_id',
                'is_read',
                'is_read_count',
                'notifications.title',
                'notifications.preview',
                'notifications.date_time',
                // 'notifications.meta_data',
                'notification_people.personable_type',
                'notification_people.personable_id',
                'notifications.notificationable_id',
                'notifications.notificationable_type',
            )
            ->orderBy('notification_people.id', 'desc');

        if($status == 'read'){
            $query->where('notification_people.is_read', 1);
        }
        if($status == 'unread'){
            $query->where('notification_people.is_read', 0);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function markNotificationAsRead(int $id)
    {
        $noti = NotificationPerson::find($id);
        if($noti){
            $noti->is_read = 1;
            $noti->is_read_count += 1;
            $noti->read_at = CurrentTime();
            $noti->save();
        }
        return true;
    }
}

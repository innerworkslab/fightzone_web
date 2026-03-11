<?php

namespace App\Services;

use App\Repositories\Notification\NotificationRepositoryInterface;

class NotificationService
{
    public function __construct(protected NotificationRepositoryInterface $repo)
    {

    }

    public function getNotifications(int $personId, ?string $personType=null, ?string $status=null, ?int $page, ?int $limit=null)
    {
        $query = $this->repo->getNotifications(
            $personId,
            ($personType)? $personType: 'user',
            $status,
            $limit
        );

        return $page
            ? $query->paginate($limit ?? config('common.list_count'))
            : $query->get();
    }

    public function getUnreadNotificationCount(int $personId, ?string $personType=null)
    {
        $query = $this->repo->getNotifications(
            $personId,
            ($personType)? $personType: 'user',
            "unread",
            null
        );

        return $query->count();
    }

    public function markAsRead(int $id)
    {
        return $this->repo->markNotificationAsRead($id);
    }
}

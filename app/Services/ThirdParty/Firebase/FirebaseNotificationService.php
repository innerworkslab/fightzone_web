<?php

namespace App\Services\ThirdParty\Firebase;

use Exception;
use InvalidArgumentException;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as NotificationQueue;
use Illuminate\Support\Facades\Log;

use App\Models\Notification;
use App\Models\NotificationPerson;

use App\Services\ThirdParty\Firebase\BroadcastFcmMessageService;

class FirebaseNotificationService
{
    protected  $model;
    protected string $modelType;
    protected  $people;
    protected int $creatorId;
    protected string $creatorType;

    private function getMorphName($model)
    {
        if($model)
            return array_search(get_class($model), Relation::morphMap());
        else
            return null;
    }

    public function __construct($model, $people, int $creatorId, string $creatorType)
    {
        $this->model = $model;
        $this->modelType = $this->getMorphName($model);

        $this->creatorId = $creatorId;
        $this->creatorType = $creatorType;
        $this->people = $this->normalizePeople($people);
    }

    /**
     * Send Notification
     */
    public function send(array $data): Notification
    {
        if(!isset($data['date_time'])){
            $data['date_time'] = now();
        }
        if(!isset($data['meta_data'])){
            $data['meta_data'] = [];
        }
        $notification = Notification::create([
            'title' => $data['title'],
            'preview' => $data['preview'],
            'type' => isset($data['type'])? $data['type'] : 'general',
            'date_time' => $data['date_time'],
            'meta_data' => $data['meta_data'],
            'notificationable_id' => ($this->model)? $this->model->id: null,
            'notificationable_type' => $this->modelType,
            'createdable_id' => $this->creatorId,
            'createdable_type' => $this->creatorType,
        ]);
        $notificationPersons = [];
        foreach ($this->people as $person) {
            $notificationPersons[] = [
                'notification_id' => $notification->id,
                'personable_id' => $person->id,
                'personable_type' => $this->getMorphName($person),
            ];
        }
        NotificationPerson::insert($notificationPersons);
        NotificationQueue::send($this->people, new BroadcastFcmMessageService($data));
        return $notification;
    }

    /**
     * Normalize people to Collection
     */

    private function normalizePeople($people): Collection
    {
        if ($people instanceof Model) {
            return new Collection([$people]);
        } elseif (is_array($people)) {
            return new Collection($people);
        } elseif ($people instanceof Collection) {
            return $people;
        }

        throw new InvalidArgumentException("People must be Model, array, or Collection");
    }
}

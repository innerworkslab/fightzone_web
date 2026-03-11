<?php

namespace App\Services\ThirdParty\Firebase;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class BroadcastFcmMessageService extends Notification implements ShouldQueue
{
    use Queueable;

    private $title;
    private $body;
    private $date_time;
    private $extraData = [];
    private $image;
    private $fcmMessageControls = [];

    public function __construct(array $data, array $fcmMessageControls=[] ){
        $this->title=$data['title'];
        $this->body=$data['preview'];
        if(isset($data['image'])){
            $this->image=$data['image'];
        }
        $this->date_time=$data['date_time'];
        if(isset($data['meta_data'])){
            $this->extraData = $data['meta_data'];
        }
        if(count($fcmMessageControls) > 0){
            $this->fcmMessageControls = $fcmMessageControls;
        }
    }

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable): FcmMessage
    {
        return (new FcmMessage(notification: new FcmNotification(
            title: $this->title,
            body: $this->body,
            image: $this->image
        )))
        ->data($this->extraData)
        ->custom([
            'android' => [
                'notification' => [
                    'color' => '#0A0A0A',
                    'sound' => 'default',
                ],
                'fcm_options' => [
                    'analytics_label' => 'analytics',
                ],
            ],
            'apns' => [
                'payload' => [
                    'aps' => [
                        'sound' => 'default'
                    ],
                ],
                'fcm_options' => [
                    'analytics_label' => 'analytics',
                ],
            ],
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use LaravelFCM\Facades\FCM;
// use LaravelFCM\Message\OptionsBuilder;
// use LaravelFCM\Message\PayloadDataBuilder;
// use LaravelFCM\Message\PayloadNotificationBuilder;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable=[
        'title',
        'preview',
        'notificationable_id',
        'notificationable_type',
        'createdable_id',
        'createdable_type',
        'date_time'
    ];

    public function notificationPerson()
    {
        return $this->hasMany(\App\Models\NotificationPerson::class);
    }
}

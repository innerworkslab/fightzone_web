<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPerson extends Model
{
    protected $fillable=[
        'is_read',
        'is_read_count',
        'read_at',
        'personable_id',
        'personable_type',
        'notification_id'
    ];

    public function personable()
    {
        return $this->morphTo();
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}

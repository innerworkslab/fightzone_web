<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonFcmToken extends Model
{
    protected $fillable=['fcm_token','personable_id','personable_type'];
}

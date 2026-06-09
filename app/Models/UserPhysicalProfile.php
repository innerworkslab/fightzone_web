<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPhysicalProfile extends Model
{
    protected $fillable = [
        'user_id',
        'stance',
        'rope_jump_level',
        'fitness_level',
        'boxing_level',
        'weight',
        'gender'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

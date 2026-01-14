<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'price',
        'days',
        'is_active'
    ];

    protected $casts = [
        'days' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];
}

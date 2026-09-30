<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'price',
        'days',
        'validity_months',
        'is_active'
    ];

    protected $casts = [
        'days' => 'integer',
        'validity_months' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];
}

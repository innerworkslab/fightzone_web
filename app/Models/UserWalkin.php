<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWalkin extends Model
{
    //
    protected $fillable = [
        'package_purchase_id',
        'walkin_at',
        'confirmed_at',
    ];

    protected $casts = [
        'walkin_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function packagePurchase()
    {
        return $this->belongsTo(PackagePurchase::class);
    }
}

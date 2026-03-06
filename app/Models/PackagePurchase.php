<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagePurchase extends Model
{
    //
    protected $fillable = [
        'user_id',
        'package_id',
        'purchase_id',
        'total_days',
        'remaining_days',
        'completed'
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function userWalkins()
    {
        return $this->hasMany(UserWalkin::class);
    }

    /** Scope: has remaining days and not completed */
    public function scopeValid($query)
    {
        return $query->where('remaining_days', '>', 0)->where('completed', false);
    }
}

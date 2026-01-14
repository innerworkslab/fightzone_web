<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'purchasable_type',
        'purchasable_id',
        'quantity',
        'unit_price',
        'total_points',
        'status',
        'admin_id',
        'admin_note',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'unit_price' => 'decimal:2',
        'total_points' => 'integer',
        'quantity' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Polymorphic relationship to the purchased item (Package, Course, etc.)
     */
    public function purchasable()
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}

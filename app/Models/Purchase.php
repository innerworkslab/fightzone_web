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
        'certificate_path',
        'note',
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

    protected $appends = ['certificate_url', 'valid_from', 'valid_until', 'is_within_validity'];

    public function getCertificateUrlAttribute()
    {
        return $this->certificate_path
            ? \Illuminate\Support\Facades\Storage::url($this->certificate_path)
            : null;
    }


    public function courseLevelPurchase()
    {
        return $this->hasOne(CourseLevelPurchase::class);
    }

    public function packagePurchase()
    {
        return $this->hasOne(PackagePurchase::class);
    }

    public function getValidFromAttribute()
    {
        return $this->validityPurchase()?->valid_from;
    }

    public function getValidUntilAttribute()
    {
        return $this->validityPurchase()?->valid_until;
    }

    public function getIsWithinValidityAttribute(): bool
    {
        $purchase = $this->validityPurchase();

        if (! $purchase?->valid_from || ! $purchase?->valid_until) {
            return false;
        }

        return now()->between($purchase->valid_from, $purchase->valid_until);
    }

    private function validityPurchase()
    {
        return $this->courseLevelPurchase ?? $this->packagePurchase;
    }

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

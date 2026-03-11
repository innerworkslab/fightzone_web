<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    //
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'username',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean'
        ];
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    public function permissionType()
    {
        return $this->belongsTo(PermissionType::class);
    }

    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()->whereHas('permissionType', function ($query) use ($permissionName) {
            $query->where('name', $permissionName);
        })->exists();
    }

    public function personTokens()
    {
        return $this->morphMany(PersonFcmToken::class,'personable');
    }

    public function routeNotificationForFcm()
    {
        return $this->personTokens()->pluck('fcm_token')->toArray();
    }
}

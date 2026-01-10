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
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
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
}

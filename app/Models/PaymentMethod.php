<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'holder',
        'account_number',
        'logo_path',
        'is_active'
    ];

    protected $hidden = [
        'logo_path'
    ];

    protected $appends = [
        'logo_url'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function getLogoPath()
    {
        return $this->logo_path;
    }

    public function getLogoUrlAttribute()
    {
        if($this->logo_path){
            return Storage::url($this->logo_path);
        }
        return null;
    }
}

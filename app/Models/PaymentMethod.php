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
        'qr_path',
        'is_active'
    ];

    protected $hidden = [
        'logo_path',
        'qr_path'
    ];

    protected $appends = [
        'logo_url',
        'qr_url'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function getLogoPath()
    {
        return $this->logo_path;
    }

    public function getQrPath()
    {
        return $this->qr_path;
    }

    public function getLogoUrlAttribute()
    {
        if($this->logo_path){
            return Storage::url($this->logo_path);
        }
        return null;
    }

    public function getQrUrlAttribute()
    {
        if ($this->qr_path) {
            return Storage::url($this->qr_path);
        }
        return null;
    }
}

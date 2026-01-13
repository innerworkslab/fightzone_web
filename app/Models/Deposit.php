<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_method_id',
        'transaction_id',
        'amount',
        'screenshot_path',
        'status',
        'admin_id',
        'admin_note',
        'confirmed_at',
    ];

    protected $hidden = [
        'screenshot_path'
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    protected $appends = [
        'screenshot_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function getScreenshotUrlAttribute()
    {
        if($this->screenshot_path){
            return Storage::url($this->screenshot_path);
        }
        return null;
    }

    public function getScreenshotPath()
    {
        return $this->screenshot_path;
    }
}

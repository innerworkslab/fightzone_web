<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone_number',
        'password',
        'stripe_customer_id',
        'provider',      // e.g. 'google', 'facebook', or 'local'
        'provider_id',   // social platform user ID
        'profile_image_path',
        'email_verification_token',
        'email_verified_at',
        'otp_code',
        'otp_expires_at',
        'is_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
        'otp_expires_at',
        'is_verified',
        'email_verification_token',
        'profile_image_path'
    ];

    protected $appends = ['profile_image_url'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'otp_expires_at'    => 'datetime',
            'is_verified'       => 'boolean',
            'is_active'         => 'boolean'
        ];
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Generate a 6-digit OTP and set expiry time.
     */
    public function generateOtp(int $minutes = 10): string
    {
        // if ($this->otp_expires_at && $this->otp_expires_at->isFuture()) {
        //     $remaining = $this->otp_expires_at->diffInSeconds(now());
        //     throw new \Exception("Please wait {$remaining} seconds before requesting a new OTP.");
        // }
        $this->otp_code = rand(100000, 999999);
        $this->otp_code = "000000";
        $this->otp_expires_at = now()->addMinutes($minutes);
        $this->email_verification_token = Str::random(64);
        $this->save();

        return $this;
    }

    /**
     * Verify an OTP code.
     *
     * @return bool|string  Returns true if verified, or an error message string.
     */
    public function verifyOtp(?string $otp, ?string $token=null)
    {
        if(!$otp && !$token){
            return 'OTP or token must present';
        }
        if ($otp && !$this->otp_code) {
            return 'No OTP was generated for this account.';
        }
        if ($token && !$this->email_verification_token) {
            return 'No verification token was generated for this account.';
        }

        if ($this->otp_expires_at->isPast()) {
            return 'OTP has expired.';
        }

        if ($otp && ($this->otp_code !== $otp)) {
            return 'Invalid OTP code.';
        }

        if ($token && ($this->email_verification_token !== $token)) {
            return 'Invalid token.';
        }

        $this->update([
            'is_verified' => true,
            'otp_code' => null,
            'otp_expires_at' => null,
            'email_verification_token' => null,
            'email_verified_at' => now()
        ]);

        return true;
    }

    /**
     * Helper to check if user is verified.
     */
    public function hasVerifiedAccount(): bool
    {
        return $this->is_verified === true;
    }

    public function markAccountAsVerified(): bool
    {
        if ($this->hasVerifiedAccount()) {
            return false;
        }

        if ($this->otp_expires_at->isPast()) {
            return false;
        }

        return $this->forceFill([
            'is_verified' => true,
            'otp_code' => null,
            'otp_expires_at' => null,
            'email_verification_token' => null,
            'email_verified_at' => now(),
        ])->save();
    }

    public function getProfileImageUrlAttribute()
    {
        if($this->profile_image_path){
            return Storage::url($this->profile_image_path);
        }
        return null;
    }
}

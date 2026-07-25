<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'is_active',
        'plan',
        'trial_ends_at',
    ];



    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isMerchant()
    {
        return $this->role === 'merchant';
    }




    public function enable2FA()
    {
        $this->google2fa_secret = app('pragmarx.google2fa')->generateSecretKey();
        $this->google2fa_enabled = true;
        $this->save();
    }

    public function disable2FA()
    {
        $this->google2fa_secret = null;
        $this->google2fa_enabled = false;
        $this->save();
    }

    public function get2FAQRCode()
    {
        if (!$this->google2fa_secret) return null;

        return app('pragmarx.google2fa')->getQRCodeInline(
            'Caravane',
            $this->email,
            $this->google2fa_secret
        );
    }
}

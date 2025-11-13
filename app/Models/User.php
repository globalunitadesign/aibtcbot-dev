<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'account',
        'password',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function username()
    {
        return 'account';
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'user_id', 'id');
    }

    public function avatars()
    {
        return $this->hasMany(Avatar::class, 'owner_id', 'id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function otp()
    {
        return $this->hasOne(UserOtp::class, 'user_id', 'id');
    }

    public function kyc()
    {
        return $this->hasOne(KycVerification::class, 'user_id', 'id');
    }
}

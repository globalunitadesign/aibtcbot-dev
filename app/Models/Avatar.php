<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Avatar extends Authenticatable
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'owner_id',
        'name',
        'is_active',
    ];

    public function member()
    {
        return $this->hasOne(Member::class, 'avatar_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }
}

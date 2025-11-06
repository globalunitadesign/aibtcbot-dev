<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = ['type', 'content'];

    protected $casts = [
        'content' => 'array',
    ];

    public $timestamps = true;
}

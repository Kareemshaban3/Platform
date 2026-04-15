<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityAlert extends Model
{
    protected $fillable = [
        'user_id',
        'attempted_device_id',
        'ip_address',
        'user_agent',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

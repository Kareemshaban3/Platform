<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reviews extends Model
{
    protected $fillable = ['user_id', 'rating', 'comment'];


    public function user() {
        return $this->belongsTo(User::class);
    }
}

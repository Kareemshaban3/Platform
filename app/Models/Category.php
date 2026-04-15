<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'type', 'description', 'price', 'time', 'lesson_count', 'image'];


    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'categories_id');
    }

      public function users()
    {
        return $this->belongsToMany(User::class);
    }
 
}

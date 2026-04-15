<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = ['categories_id', 'name', 'description', 'path', 'is_main', 'file_type'];
    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }
    protected $hidden = ['categories_id'];
    protected $appends = ['full_path'];

    public function getFullPathAttribute()
    {
        return $this->path ? asset('storage/' . $this->path) : null;
    }
}

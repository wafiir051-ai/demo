<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['title', 'description', 'image_path', 'category', 'alt_text', 'status', 'sort_order'];

    public function scopePublished($q)
    {
        return $q->where('status', 'published')->orderBy('sort_order')->orderBy('id');
    }
}

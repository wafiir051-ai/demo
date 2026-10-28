<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'icon', 'image_path', 'status', 'sort_order'];

    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class);
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published')->orderBy('sort_order')->orderBy('id');
    }
}

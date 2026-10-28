<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accommodation extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'capacity', 'bed_description', 'price_per_night', 'featured_image', 'status', 'sort_order'];

    public function facilities()
    {
        return $this->belongsToMany(Facility::class)->orderBy('sort_order');
    }

    public function images()
    {
        return $this->hasMany(AccommodationImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published')->orderBy('sort_order')->orderBy('id');
    }
}

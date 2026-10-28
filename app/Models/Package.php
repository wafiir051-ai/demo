<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    public const TYPES = [
        'gathering' => 'Gathering', 'family_event' => 'Acara Keluarga', 'corporate' => 'Acara Perusahaan',
        'stay' => 'Paket Menginap', 'promo' => 'Promo', 'other' => 'Lainnya',
    ];

    protected $fillable = ['name', 'slug', 'description', 'package_type', 'capacity', 'price', 'featured_image', 'terms', 'status'];

    public function items()
    {
        return $this->hasMany(PackageItem::class)->orderBy('sort_order');
    }

    public function scopePublished($q)
    {
        return $q->where('status', 'published')->orderBy('id');
    }
}

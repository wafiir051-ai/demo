<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['package_id', 'label', 'sort_order'];
}

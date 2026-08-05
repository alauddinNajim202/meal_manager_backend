<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $guarded = [];

    public function properties()
    {
        return $this->belongsToMany(Property::class);
    }
}

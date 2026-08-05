<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $guarded = [];

    public function property()
    {
        return $this->hasOne(Property::class);
    }

}

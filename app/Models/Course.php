<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $guarded = [];

    public function curricula()
    {
        return $this->hasMany(Curriculum::class);
    }
}

<?php

namespace Modules\Portfolio\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}

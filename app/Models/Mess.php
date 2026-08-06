<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mess extends Model
{
    protected $fillable = [
        'name',
        'address',
        'image'
    ];
    
}

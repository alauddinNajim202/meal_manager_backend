<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'type',
        'slug',
        'url',
        'name',
        'description',
        'icon',
        'order',
        'parent_id',
        'status'
    ];

    protected $appends = [
        'sn'
    ];

    public function getSnAttribute()
    {
        return $this->id + $this->parent_id ?? 0;
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }
    
}

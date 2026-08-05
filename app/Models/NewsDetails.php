<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsDetails extends Model
{
    //
    protected $gurded = [];


    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function images()
    {
        return $this->hasMany(NewsDetailsImage::class);
    }
}

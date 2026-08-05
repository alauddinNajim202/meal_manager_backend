<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    //
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(NewsDetails::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }


    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function dislikes()
    {
        return $this->hasMany(Dislike::class);
    }

    public function readers()
    {
        return $this->belongsToMany(User::class, 'news_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dislike extends Model
{
    //
    protected $table = 'news_dislikes';

    protected $fillable = [
        'news_id', 'user_id'
    ];
}

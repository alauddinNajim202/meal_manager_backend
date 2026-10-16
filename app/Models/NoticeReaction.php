<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoticeReaction extends Model
{
    protected $guarded = ['id'];

    public function notice()
    {
        return $this->belongsTo(Notice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}

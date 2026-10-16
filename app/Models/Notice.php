<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $guarded = ['id'];

    public function mess()
    {
        return $this->belongsTo(Mess::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function reactions()
    {
        return $this->hasMany(NoticeReaction::class);
    }

    public function reactedUsers()
    {
        return $this->belongsToMany(User::class, 'notice_reactions')
            ->withTrashed()
            ->withPivot('reaction', 'created_at')
            ->withTimestamps();
    }
}

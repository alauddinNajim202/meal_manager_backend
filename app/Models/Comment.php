<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    protected $guarded = [];

    protected $appends = [
        'reaches_count',
        'humanize_date'
    ];

    public function getReachesCountAttribute()
    {
        return Reach::where('reachable_id', $this->id)->where('reachable_type', self::class)->count();
    }

    public function getHumanizeDateAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function reaches(): MorphMany
    {
        return $this->morphMany(Reach::class, 'reachable');
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }
    public function news()
    {
        return $this->belongsTo(News::class, 'news_id');
    }


}

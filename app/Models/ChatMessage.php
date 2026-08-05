<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'session_id',
        'role',
        'message',
        'outcome',
        'reason_code',
        'category',
        'crypstore_url',
    ];

    public function session()
    {
        return $this->belongsTo(ChatSession::class, 'session_id', 'id');
    }


}

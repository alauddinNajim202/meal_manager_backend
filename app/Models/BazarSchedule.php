<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BazarSchedule extends Model
{
    protected $fillable = [
        'mess_id',
        'user_id',
        'date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mess()
    {
        return $this->belongsTo(Mess::class);
    }
}

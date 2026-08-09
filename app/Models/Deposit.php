<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = [
        'mess_id',
        'user_id',
        'amount',
        'date',
        'method',
        'note',
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

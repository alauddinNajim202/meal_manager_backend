<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded = [];

    public function mess()
    {
        return $this->belongsTo(Mess::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

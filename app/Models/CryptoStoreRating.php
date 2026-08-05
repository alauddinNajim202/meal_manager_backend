<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoStoreRating extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cryptoStore()
    {
        return $this->belongsTo(CryptoStore::class);
    }
}

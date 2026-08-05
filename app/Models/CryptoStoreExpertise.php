<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoStoreExpertise extends Model
{
    protected $guarded = [];

    public function cryptoStore()
    {
        return $this->belongsTo(CryptoStore::class);
    }
}

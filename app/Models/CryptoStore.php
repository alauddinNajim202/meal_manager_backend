<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class CryptoStore extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($store) {
            $store->slug = static::generateUniqueSlug($store->name);
        });
    }

    private static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }

    public function ratings()
    {
        return $this->hasMany(CryptoStoreRating::class);
    }


    public function supported_ecosystems()
    {
        return $this->hasMany(SupportedEcosystems::class);
    }
    public function verification_audits()
    {
        return $this->hasMany(VerificationAndAudit::class);
    }

    public function expertises()
    {
        return $this->hasMany(CryptoStoreExpertise::class);
    }

}

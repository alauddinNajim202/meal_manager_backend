<?php

namespace App\Traits;

use App\Enums\CacheEnum;
use App\Models\CMS;
use Illuminate\Support\Facades\Cache;


trait CMSData
{
    public static function all()
    {
        return Cache::rememberForever(CacheEnum::CMS_DATA, function () {
            return CMS::where('status', 'active')->get();
        });
    }
}

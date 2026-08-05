<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AdminCategoryOverride extends Model
{
    use HasUuids;
    protected $fillable = [
        'question_pattern',
        'forced_category',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];
}

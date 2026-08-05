<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AdminBlockRules extends Model
{
    use HasUuids;
    protected $table = 'admin_block_rules';

    protected $fillable = [
        'pattern',
        'reason',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

}

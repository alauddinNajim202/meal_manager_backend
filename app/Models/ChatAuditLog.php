<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ChatAuditLog extends Model
{
    use HasUuids;


    protected $fillable = [
        'timestamp',
        'language',
        'category',
        'outcome',
        'reason_code',
        'agent_type',
        'policy_result',
        'question_fingerprint',

    ];
}

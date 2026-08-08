<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    protected $fillable = [
        'mess_id',
        'user_id',
        'date',
        'breakfast',
        'lunch',
        'dinner',
        'is_guest',
    ];

    protected $casts = [
        'date'      => 'date',
        'breakfast' => 'decimal:1',
        'lunch'     => 'decimal:1',
        'dinner'    => 'decimal:1',
        'is_guest'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mess()
    {
        return $this->belongsTo(Mess::class);
    }

    /**
     * Calculate total meals for this record.
     */
    public function getTotalAttribute(): float
    {
        return (float) $this->breakfast + (float) $this->lunch + (float) $this->dinner;
    }
}

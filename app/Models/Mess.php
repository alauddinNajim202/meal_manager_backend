<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mess extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'image',
    ];

    /**
     * All users belonging to this mess (manager + members).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'mess_user')
            ->withPivot('role', 'status')
            ->withTimestamps();
    }

    /**
     * Only the manager(s) of this mess.
     */
    public function managers()
    {
        return $this->belongsToMany(User::class, 'mess_user')
            ->wherePivot('role', 'manager')
            ->withPivot('role', 'status')
            ->withTimestamps();
    }

    /**
     * Only the members of this mess.
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'mess_user')
            ->wherePivot('role', 'member')
            ->withPivot('role', 'status')
            ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mess extends Model
{
   
    protected $fillable = [
        'name',
        'address',
        'image',
    ];

     public function getImageAttribute($value): string|null
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*') && !empty($value)) {
            return url($value);
        }

        return $value;
    }





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

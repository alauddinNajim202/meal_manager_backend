<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMonthlyBill extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function monthlyReport()
    {
        return $this->belongsTo(MonthlyReport::class);
    }
}

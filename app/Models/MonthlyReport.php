<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    protected $fillable = [
        'mess_id',
        'month',
        'year',
        'total_meals',
        'total_cost',
        'meal_rate',
        'status',
        // Optional / user customized fields
        'month_id',
        'total_bazar',
        'total_deposit',
        'total_expense',
        'total_due',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'total_meals' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'meal_rate' => 'decimal:2',
        'total_bazar' => 'decimal:2',
        'total_deposit' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'total_due' => 'decimal:2',
    ];

    public function mess()
    {
        return $this->belongsTo(Mess::class);
    }

    public function userMonthlyBills()
    {
        return $this->hasMany(UserMonthlyBill::class, 'monthly_report_id');
    }

    public function month()
    {
        return $this->belongsTo(Month::class, 'month', 'id');
    }
}

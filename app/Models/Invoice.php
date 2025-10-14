<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Invoice extends Model
{
     protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'total_days',
        'total_hours',
        'total_daily_pay',
        'total_overtime_pay',
        'commission_descriptions',
        'commission_quantity',
        'total_commission',
        'deduction_descriptions',
        'total_deduction',
        'gross_pay',
        'net_pay',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

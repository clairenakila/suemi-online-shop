<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;


class Attendance extends Model
{
    protected $fillable =[
        'date',
        'user_id',
        'time_in',
        'time_out',
        'work_shift_status',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

     // Attendance belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTimeInAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('h:i A') : null;
    }

    /**
     * Accessor for formatted time_out (e.g. 5:15 PM)
     */
    public function getTimeOutAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('h:i A') : null;
    }
}

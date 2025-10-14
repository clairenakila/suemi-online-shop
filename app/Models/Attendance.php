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
        'total_days',
        'total_hours',
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

    //for workshift status
     protected static function booted()
    {
        static::saving(function ($attendance) {
            // Default values
            $attendance->total_days = 0;
            $attendance->total_hours = 0;

            switch ($attendance->work_shift_status) {
                case 'Whole Day':
                    $attendance->total_days = 1;
                    $attendance->total_hours = 0;
                    break;

                case 'Half Day':
                    $attendance->total_days = 0.5;
                    $attendance->total_hours = 0;
                    break;

                case 'Overtime':
                    $attendance->total_days = 0;
                    if ($attendance->time_in && $attendance->time_out) {
                        $timeIn = Carbon::parse($attendance->time_in);
                        $timeOut = Carbon::parse($attendance->time_out);
                        $attendance->total_hours = $timeIn->diffInHours($timeOut);
                    }
                    break;

                case 'Absent':
                    $attendance->total_days = 0;
                    $attendance->total_hours = 0;
                    break;

                default:
                    // fallback in case work_shift_status is empty or unknown
                    $attendance->total_days = 0;
                    $attendance->total_hours = 0;
                    break;
            }
        });
    }
}

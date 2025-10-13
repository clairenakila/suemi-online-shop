<?php

namespace App\Livewire\Filament;

use Livewire\Component;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class Payslips extends Component
{
    public $user;
    public $attendances;
    public $startDate;
    public $endDate;
    public $totalDays = 0;
    public $totalHours = 0;

     public function mount($user_id, $start_date, $end_date)
    {
        $this->user = User::find($user_id);
        $this->startDate = $start_date;
        $this->endDate = $end_date;

        $this->attendances = Attendance::where('user_id', $user_id)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

        $this->totalDays = $this->attendances->sum('total_days');
        $this->totalHours = $this->attendances->sum('total_hours');
    }

    public function render()
    {
        return view('livewire.filament.payslips');
    }
}

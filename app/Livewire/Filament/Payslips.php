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
    public $commissions = []; // list of added commissions
    public $newCommission = [
        'description' => '',
        'quantity' => 1,
        'price' => 0,
    ];
    public $showCommissionModal = false;

    

      public function mount($user_id, $start_date, $end_date)
    {
        // Find user
        $this->user = User::find($user_id);

        // Store start & end dates
        $this->startDate = $start_date;
        $this->endDate = $end_date;

        // Fetch attendances in the date range
        $this->attendances = Attendance::where('user_id', $user_id)
            ->whereBetween('date', [$start_date, $end_date])
            ->get();

        // Calculate totals
        $this->totalDays = $this->attendances->sum(fn($a) => (float) $a->total_days);
        $this->totalHours = $this->attendances->sum(fn($a) => (float) $a->total_hours);
    }

    public function addCommission()
    {
        $this->validate([
            'newCommission.description' => 'required|string',
            'newCommission.quantity' => 'required|numeric|min:1',
            'newCommission.price' => 'required|numeric|min:0',
        ]);

        $this->commissions[] = [
            'description' => $this->newCommission['description'],
            'quantity' => $this->newCommission['quantity'],
            'price' => $this->newCommission['price'],
            'total' => $this->newCommission['quantity'] * $this->newCommission['price'],
        ];

        // Reset modal input
        $this->newCommission = ['description' => '', 'quantity' => 1, 'price' => 0];
        $this->showCommissionModal = false;
    }

    public function render()
    {
        return view('livewire.filament.payslips', [
            'totalDays' => $this->totalDays,
            'totalHours' => $this->totalHours,
        ]);
    }
}

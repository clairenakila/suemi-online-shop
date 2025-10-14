<?php

namespace App\Livewire\Filament;

use Livewire\Component;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Invoice;

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

    public $deductions = []; // list of added deductions
    public $newDeduction = [
        'description' => '',
        'amount' => 0,
    ];
    public $showDeductionModal = false;
    

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

    public function addDeduction()
    {
        $this->validate([
            'newDeduction.description' => 'required|string',
            'newDeduction.amount' => 'required|numeric|min:0',
        ]);

        $this->deductions[] = [
            'description' => $this->newDeduction['description'],
            'amount' => $this->newDeduction['amount'],
        ];

        // Reset modal input
        $this->newDeduction = ['description' => '', 'amount' => 0];
        $this->showDeductionModal = false;
    }
    
    public function createInvoice()
{
    // Compute total commissions and deductions
    $totalCommission = collect($this->commissions)->sum('total');
    $commissionDescriptions = collect($this->commissions)->pluck('description')->implode(', ');
    $commissionQuantity = collect($this->commissions)->sum('quantity');

    $totalDeduction = collect($this->deductions)->sum('amount');
    $deductionDescriptions = collect($this->deductions)->pluck('description')->implode(', ');

    // Compute gross and net pay (you may already have your own logic)
    $grossPay = $this->total_daily_pay + $this->total_overtime_pay + $totalCommission;
    $netPay = $grossPay - $totalDeduction;

    // Save invoice record
    Invoice::create([
        'user_id' => $this->user->id,
        'start_date' => $this->startDate,
        'end_date' => $this->endDate,
        'total_days' => $this->totalDays,
        'total_hours' => $this->totalHours,
        'total_daily_pay' => $this->total_daily_pay,
        'total_overtime_pay' => $this->total_overtime_pay,
        'total_commission' => $totalCommission,
        'commission_descriptions' => $commissionDescriptions,
        'commission_quantity' => $commissionQuantity,
        'total_deduction' => $totalDeduction,
        'deduction_descriptions' => $deductionDescriptions,
        'gross_pay' => $grossPay,
        'net_pay' => $netPay,
    ]);



    // Optionally, show a confirmation in Livewire
    $this->dispatch('notify', message: 'Invoice saved successfully!');
}


public function storeInvoice(\Illuminate\Http\Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'total_days' => 'required|numeric',
        'total_hours' => 'required|numeric',
        'total_daily_pay' => 'required|numeric',
        'total_overtime_pay' => 'required|numeric',
        'total_commission' => 'required|numeric',
        'commission_descriptions' => 'nullable|string',
        'commission_quantity' => 'nullable|numeric',
        'total_deduction' => 'required|numeric',
        'deduction_descriptions' => 'nullable|string',
        'gross_pay' => 'required|numeric',
        'net_pay' => 'required|numeric',
    ]);

    \App\Models\Invoice::create($validated);

    return response()->json(['message' => 'Invoice created successfully!']);
}


    public function render()
    {
        return view('livewire.filament.payslips', [
            'totalDays' => $this->totalDays,
            'totalHours' => $this->totalHours,
            // 'commissions' => $this->commissions,
            // 'deductions' => $this->deductions,
        ]);
    }
}

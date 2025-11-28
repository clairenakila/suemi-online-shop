<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\User;
use App\Models\Invoice;

class Invoices extends Component
{
    public $invoice;
    public $user;
    public $start_date;
    public $end_date;

    public function mount($user_id, $start_date, $end_date)
    {
        $this->user = User::findOrFail($user_id);
        $this->start_date = $start_date;
        $this->end_date = $end_date;

        $this->invoice = Invoice::where('user_id', $user_id)
            ->where('start_date', $start_date)
            ->where('end_date', $end_date)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.pages.show-invoice');
    }
}

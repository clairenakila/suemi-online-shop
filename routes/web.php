<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Filament\Payslips;
use App\Livewire\Filament\Invoices;



// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect('suemionlineshop/login');
});

// Route::get('/payslips', Payslips::class)->name('payslip.view');

Route::get('/payslips/{user_id}/{start_date}/{end_date}', Payslips::class)
    ->name('payslip.view');

    // Invoices (Livewire manual mounting)
Route::get('/invoices/{user_id}/{start_date}/{end_date}', function($user_id, $start_date, $end_date) {
    return app(Invoices::class)
        ->call('mount', [$user_id, $start_date, $end_date])
        ->render();
})->name('invoice.view');



Route::post('/invoices/create', [Payslips::class, 'storeInvoice'])->name('invoices.create');

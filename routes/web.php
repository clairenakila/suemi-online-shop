<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Filament\Payslips;


// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect('suemionlineshop/login');
});

// Route::get('/payslips', Payslips::class)->name('payslip.view');

Route::get('/payslips/{user_id}/{start_date}/{end_date}', Payslips::class)
    ->name('payslip.view');


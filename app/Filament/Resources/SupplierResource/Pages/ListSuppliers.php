<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action; 
use App\Imports\EmployeeImport;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;


class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
{
    $user = auth()->user();

    $actions = [
        Actions\CreateAction::make()
            ->label('Create')
            ->slideOver(),
    ];

    // ✅ Show import only for super_admin (role_id = 1)
    if ($user && $user->role_id === 1) {
        $actions[] = Action::make('importEmployee')
            ->label('Import')
            ->slideOver()
            ->color('success')
            ->icon('heroicon-o-arrow-up-tray')
            ->button()
            ->form([
                FileUpload::make('attachment')
                    ->label('Import an Excel file. Column headers must include: Name, Email, and Contact Number.') 
            ])
            ->action(function (array $data) {
                $file = public_path('storage/' . $data['attachment']);

                Excel::import(new \App\Imports\SupplierImport, $file);
                  
                

            });
    }

    return $actions;
}

}
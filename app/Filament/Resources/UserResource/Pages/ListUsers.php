<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action; 
use App\Imports\EmployeeImport;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;



class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

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
                    ->label('Import an Excel file. Column headers must include: Name, Role, Email, Contact Number, SSS Number, Pag-IBIG Number, Philhealth Number, Hourly Rate, Daily Rate, and Signature Existing Name and Email will cause failure.') 
            ])
            ->action(function (array $data) {
                $file = public_path('storage/' . $data['attachment']);

                Excel::import(new \App\Imports\EmployeeImport, $file);
                  
                Notification::make()
                        ->title('Employees Imported')
                        ->success()
                        ->send();
                

            });
    }

    return $actions;
}

}

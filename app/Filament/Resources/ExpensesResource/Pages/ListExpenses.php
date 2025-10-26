<?php

namespace App\Filament\Resources\ExpensesResource\Pages;

use App\Filament\Resources\ExpensesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Imports\ExpenseImport;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpensesResource::class;

    protected function getHeaderActions(): array
    {
        $user = Auth::user(); // ✅ get logged-in user

        $actions = [
            Actions\CreateAction::make()
                ->label('Create')
                ->slideOver(),
        ];

        // ✅ Only show Import for super_admin (role_id = 1)
        if ($user && $user->role_id === 1) {
            $actions[] = Action::make('importExpense')
                ->label('Import')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->button()
                ->slideOver()
                ->form([
                    FileUpload::make('attachment')
                        ->label('Upload Excel file')
                        ->required()
                        ->directory('imports') // optional: store in storage/app/public/imports
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]),
                ])
                ->action(function (array $data) {
                    $path = storage_path('app/public/' . $data['attachment']);
                    
                    Excel::import(new ExpenseImport, $path);

                    Notification::make()
                        ->title('Import Successful')
                        ->body('Expenses imported successfully.')
                        ->success()
                        ->send();
                });
        }

        return $actions;
    }
}

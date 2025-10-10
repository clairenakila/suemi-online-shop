<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ListInventories extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make()
                ->label('Create')
                ->slideOver(),
        ];

        $user = Auth::user();

        // ✅ Show import only for super_admin (role_id = 1)
        if ($user && $user->role_id === 1) {
            $actions[] = Action::make('importInventory')
                ->label('Import')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->button()
                ->slideOver()
                ->form([
                    FileUpload::make('attachment')
                        ->label('Import an Excel file. Column headers must include: Date Arrived, Category, Supplier, Box Number, Quantity, Amount, and Total.') 
                        ->required()
                ])
                ->action(function (array $data) {

                    $file = public_path('storage/' . $data['attachment']);
    
                    Excel::import(new \App\Imports\InventoryImport, $file);

                    Notification::make()
                        ->title('Inventories Imported Successfully')
                        ->success()
                        ->send();
                });
        }

        return $actions;
    }
}

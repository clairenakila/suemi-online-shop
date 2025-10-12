<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\ItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Imports\ItemImport;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Components\Tab;
use App\Models\Item;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

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
            $actions[] = Action::make('importItem')
                ->label('Import')
                ->slideOver()
                ->color('success')
                ->icon('heroicon-o-arrow-up-tray')
                ->button()
                ->form([
                    FileUpload::make('attachment')
                        ->label('Import an Excel file. Column headers must include: Timestamp, Brand, Order ID, Category, Prepared By, Quantity, Capital, Selling Price, Live Seller, Is Returned, Date Returned, and Date Shipped.')
                ])
                ->action(function (array $data) {
                    $file = public_path('storage/' . $data['attachment']);
                    Excel::import(new \App\Imports\ItemImport, $file);
                });
        }

        return $actions;
    }

    // ✅ Count all quantities of all items
    protected function allItems(): int
    {
        return Item::sum('quantity');
    }

    // ✅ Count items where is_returned = Yes
    protected function returnedItems(): int
    {
        return Item::where('is_returned', 'Yes')->count();
    }

    // ✅ Tabs for Filament
    public function getTabs(): array
    {
        return [
            Tab::make('All Items')
                ->badge($this->allItems())
                ->modifyQueryUsing(fn($query) => $query->orderBy('created_at', 'desc')),

            Tab::make('Returned Items')
                ->badge($this->returnedItems())
                ->modifyQueryUsing(fn($query) =>
                    $query->where('is_returned', 'Yes')
                          ->orderBy('date_returned', 'desc')
                ),
        ];
    }
}

<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Components\Tab;



class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create')
                ->slideOver(),
        ];
    }
//     protected function getHeaderActions(): array
// {
//     $user = auth()->user();

//     $actions = [];

//     // ✅ Only allow import if user is super_admin (role_id = 1)
//     if ($user->role_id == 1 || $user->hasRole('super_admin')) {
//         $actions[] = Actions\Action::make('importEquipment')
//             ->label('Import')
//             ->color('success')
//             ->button()
//             ->form([
//                 FileUpload::make('attachment')
//                     ->label('Import an Excel file. Column headers must include: PO Number, Unit Number, Brand Name, Description, Facility, Category, Status, Date Acquired, Supplier, Amount, Estimated Life, Item Number, Property Number, Control Number, Serial Number, Person Liable, and Remarks. It is okay to have null fields in Excel as long as all the column headers are present.')
//                     ->required(),
//             ])
//             ->action(function (array $data) {
//                 $file = public_path('storage/' . $data['attachment']);

//                 Excel::import(new EquipmentImport, $file);

//                 Notification::make()
//                     ->title('Equipment Imported')
//                     ->success()
//                     ->send();
//             });
//     }

//     // ✅ You can still allow Create for others if needed
//     $actions[] = Actions\CreateAction::make()
//         ->label('Create');

//     return $actions;
// }



}

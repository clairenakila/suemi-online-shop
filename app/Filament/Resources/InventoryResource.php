<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Filament\Resources\InventoryResource\RelationManagers;
use App\Models\Inventory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Supplier;
use App\Models\Category;
use Filament\Tables\Enums\ActionsPosition;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Spatie\Permission\Models\Role;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\Summarizers\Sum;

class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationGroup = 'Product Management';
    protected static ?string $navigationLabel = 'Inventories';
    protected static bool $isLazy = false;
    // public static function getNavigationBadge(): ?string
    // {
    //     return (string) static::getModel()::sum('quantity');
    // }
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('date_arrived')
                    ->label('Date Arrived')
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category','description')
                    ->label('Category')
                    ->default(null)
                    ->required(),
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier','name')
                    ->label('Supplier')
                    ->default(null)
                    ->required(),
                Forms\Components\TextInput::make('box_number')
                    ->label('Box Number')
                    ->maxLength(255)
                    ->default(null)
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->default(null)
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->default(null)
                    ->required(),
                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->default(null)
                    ->hidden(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date_arrived')
                    ->label('Date Arrived')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('category.description')
                    ->label('Category')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('box_number')
                    ->label('Box Number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                 Filter::make('date_arrived')
                    ->form([
                        DatePicker::make('date_arrived')
                            ->label('Date Arrived'),
                    ])
                    ->query(function ($query, array $data): void {
                        $query->when(
                            $data['date_arrived'],
                            fn ($query, $date) => $query->whereDate('date_arrived', $date),
                        );
                    }),
                    SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->options(
                        \App\Models\Supplier::whereIn('id', \App\Models\Inventory::pluck('supplier_id')->unique())
                            ->pluck('name', 'id')
                            ->toArray()
                    ),
                    SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(
                        \App\Models\Category::whereIn('id', \App\Models\Inventory::pluck('category_id')->unique())
                            ->pluck('description', 'id')
                            ->toArray()
                    ),


            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->label(''),
            ], position: ActionsPosition::BeforeCells) 
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_update')
                    ->label('Bulk Update')
                    ->slideOver()
                    ->icon('heroicon-o-pencil-square')
                    ->color('secondary')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-pencil-square')
                    ->modalHeading('Bulk Update Inventory Details')
                    ->modalDescription('Select the fields you want to update.')
                    ->form(function (Forms\Form $form) {
                return $form->schema([
                    // Select columns to update    
                    Forms\Components\CheckboxList::make('fields_to_update')
                        ->label('Select fields to update.')
                        ->options([
                            'date_arrived'=>'Created At',
                            'category_id' => 'Category',
                            'supplier_id' => 'Supplier',
                            'box_number' => 'Box Number',
                            'quantity' => 'Quantity',
                            'amount' => 'Amount',
                           
                        ])
                        ->columns(2)
                        ->reactive(), 
                    Forms\Components\DateTimePicker::make('date_arrived')
                        ->label('Date Arrived')
                        ->visible(fn ($get) => in_array('date_arrived', $get('fields_to_update') ?? [])) 
                        ->required(fn ($get) => in_array('date_arrived', $get('fields_to_update') ?? [])),
                    Forms\Components\Select::make('category_id')
                        ->relationship('category','description')
                        ->options(\App\Models\Category::all()->pluck('description', 'id'))
                        ->visible(fn ($get) => in_array('category_id', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('category_id', $get('fields_to_update') ?? [])),
                    Forms\Components\TextInput::make('order_id')
                        ->label('Order ID')
                        ->maxLength(4)
                        ->visible(fn ($get) => in_array('order_id', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('order_id', $get('fields_to_update') ?? [])),
                    Forms\Components\Select::make('supplier_id')
                        ->label('Supplier')
                        ->options(\App\Models\Supplier::all()->pluck('name', 'id'))
                        ->visible(fn ($get) => in_array('supplier_id', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('supplier_id', $get('fields_to_update') ?? [])),
                    Forms\Components\TextInput::make('box_number')
                        ->label('Box Number')
                        ->visible(fn ($get) => in_array('box_number', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('box_number', $get('fields_to_update') ?? [])),
                    Forms\Components\TextInput::make('quantity')
                        ->label('Quantity')
                        ->integer()
                        ->placeholder('Ilang bag sa isang carton? Enter a number only.')
                        ->visible(fn ($get) => in_array('quantity', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('quantity', $get('fields_to_update') ?? [])),
                    Forms\Components\TextInput::make('amount')
                        ->label('Amount')
                        ->integer()
                        ->visible(fn ($get) => in_array('amount', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('amount', $get('fields_to_update') ?? [])),
                ]);
            })
            ->action(function (array $data, $records) {
                foreach ($records as $record) {
                    $updateData = [];

                    if (in_array('date_arrived', $data['fields_to_update'])) {
                        $updateData['date_arrived'] = $data['date_arrived'];
                    }      
                    if (in_array('category_id', $data['fields_to_update'])) {
                        $updateData['category_id'] = $data['category_id'];
                    }
                    if (in_array('supplier_id', $data['fields_to_update'])) {
                        $updateData['supplier_id'] = $data['supplier_id'];
                    }
                    if (in_array('box_number', $data['fields_to_update'])) {
                        $updateData['box_number'] = $data['box_number'];
                    }
                    if (in_array('quantity', $data['fields_to_update'])) {
                        $updateData['quantity'] = $data['quantity'];
                    }
                    if (in_array('amount', $data['fields_to_update'])) {
                        $updateData['amount'] = $data['amount'];
                    }
                   
                    
                     
                    // ✅ Recalculate total automatically if quantity or amount changed
        if (array_key_exists('quantity', $updateData) || array_key_exists('amount', $updateData)) {
            $quantity = $updateData['quantity'] ?? $record->quantity;
            $amount   = $updateData['amount'] ?? $record->amount;
            $updateData['total'] = $quantity * $amount;
        }

        // ✅ Save the updated values to DB
        if (!empty($updateData)) {
            $record->update($updateData);
        }
    }

                    
        
                \Filament\Notifications\Notification::make()
                    ->title('Inventories updated successfully!')
                    ->success()
                    ->color('secondary')
                    ->send();
            }),
                ExportBulkAction::make()
                    ->label('Export Selected')
                    ->color('success')   
                    ->icon('heroicon-o-arrow-down-tray')
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make('Inventories')
                                ->fromTable()
                                ->withFilename('Inventories.xlsx'),
                        ]),
                 Tables\Actions\DeleteBulkAction::make()
                 ->slideOver(),

                ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventories::route('/'),
            // 'create' => Pages\CreateInventory::route('/create'),
            // 'edit' => Pages\EditInventory::route('/{record}/edit'),
        ];
    }
}

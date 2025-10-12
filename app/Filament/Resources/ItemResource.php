<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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


class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Product Management';
    protected static ?string $navigationLabel = 'Items';
    protected static bool $isLazy = false;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::Sum('quantity');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('created_at')
                ->label('Created At')
                ->default('now')
                ->readOnly(fn () => ! auth()->user()->hasRole('super_admin')),
                Forms\Components\Select::make('category_id')
                    ->relationship('category','description')
                    ->default(function () {
                        $bagCategory = \App\Models\Category::where('description', 'Bag')->first();
                        return $bagCategory ? $bagCategory->id : null;
                    })
                    ->label('Category'),
                Forms\Components\Select::make('user_id')
                    ->label('Prepared By')
                    ->relationship('user','name')
                     ->default(fn () => auth()->id()),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->placeholder('Ilang bag sa isang code? Enter a number only.'),
                Forms\Components\TextInput::make('brand')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('order_id')
                    ->label('Order ID')
                    ->required()
                    ->placeholder('Apat na letra lamang')
                    ->maxLength(4),
                Forms\Components\Select::make('live_seller') // the column that stores the name
                    ->label('Live Seller')
                    ->options(function () {
                        return \App\Models\User::where('is_live_seller', 'Yes')
                            ->pluck('name', 'name'); // key = value = name
                    })
                    ->default(fn () => auth()->user()->name)
                    ->required(),
                Forms\Components\TextInput::make('capital')
                    ->numeric()
                    ->required()
                    ->default(null),
                Forms\Components\TextInput::make('selling_price')
                    ->label('Selling Price')
                    ->numeric()
                    ->required()
                    ->placeholder('Enter the selling price'),

                Forms\Components\TextInput::make('shoppee_commission')
                    ->numeric()
                    ->hidden(),
                Forms\Components\TextInput::make('total_gross_sale')
                    ->numeric()
                    ->hidden(),

                Forms\Components\Section::make('Returned Info')
                ->schema([
                Forms\Components\Select::make('is_returned')
                    ->label('Is Returned')
                    ->default('No')
                    ->options([
                        'Yes' => 'Yes',
                        'No'  => 'No',
                    ])
                    ->required(),

                Forms\Components\DateTimePicker::make('date_returned'),
                Forms\Components\DateTimePicker::make('date_shipped'),

                ])
                ->collapsible()
            ]);
            
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Prepared By')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.description')
                    ->label('Category')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                  Tables\Columns\TextColumn::make('live_seller')
                    ->label('Live Seller')
                    ->searchable(),      
                
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->searchable()
                    ->sortable()
                    ->summarize(Sum::make()->label('Total Quantity')->suffix('pcs.')),
                Tables\Columns\TextColumn::make('capital')
                    ->numeric()
                    ->searchable()
                    ->sortable()
                    ->summarize(Sum::make()->label('Total Capital')->money('PHP')),
                Tables\Columns\TextColumn::make('selling_price')
                    ->label('Selling Price')
                    ->numeric()
                    ->searchable()
                    ->sortable()
                    ->summarize(Sum::make()->label('Total Selling Price')->money('PHP')),
                Tables\Columns\TextColumn::make('shoppee_commission')
                    ->label('Shoppee Commission')
                    ->numeric()
                    ->searchable()
                    ->sortable()
                    ->summarize(Sum::make()->label('Total Shoppee Commission (21%)')->money('PHP')),
                
                    
                Tables\Columns\TextColumn::make('total_gross_sale')
                    ->label('Total Gross Sale')
                    ->numeric()
                    ->searchable()
                    ->sortable()
                    ->summarize(Sum::make()->label('Total Gross Sale')->money('PHP')),
                
                Tables\Columns\TextColumn::make('is_returned')
                    ->label('Is Returned')
                    ->badge()
                    ->searchable()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                                'yes' => 'success',
                                'no' => 'danger',
                            }),
                Tables\Columns\TextColumn::make('date_returned')
                    ->label('Date Returned')
                    ->dateTime()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_shipped')
                    ->label('Date Shipped')
                    ->dateTime()
                    ->searchable()
                    ->sortable(),
            ])
            
            ->defaultSort('created_at','desc')
            ->filters([

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_at')
                            ->label('Date'),
                    ])
                    ->query(function ($query, array $data): void {
                        $query->when(
                            $data['created_at'],
                            fn ($query, $date) => $query->whereDate('created_at', $date),
                        );
                    }),

                SelectFilter::make('user_id')
                    ->label('Prepared By')
                    ->options(
                        \App\Models\User::whereIn('id', \App\Models\Item::pluck('user_id')->unique())
                            ->pluck('name', 'id')
                            ->toArray()
                    ),
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(
                        \App\Models\Category::whereIn('id', \App\Models\Item::pluck('category_id')->unique())
                            ->pluck('description', 'id')
                            ->toArray()
                    ),
            
                SelectFilter::make('live_seller')
                    ->label('Live Seller')
                    ->options(
                        \App\Models\Item::whereNotNull('live_seller')
                            ->distinct()
                            ->pluck('live_seller', 'live_seller')
                            ->toArray()
                    ),
                 SelectFilter::make('quantity')
                    ->label('Quantity')
                    ->options(
                        \App\Models\Item::whereNotNull('quantity')
                            ->distinct()
                            ->pluck('quantity', 'quantity')
                            ->toArray()
                    ),
                
        ])
        
            ->actions([
                Tables\Actions\EditAction::make()
                ->slideOver()
                ->label(''),
            ], position: ActionsPosition::BeforeCells) 
            ->bulkActions([
                Tables\Actions\BulkAction::make('mark_as_returned')
                    ->label('Mark as Returned')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->slideOver()
                    ->modalHeading('Mark Items as Returned')
                    ->modalDescription('Please provide the shipment and return dates.')
                    ->form([
                        Forms\Components\DatePicker::make('date_shipped')
                            ->label('Date Shipped')
                            ->required(),
                        Forms\Components\DatePicker::make('date_returned')
                            ->label('Date Returned')
                            ->required(),
                    ])
                    ->action(function (array $data, $records): void {
                        foreach ($records as $record) {
                            $record->update([
                                'is_returned' => 'Yes',
                                'date_shipped' => $data['date_shipped'],
                                'date_returned' => $data['date_returned'],
                            ]);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Selected items marked as returned!')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_update')
                    ->label('Bulk Update')
                    ->slideOver()
                    ->icon('heroicon-o-pencil-square')
                    ->color('secondary')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-pencil-square')
                    ->modalHeading('Bulk Update User Details')
                    ->modalDescription('Select the fields you want to update.')
                    ->form(function (Forms\Form $form) {
                return $form->schema([
                    // Select columns to update    
                    Forms\Components\CheckboxList::make('fields_to_update')
                        ->label('Select fields to update.')
                        ->options([
                            'created_at'=>'Created At',
                            'brand' => 'Brand',
                            'order_id' => 'Order ID',
                            'category_id' => 'Category',
                            'user_id' => 'Prepared By',
                            'quantity' => 'Quantity',
                            'capital' => 'Capital',
                            'selling_price' => 'Selling Price',
                            'is_returned' => 'Is Returned',
                            'date_returned' => 'Date Returned',
                            'date_shipped' => 'Date Shipped',
                            'live_seller' => 'Live Seller',
                           
                        ])
                        ->columns(2)
                        ->reactive(), 
                    Forms\Components\DateTimePicker::make('created_at')
                        ->label('Created At')
                        ->visible(fn ($get) => in_array('created_at', $get('fields_to_update') ?? [])) 
                        ->required(fn ($get) => in_array('created_at', $get('fields_to_update') ?? [])),
                    Forms\Components\TextInput::make('brand')
                        ->label('Brand')
                        ->visible(fn ($get) => in_array('brand', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('brand', $get('fields_to_update') ?? [])),
                    Forms\Components\TextInput::make('order_id')
                        ->label('Order ID')
                        ->maxLength(4)
                        ->visible(fn ($get) => in_array('order_id', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('order_id', $get('fields_to_update') ?? [])),
                    Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->options(\App\Models\Category::all()->pluck('description', 'id'))
                        ->visible(fn ($get) => in_array('category_id', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('category_id', $get('fields_to_update') ?? [])),
                    Forms\Components\Select::make('user_id')
                        ->label('Prepared By')
                        ->options(\App\Models\User::all()->pluck('name', 'id'))
                        ->visible(fn ($get) => in_array('user_id', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('user_id', $get('fields_to_update') ?? [])),
                    Forms\Components\TextInput::make('quantity')
                        ->label('Quantity')
                        ->integer()
                        ->placeholder('Ilang bag sa isang code? Enter a number only.')
                        ->visible(fn ($get) => in_array('quantity', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('quantity', $get('fields_to_update') ?? [])),
                    Forms\Components\TextInput::make('capital')
                        ->label('Capital')
                        ->integer()
                        ->visible(fn ($get) => in_array('capital', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('capital', $get('fields_to_update') ?? [])),
                    Forms\Components\TextInput::make('selling_price')
                        ->label('Selling Price')
                        ->integer()
                        ->visible(fn ($get) => in_array('selling_price', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('selling_price', $get('fields_to_update') ?? [])),
                    Forms\Components\Select::make('is_returned')
                        ->label('Is Returned')
                        ->default('No')
                        ->options([
                            'Yes' => 'Yes',
                            'No'  => 'No',
                        ])
                        ->visible(fn ($get) => in_array('is_returned', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('is_returned', $get('fields_to_update') ?? [])),
                    Forms\Components\DateTimePicker::make('date_returned')
                        ->label('Date Returned')
                        ->visible(fn ($get) => in_array('date_returned', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('date_returned', $get('fields_to_update') ?? [])),
                    Forms\Components\TextInput::make('date_shipped')
                        ->label('Date Shipped')
                        ->visible(fn ($get) => in_array('date_shipped', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('date_shipped', $get('fields_to_update') ?? [])),
                    Forms\Components\Select::make('live_seller')
                        ->label('Live Seller')
                         ->options(function () {
                            return \App\Models\User::where('is_live_seller', 'Yes')
                                ->pluck('name', 'name'); // key = value = name
                        })
                        ->visible(fn ($get) => in_array('live_seller', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('live_seller', $get('fields_to_update') ?? [])),
                ]);
            })
            ->action(function (array $data, $records) {
                foreach ($records as $record) {
                    $updateData = [];

                    if (in_array('created_at', $data['fields_to_update'])) {
                        $updateData['created_at'] = $data['created_at'];
                    }      
                    if (in_array('brand', $data['fields_to_update'])) {
                        $updateData['brand'] = $data['brand'];
                    }
                    if (in_array('order_id', $data['fields_to_update'])) {
                        $updateData['order_id'] = $data['order_id'];
                    }
                    if (in_array('category_id', $data['fields_to_update'])) {
                        $updateData['category_id'] = $data['category_id'];
                    }
                    if (in_array('user_id', $data['fields_to_update'])) {
                        $updateData['user_id'] = $data['user_id'];
                    }
                    if (in_array('quantity', $data['fields_to_update'])) {
                        $updateData['quantity'] = $data['quantity'];
                    }
                    if (in_array('capital', $data['fields_to_update'])) {
                        $updateData['capital'] = $data['capital'];
                    }
                    if (in_array('selling_price', $data['fields_to_update'])) {
                        $updateData['selling_price'] = $data['selling_price'];
                    }
                    if (in_array('is_returned', $data['fields_to_update'])) {
                        $updateData['is_returned'] = $data['is_returned'];
                    }
                    if (in_array('date_returned', $data['fields_to_update'])) {
                        $updateData['date_returned'] = $data['date_returned'];
                    }
                    if (in_array('date_shipped', $data['fields_to_update'])) {
                        $updateData['date_shipped'] = $data['date_shipped'];
                    }
                    if (in_array('live_seller', $data['fields_to_update'])) {
                        $updateData['live_seller'] = $data['live_seller'];
                    }
                        $record->timestamps = false;  // 🚨 very important
                            $record->update($updateData);
                            $record->timestamps = true;
                    }
        
                \Filament\Notifications\Notification::make()
                    ->title('Items updated successfully!')
                    ->success()
                    ->color('secondary')
                    ->send();
            }),
                ExportBulkAction::make()
                    ->label('Export Selected')
                    ->color('success')   
                    ->icon('heroicon-o-arrow-down-tray')
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make('Items')
                                ->fromTable()
                                ->withFilename('Items.xlsx'),
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

    public static function getWidgets(): array
{
    return [
        \App\Filament\Resources\ItemResource\Widgets\ItemSalesSummary::class,
    ];
}


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            // 'create' => Pages\CreateItem::route('/create'),
            // 'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}

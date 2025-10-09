<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use App\Models\User;
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

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->placeholder('Ilang bag sa isang code? Enter a number only.')
                    ->default(1),
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
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
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
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capital')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling_price')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shoppee_commission')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_gross_sale')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('live_seller')
                    ->label('Live Seller')
                    ->searchable(),                                   
                Tables\Columns\TextColumn::make('is_returned')
                ->badge()
                ->searchable()
                 ->color(fn (string $state): string => match (strtolower($state)) {
                            'yes' => 'success',
                            'no' => 'danger',
                        }),
                Tables\Columns\TextColumn::make('date_returned')
                    ->dateTime()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_shipped')
                    ->dateTime()
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('created_at','desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->slideOver(),
            ], position: ActionsPosition::BeforeCells) 
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->slideOver(),
                ]),
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
            'index' => Pages\ListItems::route('/'),
            // 'create' => Pages\CreateItem::route('/create'),
            // 'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}

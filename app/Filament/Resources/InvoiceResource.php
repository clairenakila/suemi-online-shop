<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Payroll Management';
    protected static ?string $navigationLabel = 'Invoices';
    protected static bool $isLazy = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required(),
                Forms\Components\TextInput::make('total_days')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_hours')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_daily_pay')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_overtime_pay')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Textarea::make('commission_descriptions')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('commission_quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_commission')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Textarea::make('deduction_descriptions')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('total_deduction')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('gross_pay')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('net_pay')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Pay Period(Start)')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Pay Period(End)')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Employee')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('total_days')
                    ->label('Total Days Worked')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Total Overtime/Hours Worked')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_daily_pay')
                    ->label('Total Daily Pay')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_overtime_pay')
                ->label('Total Overtime Pay')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('commission_descriptions')
                    ->label('Commission/s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_commission')
                    ->label('Total Commission')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deduction_descriptions')
                    ->label('Deduction/s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_deduction')
                    ->label('Total Deduction')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gross_pay')
                    ->label('Gross Pay')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('net_pay')
                    ->label('Net Pay')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListInvoices::route('/'),
            // 'create' => Pages\CreateInvoice::route('/create'),
            // 'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpensesResource\Pages;
use App\Filament\Resources\ExpensesResource\RelationManagers;
use App\Models\Expenses;
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

class ExpensesResource extends Resource
{
    protected static ?string $model = Expenses::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->required(),
                Forms\Components\select::make('expense_type')
                    ->required()
                    ->options([
                        'Marketing Expense' => 'Marketing Expense',
                        'Production Expense' => 'Production Expense',
                        'Utility Expense' => 'Utility Expense',
                        'Salary Expense' => 'Salary Expense',
                        'Other' => 'Other',

                    ]),
                Forms\Components\TextInput::make('amount')
                    ->integer()    
                    ->required(),
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
                 Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('expense_type')
                    ->label('Expense Type')    
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('amount')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->summarize(Sum::make()->label('Total Amount')),
            ])
            ->defaultSort('created_at','desc')
            ->filters([
                 SelectFilter::make('expense_type')
                    ->label('Expense Type')
                     ->options([
                        'Marketing Expense' => 'Marketing Expense',
                        'Production Expense' => 'Production Expense',
                        'Utility Expense' => 'Utility Expense',
                        'Salary Expense' => 'Salary Expense',
                        'Other' => 'Other',

                    ]),
            
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->slideOver(),
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
                    ->modalHeading('Bulk Update Expense Details')
                    ->modalDescription('Select the fields you want to update.')
                    ->form(function (Forms\Form $form) {
                return $form->schema([
                    // Select columns to update    
                    Forms\Components\CheckboxList::make('fields_to_update')
                        ->label('Select fields to update.')
                        ->options([
                            'description' => 'Description',
                            'expense_type' => 'Expense Type',
                            'amount' => 'amount',

                        ])
                        ->columns(1)
                        ->reactive(), 
                   
                    Forms\Components\TextInput::make('description')
                        ->label('Description')
                        ->visible(fn ($get) => in_array('description', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('description', $get('fields_to_update') ?? [])),
                    Forms\Components\Select::make('expense_type')
                        ->label('Expense Type')
                        ->options([
                            'description' => 'Description',
                            'expense_type' => 'Expense Type',
                            'amount' => 'amount',

                        ])
                        ->visible(fn ($get) => in_array('expense_type', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('expense_type', $get('fields_to_update') ?? [])),
                    Forms\Components\TextInput::make('amount')
                        ->label('Amount')
                        ->numeric()
                        ->visible(fn ($get) => in_array('amount', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('amount', $get('fields_to_update') ?? [])),
                  
                    ]);
            })
            ->action(function (array $data, $records) {
                foreach ($records as $record) {
                    $updateData = [];

                    if (in_array('description', $data['fields_to_update'])) {
                        $updateData['description'] = $data['description'];
                    }      
                    if (in_array('expense_type', $data['fields_to_update'])) {
                        $updateData['expense_type'] = $data['expense_type'];
                    }
                    if (in_array('amount', $data['fields_to_update'])) {
                        $updateData['amount'] = $data['amount'];
                    }
                    
                        $record->timestamps = false;  // 🚨 very important
                            $record->update($updateData);
                            $record->timestamps = true;
                    }
        
                \Filament\Notifications\Notification::make()
                    ->title('Expense details updated successfully!')
                    ->success()
                    ->color('secondary')
                    ->send();
            }),
                ExportBulkAction::make()
                    ->label('Export Selected')
                    ->color('success')   
                    ->icon('heroicon-o-arrow-down-tray')
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make('Expenses')
                                ->fromTable()
                                ->withFilename('Expenses.xlsx'),
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
            'index' => Pages\ListExpenses::route('/'),
            // 'create' => Pages\CreateExpenses::route('/create'),
            // 'edit' => Pages\EditExpenses::route('/{record}/edit'),
        ];
    }
}

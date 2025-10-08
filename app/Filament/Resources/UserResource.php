<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
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




class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Users & Employee Management';
    protected static ?string $navigationLabel = 'Employees';
    protected static bool $isLazy = false;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->disabled(fn (string $context): bool => $context === 'edit')
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state, $record) =>
                        filled($state)
                            ? bcrypt($state)
                            : $record->password // keep old hash if empty
                    ),
                Forms\Components\TextInput::make('contact_number')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('sss_number')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('pagibig_number')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('philhealth_number')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('hourly_rate')
                    ->numeric()
                    ->required()
                    ->default(null),
                Forms\Components\TextInput::make('daily_rate')
                    ->numeric()
                    ->required()
                    ->default(null),
                Forms\Components\Select::make('role_id')
                    ->relationship('role','name')
                    ->default(null)
                    ->required(),
                Forms\Components\FileUpload::make('signature')
                    ->imageEditor()
                    ->deletable()
                    ->preserveFilenames()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
         

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('role.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('password')
                    ->label('Password')
                    ->limit(10)
                     ->getStateUsing(fn () => 'password')
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > $column->getCharacterLimit() ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('role.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sss_number')
                    ->searchable()
                    ->label('SSS Number')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('pagibig_number')
                    ->searchable()
                    ->label('Pagibig Number')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('philhealth_number')
                    ->searchable()
                    ->label('Philhealth Number')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('hourly_rate')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('daily_rate')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\ImageColumn::make('signature')
                    ->searchable(),
                
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
           
            ->actions([
                Tables\Actions\EditAction::make()
                ->label('')
                ->slideOver(),
                ], position: ActionsPosition::BeforeCells)  
                     
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_update')
                    ->label('Bulk Update')
                    ->slideOver()
                    ->icon('heroicon-o-pencil-square')
                    ->color('secondary')
                    // ->extraAttributes([
                    //     'class' => 'bg-[#f43f5e] hover:bg-[#e11d48] text-white',
                    // ])
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
                            'role_id' => 'Role',
                            'hourly_rate' => 'Hourly Rate',
                            'daily_rate' => 'Daily Rate',
                        ])
                        ->columns(1)
                        ->reactive(), 
                    Forms\Components\Select::make('role_id')
                        ->label('Role')
                        ->options(Role::all()->pluck('name', 'id'))
                        ->visible(fn ($get) => in_array('role_id', $get('fields_to_update') ?? [])) 
                        ->required(fn ($get) => in_array('role_id', $get('fields_to_update') ?? [])),
                    Forms\Components\TextInput::make('hourly_rate')
                        ->label('Hourly Rate')
                        ->visible(fn ($get) => in_array('hourly_rate', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('hourly_rate', $get('fields_to_update') ?? [])),
                    Forms\Components\TextInput::make('daily_rate')
                        ->label('Daily Rate')
                        ->visible(fn ($get) => in_array('daily_rate', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('daily_rate', $get('fields_to_update') ?? [])),
                ]);
            })
            ->action(function (array $data, $records) {
                foreach ($records as $record) {
                    $updateData = [];
        
                    if (in_array('role_id', $data['fields_to_update'])) {
                        $updateData['role_id'] = $data['role_id'];
                    }
                    if (in_array('hourly_rate', $data['fields_to_update'])) {
                        $updateData['hourly_rate'] = $data['hourly_rate'];
                    }
                    if (in_array('daily_rate', $data['fields_to_update'])) {
                        $updateData['daily_rate'] = $data['daily_rate'];
                    }
        
                    $record->update($updateData);   
                }
        
                \Filament\Notifications\Notification::make()
                    ->title('Users updated successfully!')
                    ->success()
                    ->color('secondary')
                    ->send();
            }),
                ExportBulkAction::make()
                    ->label('Export Selected')
                    ->color('success')   
                    ->icon('heroicon-o-arrow-down-tray')
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make('Employees')
                                ->fromTable()
                                ->withFilename('Employees.xlsx'),
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
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

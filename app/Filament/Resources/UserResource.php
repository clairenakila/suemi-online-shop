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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
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
                    ->default(null),
                Forms\Components\TextInput::make('daily_rate')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('signature')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('role_id')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('contact_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sss_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pagibig_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('philhealth_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hourly_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('daily_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('signature')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role_id')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

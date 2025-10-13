<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
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
use App\Models\User;


class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
     protected static ?string $navigationGroup = 'Payroll Management';
    protected static ?string $navigationLabel = 'Attendance';
    protected static bool $isLazy = false;
    protected static ?string $modelLabel = 'Attendance';
    protected static ?string $pluralModelLabel = 'Attendance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('user_id')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('time_in'),
                Forms\Components\TextInput::make('time_out'),
                Forms\Components\TextInput::make('work_shift_status')
                    ->required(),
                Forms\Components\TextInput::make('total_days')
                    ->hidden(),
                Forms\Components\TextInput::make('total_hours')
                    ->hidden(),
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
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('time_in')
                    ->label('Time In')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('time_out')
                    ->label('Time Out')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('work_shift_status')
                    ->label('Work Shift Status')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('total_days')
                    ->label('Total Days')
                    ->summarize(Sum::make()->label('Total Days Worked')),
                Tables\Columns\TextColumn::make('total_hours')
                    ->label('Total Hours')
                    ->summarize(Sum::make()->label('Total Overtime/Hours Worked')),
            ])
            ->defaultSort('date','desc')
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('date')
                            ->label('Date'),
                    ])
                    ->query(function ($query, array $data): void {
                        $query->when(
                            $data['date'],
                            fn ($query, $date) => $query->whereDate('date', $date),
                        );
                    }),
                SelectFilter::make('user_id')
                    ->label('Employee')
                    ->options(
                        \App\Models\User::whereIn('id', \App\Models\Attendance::pluck('user_id')->unique())
                            ->pluck('name', 'id')
                            ->toArray()
                    ),
                 SelectFilter::make('work_shift_status')
                    ->label('Work Shift Status')
                    ->options([
                            'Whole Day' => 'Whole Day',
                            'Half Day' => 'Half Day',
                            'Overtime' => 'Overtime',
                            'Absent' => 'Absent',
                    ]),
            ])
            ->actions([
                // Tables\Actions\EditAction::make()
                //     ->label('')
                //     ->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_update')
                    ->label('Bulk Update')
                    ->slideOver()
                    ->icon('heroicon-o-pencil-square')
                    ->color('secondary')
                    ->requiresConfirmation()
                    ->modalIcon('heroicon-o-pencil-square')
                    ->modalHeading('Bulk Update Attendance Details')
                    ->modalDescription('Select the fields you want to update.')
                    ->form(function (Forms\Form $form) {
                return $form->schema([
                    // Select columns to update    
                    Forms\Components\CheckboxList::make('fields_to_update')
                        ->label('Select fields to update.')
                        ->options([
                            'date'=>'Date',
                            'work_shift_status' => 'Work Shift Status',
                            'time_in' => 'Time In',
                            'time_out' => 'Time Out',
                           
                        ])
                        ->columns(1)
                        ->reactive(), 
                    Forms\Components\DateTimePicker::make('date')
                        ->label('Date')
                        ->visible(fn ($get) => in_array('date', $get('fields_to_update') ?? [])) 
                        ->required(fn ($get) => in_array('date', $get('fields_to_update') ?? [])),
                    
                    Forms\Components\Select::make('work_shift_status')
                        ->label('Work Shift Status')
                        ->options([
                            'Whole Day' => 'Whole Day',
                            'Half Day' => 'Half Day',
                            'Overtime' => 'Overtime',
                            'Absent' => 'Absent',
                        ])
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            match ($state) {
                                'Whole Day' => $set('time_in', '07:30') && $set('time_out', '17:00'),
                                'Half Day'  => $set('time_in', '07:30') && $set('time_out', '12:00'),
                                'Overtime'  => $set('time_in', '17:00') && $set('time_out', '22:00'),
                                'Absent'    => $set('time_in', null) && $set('time_out', null),
                            };
                        })
                        ->visible(fn ($get) => in_array('work_shift_status', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('work_shift_status', $get('fields_to_update') ?? [])),
                    Forms\Components\TimePicker::make('time_in')
                        ->label('Time In')
                        ->visible(fn ($get) => in_array('time_in', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('time_in', $get('fields_to_update') ?? [])),
                    Forms\Components\TimePicker::make('time_out')
                        ->label('Time Out')
                        ->visible(fn ($get) => in_array('time_out', $get('fields_to_update') ?? []))
                        ->required(fn ($get) => in_array('time_out', $get('fields_to_update') ?? [])),
                    
                ]);
            })
            ->action(function (array $data, $records) {
                foreach ($records as $record) {
                    $updateData = [];

                    if (in_array('date', $data['fields_to_update'])) {
                        $updateData['date'] = $data['date'];
                    }      
                    if (in_array('work_shift_status', $data['fields_to_update'])) {
                        $updateData['work_shift_status'] = $data['work_shift_status'];
                    }
                    if (in_array('time_in', $data['fields_to_update'])) {
                        $updateData['time_in'] = $data['time_in'];
                    }
                    if (in_array('time_out', $data['fields_to_update'])) {
                        $updateData['time_out'] = $data['time_out'];
                    }
                   
                    
                    if (!empty($updateData)) {
                            $record->update($updateData); // ✅ Actually updates the record
                        }
                    }
        
                \Filament\Notifications\Notification::make()
                    ->title('Attendance updated successfully!')
                    ->success()
                    ->color('secondary')
                    ->send();
            }),
                ExportBulkAction::make()
                    ->label('Export Selected')
                    ->color('success')   
                    ->icon('heroicon-o-arrow-down-tray')
                        ->exports([
                            \pxlrbt\FilamentExcel\Exports\ExcelExport::make('Attendance')
                                ->fromTable()
                                ->withFilename('Attendance.xlsx'),
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
            'index' => Pages\ListAttendances::route('/'),
            // 'create' => Pages\CreateAttendance::route('/create'),
            // 'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}

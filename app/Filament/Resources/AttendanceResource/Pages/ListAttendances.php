<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms; 
use Filament\Notifications\Notification;
use App\Models\User;
use App\Models\Attendance;
use Carbon\CarbonPeriod;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList\SelectAllAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;





class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
         $actions = [
            Actions\Action::make('bulkAddAttendance')
                ->label('Create')
                // ->icon('heroicon-o-calendar-plus')
                ->color('primary')
                ->button()
                ->slideOver()
                ->form([
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required(),

                    Forms\Components\DatePicker::make('end_date')
                        ->label('End Date')
                        ->required(),
                       

                    CheckboxList::make('user_ids')
                        ->label('Select Employees')
                        ->options(User::where('is_employee', 'Yes')->pluck('name', 'id'))
                        ->columns(2)
                        ->required(),
                    
                    Forms\Components\Select::make('work_shift_status')
                        ->label('Work Shift status')
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
                        }),


                    Forms\Components\TimePicker::make('time_in')
                        ->label('Time In'),

                    Forms\Components\TimePicker::make('time_out')
                        ->label('Time Out'),

                ])
                ->action(function (array $data) {
                    $period = CarbonPeriod::create($data['start_date'], $data['end_date']);

                    foreach ($data['user_ids'] as $userId) {
                        foreach ($period as $date) {
                            Attendance::create([
                                'date' => $date->toDateString(),
                                'user_id' => $userId,
                                'time_in' => $data['time_in'],
                                'time_out' => $data['time_out'],
                                'work_shift_status' => $data['work_shift_status'],
                            ]);
                        }
                    }

                    Notification::make()
                        ->title('Attendance Added')
                        ->body('Attendance successfully created for selected users and dates.')
                        ->success()
                        ->send();
                }),

            // Actions\CreateAction::make()
            //     ->label('Create')
            //     ->slideOver(),

            
        ];
        $user = auth()->user();
        // ✅ Show import only for super_admin (role_id = 1)
        if ($user && $user->role_id === 1) {
            $actions[] = Actions\Action::make('importAttendance')
                ->label('Import')
                ->slideOver()
                ->color('success')
                ->icon('heroicon-o-arrow-up-tray')
                ->button()
                ->form([
                    FileUpload::make('attachment')
                        ->label('Import an Excel file. Column headers must include: Date, Name, Time In, Time Out, Work Shift Status, Total Days, and Total Hours.')
                ])
                ->action(function (array $data) {
                    $file = public_path('storage/' . $data['attachment']);
                    Excel::import(new \App\Imports\AttendanceImport, $file);
                });
        }

        return $actions;

    }
}

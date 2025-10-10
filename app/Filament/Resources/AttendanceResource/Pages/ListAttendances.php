<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms; // ✅ Add this line!
use Filament\Notifications\Notification;
use App\Models\User;
use App\Models\Attendance;
use Carbon\CarbonPeriod;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
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

                    Forms\Components\CheckboxList::make('user_ids')
                        ->label('Select Employees')
                        ->options(User::pluck('name', 'id'))
                        ->columns(2)
                        ->required(),

                    Forms\Components\TimePicker::make('time_in')
                        ->label('Time In')
                        ->required(),

                    Forms\Components\TimePicker::make('time_out')
                        ->label('Time Out')
                        ->required(),

                    Forms\Components\Select::make('work_shift_status')
                        ->label('Work Shift')
                        ->options([
                            'Whole Day' => 'Whole Day',
                            'Half Day' => 'Half Day',
                            'Overtime' => 'Overtime',
                            'Absent' => 'Absent',
                        ])
                        ->required(),
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
    }
}

<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Exception;
use App\Models\User;
use App\Models\Attendance;

class AttendanceImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public function collection(Collection $rows)
    {
        $missingEmployees = [];
        $itemsToInsert = [];

        foreach ($rows as $row) {
            // Normalize headers
            $normalized = [];
            foreach ($row as $key => $value) {
                $key = strtolower(trim($key));
                $key = str_replace([' ', '-', '/'], '_', $key);
                $normalized[$key] = $value;
            }
            $row = $normalized;

            if (empty($row['name'])) {
                continue;
            }

            $employeeName = trim($row['name'] ?? '');
            $employee = User::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($employeeName))])->first();

            if (!$employee && $employeeName !== '') {
                $missingEmployees[] = $employeeName;
            }

            // Convert Excel date values
            $convertDate = function ($value) {
                if (is_numeric($value)) {
                    return Carbon::createFromTimestamp(($value - 25569) * 86400)->format('Y-m-d');
                }
                if (!empty($value)) {
                    try {
                        return Carbon::parse($value)->format('Y-m-d');
                    } catch (Exception $e) {
                        return null;
                    }
                }
                return null;
            };

            // Convert Excel or AM/PM time values to 24-hour format
            $convertTime = function ($value) {
                if (empty($value)) {
                    return null;
                }

                try {
                    if (is_numeric($value)) {
                        $seconds = $value * 24 * 3600;
                        return gmdate('H:i:s', $seconds);
                    }

                    return Carbon::parse($value)->format('H:i:s');
                } catch (Exception $e) {
                    return null;
                }
            };

            $itemsToInsert[] = [
                'date'              => $convertDate($row['date'] ?? null),
                'user_id'           => $employee ? $employee->id : null,
                'time_in'           => $convertTime($row['time_in'] ?? null),
                'time_out'          => $convertTime($row['time_out'] ?? null),
                'work_shift_status' => $row['work_shift_status'] ?? null, // 👈 Must be in Excel
            ];
        }

        // ❌ Stop import if any employees not found
        if (!empty($missingEmployees)) {
            $list = implode(', ', array_unique($missingEmployees));

            Notification::make()
                ->title('Import Failed')
                ->body("The following 'Employee' names have no account in the system: {$list}")
                ->danger()
                ->send();

            throw new Exception("Import cancelled: Missing accounts -> {$list}");
        }

        // ✅ Save all validated rows
        foreach ($itemsToInsert as $data) {
            Attendance::create($data); // Model auto-calculates totals
        }

        Notification::make()
            ->title('Import Successful')
            ->body('All attendance records were successfully imported.')
            ->success()
            ->send();
    }
}

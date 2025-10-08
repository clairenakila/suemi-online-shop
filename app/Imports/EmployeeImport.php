<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Filament\Notifications\Notification;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;


class EmployeeImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        // Normalize Excel headers (convert keys like "Pag-IBIG Number" → "pagibig_number")
        $normalized = [];
        foreach ($row as $key => $value) {
            $key = strtolower(trim($key));
            $key = str_replace([' ', '-', '/'], '_', $key);
            $normalized[$key] = $value;
        }
        $row = $normalized;

        // Skip rows where name is missing
        if (empty($row['name'])) {
            return null;
        }

        //variable for name lookup
        $employeeName = $row['name'] ?? null;

        // Check if an employee with the same name exists
        $existingEmployee = User::where('name', $employeeName)->first();
        $email = strtolower(trim($row['email'] ?? ''));


        if ($existingEmployee && !in_array(strtolower($existingEmployee), ['n/a'])) {
            // If the status is the same, show notification and skip
            if (strtolower($existingEmployee->email) === $email) {
                Notification::make()
                    ->title('Duplicate Employee Details')
                    ->body("Employee with Name: {$employeeName} and Email: {$email} already exists.")
                    ->danger()
                    ->duration(10000)
                    ->send();
                return null; // Skip inserting this duplicate record

            }
    }

    $roleName = trim($row['role'] ?? '');

    $role = $roleName ? Role::firstOrCreate(['name' => $roleName], ['name' => $roleName]) : null;

    $data = [
        'name' => $row['name'] ?? null,
        'role_id' => $role ? $role->id : null,
        'email' => $row['email'] ?? null,
        'password' => Hash::make('password'), 
        'contact_number' => $row['contact_number'] ?? null,
        // 'user_id' => User::where('name', $userName)->value('id') ?? null,
        // 'status' => $status,
        'sss_number' => $row['sss_number'] ?? null,
        'pagibig_number' => $row['pagibig_number'] ?? null,
        'philhealth_number' => $row['philhealth_number'] ?? null,
        'hourly_rate' => $row['hourly_rate'] ?? null,
        'dailty_rate' => $row['dailty_rate'] ?? null,
    ];

    return new User($data);
}


    public function getRoleId($location)
    {
        // Check if location exists, else return null
        if (!$location) {
            return null;
        }

        // Lookup the role by location, or return null if not found
        return $role ? $role->id : null;
        $role = Role::firstOrCreate(['name' => $location], ['name' => $location]);
        return $role->id; 
    }

     public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                if (!empty($this->importedEmployees)) {
                    Notification::make()
                        ->title('Employees Imported')
                        ->body('Imported employees: ' . implode(', ', $this->importedEmployees))
                        ->success()
                        ->send();
                }
            },
        ];
    }

   
   
   
}

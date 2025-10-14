<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Filament\Notifications\Notification;

class SupplierImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        // ✅ Normalize Excel headers (e.g. "Contact Number" → "contact_number")
        $normalized = [];
        foreach ($row as $key => $value) {
            $key = strtolower(trim($key));
            $key = str_replace([' ', '-', '/'], '_', $key);
            $normalized[$key] = $value;
        }
        $row = $normalized;

        // ✅ Skip rows missing required info
        if (empty($row['name'])) {
            return null;
        }

        $supplierName = $row['name'] ?? null;
        $email = strtolower(trim($row['email'] ?? ''));

        // ✅ Check for duplicates
        $existingSupplier = Supplier::where('name', $supplierName)
            ->where('email', $email)
            ->first();

        if ($existingSupplier) {
            Notification::make()
                ->title('Duplicate Supplier Skipped')
                ->body("Supplier '{$supplierName}' with email '{$email}' already exists.")
                ->danger()
                ->duration(8000)
                ->send();
            return null;
        }

        // ✅ Create new supplier
        $supplier = new Supplier([
            'name' => $supplierName,
            'email' => $email,
            'contact_number' => $row['contact_number'] ?? null,
        ]);

        // ✅ Notify success
        Notification::make()
            ->title('Supplier Imported')
            ->body("Supplier '{$supplierName}' has been successfully added.")
            ->success()
            ->duration(5000)
            ->send();

        return $supplier;
    }
}

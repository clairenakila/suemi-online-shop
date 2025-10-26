<?php

namespace App\Imports;

use App\Models\Expense;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;

class ExpenseImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
    {
        // ✅ Normalize headers
        $normalized = [];
        foreach ($row as $key => $value) {
            $key = strtolower(trim($key));
            $key = str_replace([' ', '-', '/'], '_', $key);
            $normalized[$key] = $value;
        }
        $row = $normalized;

        // ✅ Skip if no description
        if (empty($row['description'])) {
            return null;
        }

        // ✅ Clean and parse amount (accept commas, ensure decimal)
        $amount = $row['amount'] ?? 0;
        $amount = is_string($amount)
            ? floatval(str_replace(',', '', $amount))
            : (float) $amount;

        // ✅ Create the Expense model
        $expense = new Expense([
            'description'   => trim($row['description']),
            'expense_type'  => $row['expense_type'] ?? null,
            'amount'        => $amount,
        ]);

        // ✅ Optional: notify success (only if using Filament)
        Notification::make()
            ->title('Expense Imported')
            ->body("Expense '{$expense->description}' has been added successfully.")
            ->success()
            ->duration(4000)
            ->send();

        return $expense;
    }
}

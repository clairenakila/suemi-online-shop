<?php

namespace App\Imports;

use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Category;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class InventoryImport implements ToCollection,  WithHeadingRow
{
    use Importable;

    public function collection(Collection $rows)
    {
        $missingSuppliers = [];
        $itemsToInsert = [];

        foreach ($rows as $row) {
            // Normalize Excel headers
            $normalized = [];
            foreach ($row as $key => $value) {
                $key = strtolower(trim($key));
                $key = str_replace([' ', '-', '/'], '_', $key);
                $normalized[$key] = $value;
            }
            $row = $normalized;

            if (empty($row['box_number'])) {
                continue;
            }

            $categoryDescription = trim($row['category'] ?? '');
            $category = $categoryDescription
                ? Category::firstOrCreate(['description' => $categoryDescription], ['description' => $categoryDescription])
                : null;

            $supplierName = trim($row['supplier'] ?? '');
            $supplier = User::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($supplierName))])->first();

            if (!$supplier && $supplierName !== '') {
                $missingSuppliers[] = $supplierName;
            }

            // Convert Excel numeric dates → datetime
            $convertDate = function ($value) {
                if (is_numeric($value)) {
                    return Carbon::createFromTimestamp(($value - 25569) * 86400)->format('Y-m-d H:i:s');
                }
                if (!empty($value)) {
                    try {
                        return Carbon::parse($value)->format('Y-m-d H:i:s');
                    } catch (Exception $e) {
                        return null;
                    }
                }
                return null;
            };

            $itemsToInsert[] = [
                'date_arrived'    => $convertDate($row['timestamp'] ?? null),
                'brand'         => $row['brand'] ?? null,
                'category_id'   => $category ? $category->id : null,
                'supplier_id'       => $supplier ? $supplier->id : null,
                'box_number'      => $row['box_number'] ?? null,
                'quantity'       => $row['quantity'] ?? null,
                'amount' => $row['amount'] ?? null,
                'total' => $row['total'] ?? null,
            ];
        }

        // ❌ Stop if any supplier names were not found
        if (!empty($missingSuppliers)) {
            $list = implode(', ', array_unique($missingSuppliers));

            Notification::make()
                ->title('Import Failed')
                ->body("The following suppliers have no account in the system: {$list}")
                ->danger()
                ->send();

            throw new Exception("Import cancelled: Hindi pa na create si supplier sa system. -> {$list}");
        }

        // ✅ Only save if all users exist
        foreach ($itemsToInsert as $data) {
            Inventory::create($data);
        }

        Notification::make()
            ->title('Import Successful')
            ->body('All inventories were successfully imported.')
            ->success()
            ->send();
    }
}

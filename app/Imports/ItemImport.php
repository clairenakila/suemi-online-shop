<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Filament\Notifications\Notification;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use App\Models\Item;


class ItemImport implements ToModel, WithHeadingRow
{
    use Importable;

    public function model(array $row)
{
    $normalized = [];
    foreach ($row as $key => $value) {
        $key = strtolower(trim($key));
        $key = str_replace([' ', '-', '/'], '_', $key);
        $normalized[$key] = $value;
    }
    $row = $normalized;

    if (empty($row['brand'])) {
        return null;
    }

    $categoryDescription = trim($row['category'] ?? '');
    $category = $categoryDescription
        ? Category::firstOrCreate(['description' => $categoryDescription], ['description' => $categoryDescription])
        : null;

    $user = User::where('name', $row['prepared_by'] ?? '')->first();

    // ✅ Convert Excel numeric dates to PHP datetime
    $convertDate = function ($value) {
        if (is_numeric($value)) {
            // Excel stores 1 as 1900-01-01
            return \Carbon\Carbon::createFromTimestamp(($value - 25569) * 86400)->format('Y-m-d H:i:s');
        }
        if (!empty($value)) {
            // Try parsing regular date strings like "10/3/2025 3:15:57 PM"
            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    };

    $data = [
        'created_at'    => $convertDate($row['timestamp'] ?? null),
        'brand'         => $row['brand'] ?? null,
        'order_id'      => $row['order_id'] ?? null,
        'category_id'   => $category ? $category->id : null,
        'quantity'      => $row['quantity'] ?? null,
        'capital'       => $row['capital'] ?? null,
        'selling_price' => $row['selling_price'] ?? null,
        'is_returned'   => $row['is_returned'] ?? 'No',
        'date_returned' => $convertDate($row['date_returned'] ?? null),
        'date_shipped'  => $convertDate($row['date_shipped'] ?? null),
        'live_seller'   => $row['live_seller'] ?? null,
    ];

    return new Item($data);
}


    public function getCategoryId($category)
    {
        // Check if location exists, else return null
        if (!$category) {
            return null;
        }

        $category = Category::firstOrCreate(['description' => $category], ['description' => $category]);
        return $category->id;
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

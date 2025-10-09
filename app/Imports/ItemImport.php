<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Exception;

class ItemImport implements ToCollection, WithHeadingRow
{
    use Importable;

    public function collection(Collection $rows)
    {
        $missingUsers = [];
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

            if (empty($row['brand'])) {
                continue;
            }

            $categoryDescription = trim($row['category'] ?? '');
            $category = $categoryDescription
                ? Category::firstOrCreate(['description' => $categoryDescription], ['description' => $categoryDescription])
                : null;

            $userName = trim($row['prepared_by'] ?? '');
            $user = User::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($userName))])->first();

            if (!$user && $userName !== '') {
                $missingUsers[] = $userName;
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
                'created_at'    => $convertDate($row['timestamp'] ?? null),
                'brand'         => $row['brand'] ?? null,
                'order_id'      => $row['order_id'] ?? null,
                'category_id'   => $category ? $category->id : null,
                'user_id'       => $user ? $user->id : null,
                'quantity'      => $row['quantity'] ?? null,
                'capital'       => $row['capital'] ?? null,
                'selling_price' => $row['selling_price'] ?? null,
                'is_returned'   => $row['is_returned'] ?? 'No',
                'date_returned' => $convertDate($row['date_returned'] ?? null),
                'date_shipped'  => $convertDate($row['date_shipped'] ?? null),
                'live_seller'   => $row['live_seller'] ?? null,
            ];
        }

        // ❌ Stop if any user names were not found
        if (!empty($missingUsers)) {
            $list = implode(', ', array_unique($missingUsers));

            Notification::make()
                ->title('Import Failed')
                ->body("The following 'Prepared By' names have no account in the system: {$list}")
                ->danger()
                ->send();

            throw new Exception("Import cancelled: Walang account sina: -> {$list}");
        }

        // ✅ Only save if all users exist
        foreach ($itemsToInsert as $data) {
            Item::create($data);
        }

        Notification::make()
            ->title('Import Successful')
            ->body('All items were successfully imported.')
            ->success()
            ->send();
    }
}

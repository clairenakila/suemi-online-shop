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
            // Normalize headers
            $normalized = [];
            foreach ($row as $key => $value) {
                $key = strtolower(trim($key));
                $key = str_replace([' ', '-', '/'], '_', $key);
                $normalized[$key] = $value;
            }
            $row = $normalized;

            // Skip rows without order_id or brand
            if (empty(trim($row['order_id'] ?? '')) || empty(trim($row['brand'] ?? ''))) {
                continue;
            }

            // Handle category
            $categoryDescription = trim($row['category'] ?? '');
            $category = $categoryDescription
                ? Category::firstOrCreate(['description' => $categoryDescription], ['description' => $categoryDescription])
                : null;

            // Handle user
            $userName = trim($row['prepared_by'] ?? '');
            $user = User::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($userName)])->first();
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

            // Numeric fields
            $sellingPrice = isset($row['selling_price']) ? floatval(str_replace(',', '', $row['selling_price'])) : 0;
            $shoppeeCommission = isset($row['shoppee_commission']) && $row['shoppee_commission'] !== ''
                ? floatval(str_replace(',', '', $row['shoppee_commission']))
                : 0; // default 0 to avoid NOT NULL error
            $discount = isset($row['discount']) ? floatval(str_replace(',', '', $row['discount'])) : 0;
            $capital = isset($row['capital']) ? floatval(str_replace(',', '', $row['capital'])) : 0;
            $quantity = isset($row['quantity']) ? floatval(str_replace(',', '', $row['quantity'])) : 0;

            $itemsToInsert[] = [
                'created_at'              => $convertDate($row['timestamp'] ?? null),
                'brand'                   => $row['brand'] ?? null,
                'order_id'                => $row['order_id'],
                'category_id'             => $category ? $category->id : null,
                'user_id'                 => $user ? $user->id : null,
                'quantity'                => $quantity,
                'capital'                 => $capital,
                'selling_price'           => $sellingPrice,
                'is_returned'             => $row['is_returned'] ?? 'No',
                'date_returned'           => $convertDate($row['date_returned'] ?? null),
                'date_shipped'            => $convertDate($row['date_shipped'] ?? null),
                'live_seller'             => $row['live_seller'] ?? null,
                'shoppee_commission'      => $shoppeeCommission,
                'discount'                => $discount,
                'mined_from'              => $row['mined_from'] ?? null,

                // Computed fields
                'commission_rate'          => $sellingPrice > 0 ? round($shoppeeCommission / $sellingPrice * 100, 2) : 0,
                'total_gross_sale'         => $sellingPrice - $shoppeeCommission,
                'discounted_selling_price' => $sellingPrice - $discount,
            ];
        }

        // Stop if any users missing
        if (!empty($missingUsers)) {
            $list = implode(', ', array_unique($missingUsers));

            Notification::make()
                ->title('Import Failed')
                ->body("The following 'Prepared By' names have no account in the system: {$list}")
                ->danger()
                ->send();

            throw new Exception("Import cancelled: Walang account sina: -> {$list}");
        }

        // Save items
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

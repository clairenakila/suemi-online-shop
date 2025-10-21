<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\User;


class Item extends Model
{
     protected $fillable = [
        'created_at',
        'brand',
        'order_id',
        'category_id',
        'user_id',
        'quantity',
        'capital',
        'selling_price',
        'is_returned',
        'date_returned',
        'date_shipped',
        'live_seller',
        'shoppee_commission',
        'total_gross_sale',
        'discount',
        'mined_from',
        'commission_rate',

    ];

     // Item belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Item belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
//     protected static function booted()
// {
//     static::saving(function ($item) {
//         if ($item->selling_price !== null) {
//             $item->shoppee_commission = $item->selling_price * 0.21;
//             $item->total_gross_sale = $item->selling_price - $item->shoppee_commission;
//         }
//     });

//     // 2️⃣ Handle multiple records when quantity > 1
//         static::creating(function ($item) {
//             if ($item->quantity > 1) {
//                 $quantity = $item->quantity;

//                 // Temporarily set quantity = 1 for each duplicate
//                 $data = $item->getAttributes();
//                 $data['quantity'] = 1;

//                 // Prevent the original "multiple quantity" record from saving
//                 $item->setAttribute('quantity', 1);

//                 // Create (quantity - 1) more records
//                 for ($i = 1; $i < $quantity; $i++) {
//                     static::create($data);
//                 }
//             }
//         });
    
// }




//  protected static function booted()
//     {
//         static::saving(function ($item) {
//             if ($item->selling_price !== null) {
//                 $item->shoppee_commission = $item->selling_price * 0.21;
//                 $item->total_gross_sale = $item->selling_price - $item->shoppee_commission;
//             }
//         });
//     }


protected static function booted()
{
    static::saving(function ($item) {
        if ($item->selling_price !== null) {

            //Calculate commission_rate safely without changing existing code
            $item->commission_rate = $item->selling_price > 0
                ? round(($item->shoppee_commission ?? 0) / $item->selling_price * 100, 2)
                : 0;

           

            // 💡 Total gross sale = selling price minus rounded commission
            $item->total_gross_sale = $item->selling_price - $item->shoppee_commission;

            $item->total_gross_sale = $item->selling_price - $item->shoppee_commission;

            // 💡 Compute discounted selling price (selling price - discount)
            if ($item->discount !== null) {
                $item->discounted_selling_price = $item->selling_price - $item->discount;
            } else {
                $item->discounted_selling_price = $item->selling_price;
            }

        }
    });
}


}

<?php

namespace App\Models;
use App\Models\Category;
use App\Models\Supplier;


use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable =[
        'date_arrived',
        'category_id',
        'supplier_id',
        'box_number',
        'quantity',
        'amount',
        'total',
    ];

    protected $casts = [
        'quantity' => 'float',
        'amount' => 'float',
        'total' => 'float',
    ];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Item belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

     protected static function boot()
    {
        parent::boot();

        static::saving(function ($inventory) {
            // Remove commas and non-numeric characters
            $quantity = preg_replace('/[^0-9.]/', '', $inventory->quantity ?? 0);
            $amount = preg_replace('/[^0-9.]/', '', $inventory->amount ?? 0);

            $quantity = (float) ($quantity ?: 0);
            $amount = (float) ($amount ?: 0);

            $inventory->quantity = $quantity;
            $inventory->amount = $amount;
            $inventory->total = $quantity * $amount;
        });
    }

}

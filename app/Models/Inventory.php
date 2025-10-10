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
            $quantity = $inventory->quantity ?? 0;
            $amount   = $inventory->amount ?? 0;

            $inventory->total = $quantity * $amount;
        });
    }
}

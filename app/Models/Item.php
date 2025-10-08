<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\User;


class Item extends Model
{
     protected $fillable = [
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
}

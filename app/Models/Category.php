<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item;


class Category extends Model
{
    protected $fillable = [
        'description'
    ];

    public function item()
    {
        return $this->hasMany(Item::class, 'category_id');
    }
}

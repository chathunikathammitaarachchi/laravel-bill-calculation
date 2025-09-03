<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Item extends Model
{
 use HasFactory;

    protected $table = 'item';

    protected $fillable = [
        'item_code',
        'item_name',
        'rate',
        'cost_price',
        'stock',
        'unit',
        'category',
        'discount_1',
    'discount_2',
    'discount_3',
    ];


public function prices()
    {
        return $this->hasMany(ItemPrice::class);
    }


public function itemPrices()
{
    return $this->hasMany(ItemPrice::class, 'item_id', 'item_code');
}


}



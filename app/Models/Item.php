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
        'category'
    ];


public function prices()
    {
        return $this->hasMany(ItemPrice::class);
    }




}



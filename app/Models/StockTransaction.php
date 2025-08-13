<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'item_code',
        'item_name',
        'transaction_type',
        'quantity',
        'rate',
        'price',
        'reference_no',
        'source',
        'transaction_date',
    ];



    
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
    'item_code',
    'item_name',
    'transaction_type', // IN / OUT
    'quantity',
    'rate',
    'price',
    'reference_no',
    'source',           // e.g., "Initial Stock", "GRN", "Bill"
    'transaction_date',
];



    
}
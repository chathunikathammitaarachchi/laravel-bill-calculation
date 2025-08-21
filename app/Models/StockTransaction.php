<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{


    protected $table = 'stocktransaction';
    

    protected $fillable = [
    'item_code',
    'item_name',
    'transaction_type', 
    'quantity',
    'reference_no',
    'source',           
    'transaction_date',
];

    protected static function booted()
{
    static::saving(function ($transaction) {
        if ($transaction->transaction_type === 'OUT' && $transaction->quantity > 0) {
            $transaction->quantity = -abs($transaction->quantity);
        }

        if ($transaction->transaction_type === 'IN' && $transaction->quantity < 0) {
            $transaction->quantity = abs($transaction->quantity);
        }
    });
}




}
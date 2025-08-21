<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Model;

class StockInHands extends Model
{
    protected $table = 'stock_in_hands'; 
    protected $fillable = ['item_code', 'item_name', 'stock_in', 'stock_out', 'stock_balance'];
}

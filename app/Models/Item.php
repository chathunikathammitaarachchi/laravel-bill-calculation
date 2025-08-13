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
        'stock',
    ];


    public function up()
{
    Schema::table('item', function (Blueprint $table) {
        $table->integer('stock')->default(0);
    });
}


public function stockTransactions()
{
    return $this->hasMany(StockTransaction::class);
}




}



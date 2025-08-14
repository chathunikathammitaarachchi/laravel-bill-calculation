<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyStockSummary extends Model
{
    use HasFactory;

    protected $table = 'daily_stock_summaries';

    protected $fillable = [
        'transaction_date',
        'source',
        'stock_in',
        'stock_out',
    ];

    public $timestamps = true;
}

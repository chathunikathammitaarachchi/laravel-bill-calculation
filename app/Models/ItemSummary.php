<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSummary extends Model
{

        protected $table = 'item_summaries';

    protected $fillable = [
        'item_code',
        'item_name',
        'quantity',
        'rate',
        'total_price',
        'bill_no',
        'grn_date',
    ];




public function grnMaster()
{
    return $this->belongsTo(GRNMaster::class, 'grn_id'); 

}



}
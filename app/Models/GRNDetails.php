<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GRNDetails extends Model
{
    use HasFactory;

    protected $table = 'bill_details';

    protected $fillable = [
        'bill_no',
        'item_code',
        'item_name',
        'rate',
        'quantity',
        'price',
    ];
protected $casts = [
    'grn_date' => 'date',  
];


    public function grnMaster()
    {
        return $this->belongsTo(GRNMaster::class, 'bill_no');
    }

    
}

<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class SupplierGRNDetails extends Model
{
     use HasFactory;

    protected $table = 'grndetails';

    protected $fillable = [
        'grn_no',
        'item_code',
        'item_name',
        'rate',
        'cost_price',
        'quantity',
        'price',
    ];
protected $casts = [
    'g_date' => 'date',  
];


    public function SupplierGRNMaster()
    {
        return $this->belongsTo(SupplierGRNMaster::class, 'grn_no');
    }
}

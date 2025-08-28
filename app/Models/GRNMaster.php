<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GRNMaster extends Model
{
    use HasFactory;


    protected $primaryKey = 'bill_no';
    public $incrementing = false;
    protected $keyType = 'string';


   
    protected $table = 'bill_master';

    protected $fillable = [
        'bill_no',
        'grn_date',
        'customer_name',
        'total_price',
        'total_discount',
        'received_by',
        'issued_by',
        'tobe_price',
        'customer_pay',    
        'balance',    
        
    ];


    public function details()
{
    return $this->hasMany(GRNDetails::class, 'bill_no', 'bill_no');
}


  public function itemSummaries()
    {
        return $this->hasMany(ItemSummary::class, 'bill_no', 'bill_no');
    }








    
}

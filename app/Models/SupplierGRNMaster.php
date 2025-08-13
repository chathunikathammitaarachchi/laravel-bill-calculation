<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierGRNMaster extends Model
{
     

     use HasFactory;


    protected $primaryKey = 'grn_no';
    public $incrementing = false;
    protected $keyType = 'string';



    protected $table = 'grnmaster';

 
protected $fillable = [
    'grn_no',
    'g_date',
    'supplier_name',
    'total_price',
    'tobe_price',
    'total_discount',
    'supplier_pay',
    'balance',
  

];


    public function details()
{
    return $this->hasMany(SupplierGRNDetails::class, 'grn_no', 'grn_no');
}

}

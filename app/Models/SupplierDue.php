<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class SupplierDue extends Model
{
    protected $fillable = ['supplier_name', 'grn_no', 'g_date', 'tobe_price', 'supplier_pay', 'balance'];

    public function payments()
    {
        return $this->hasMany(SupplierDuePayment::class, 'supplier_due_id');
    }
}





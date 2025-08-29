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

// DuePayment.php
class SupplierDuePayment extends Model
{
    protected $fillable = ['supplier_due_id', 'payment_method', 'amount'];

    public function supplierDue()
    {
        return $this->belongsTo(SupplierDue::class, 'supplier_due_id');
    }
}


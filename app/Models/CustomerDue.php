<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// CustomerDue.php
class CustomerDue extends Model
{
    protected $fillable = ['customer_name', 'bill_no', 'grn_date', 'tobe_price', 'customer_pay', 'balance'];

    public function payments()
    {
        return $this->hasMany(DuePayment::class, 'customer_due_id');
    }
}

// DuePayment.php
class DuePayment extends Model
{
    protected $fillable = ['customer_due_id', 'payment_method', 'amount'];

    public function customerDue()
    {
        return $this->belongsTo(CustomerDue::class, 'customer_due_id');
    }
}


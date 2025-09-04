<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDue extends Model
{
    protected $fillable = [
        'customer_name', 'bill_no', 'grn_date', 'tobe_price', 'customer_pay', 'balance',
    ];
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


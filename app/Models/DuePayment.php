<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DuePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_due_id',
        'payment_method',
        'amount',
    ];

    public function customerDue()
    {
        return $this->belongsTo(CustomerDue::class);
    }
}


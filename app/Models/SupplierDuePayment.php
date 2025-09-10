<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierDuePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_due_id',
        'payment_method',
        'amount',
        'cheque_number',
        'bank_name',
        'branch_name',
        'cheque_date',
        'is_returned',
        'return_reason',
        'return_date',
    ];

    public function supplierDue()
    {
        return $this->belongsTo(SupplierDue::class, 'supplier_due_id');
    }

    public function due()
    {
        return $this->belongsTo(SupplierDue::class, 'supplier_due_id');
    }
}

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
    ];



      public function supplierDue()
    {
        // assuming supplier_due_payments table has supplier_due_id foreign key to supplier_dues.id
        return $this->belongsTo(SupplierDue::class, 'supplier_due_id');
    }
public function due()
{
    return $this->belongsTo(SupplierDue::class, 'supplier_due_id');
}

}

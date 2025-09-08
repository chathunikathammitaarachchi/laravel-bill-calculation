<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierDue extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_name', 'grn_no', 'g_date', 'tobe_price', 'supplier_pay', 'balance'];

    // Define payments relationship
    public function payments()
    {
        return $this->hasMany(SupplierDuePayment::class, 'supplier_due_id');
    }

    // Accessor to check if any cheque payment is returned
    public function getHasChequeReturnedAttribute()
    {
        return $this->payments()
                    ->where('payment_method', 'Cheque')
                    ->where('is_returned', true)
                    ->exists();
    }
}

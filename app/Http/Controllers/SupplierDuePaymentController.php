<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\SupplierDue;
use App\Models\SupplierDuePayment;
use App\Models\SupplierGRNMaster;
use App\Models\Supplier;

class SupplierDuePaymentController extends Controller
{
   

public function payDueByCustomer(Request $request)
    {
        $request->validate([
            'supplier_name'   => 'required|string',
            'payment_method'  => 'required|string|in:Cash,Cheque',
            'amount'          => 'required|numeric|min:0.01',
            'cheque_number'   => 'required_if:payment_method,Cheque',
            'bank_name'       => 'required_if:payment_method,Cheque',
            'branch_name'     => 'required_if:payment_method,Cheque',
            'cheque_date'     => 'required_if:payment_method,Cheque|date',
        ]);

DB::transaction(function () use ($request) {
    $dues = SupplierDue::where('supplier_name', $request->supplier_name)
                ->where('balance', '>', 0)
                ->orderBy('g_date')
                ->get();

    $amountToPay = $request->amount;

    foreach ($dues as $due) {
        if ($amountToPay <= 0) break;

        $paying = min($amountToPay, $due->balance);

        // ✅ Only use $payment object method (no create())
        $payment = new SupplierDuePayment();
        $payment->supplier_due_id = $due->id;
        $payment->payment_method  = $request->payment_method;
        $payment->amount          = $paying;

        if ($request->payment_method === 'Cheque') {
            $payment->cheque_number = $request->cheque_number;
            $payment->bank_name     = $request->bank_name;
            $payment->branch_name   = $request->branch_name;
            $payment->cheque_date   = $request->cheque_date;
        }

        $payment->save(); // ✅ DB insert here

        $due->supplier_pay += $paying;
        $due->balance = $due->tobe_price - $due->supplier_pay;
        $due->save();

        $grn = SupplierGRNMaster::where('grn_no', $due->grn_no)->first();
        if ($grn) {
            $grn->supplier_pay += $paying;
            $grn->balance = $grn->tobe_price - $grn->supplier_pay;
            $grn->save();
        }

        $amountToPay -= $paying;
    }
});


        if ($request->payment_method === 'Cheque') {
            Log::info('Cheque Details:', [
                'number'    => $request->cheque_number,
                'bank'      => $request->bank_name,
                'branch'    => $request->branch_name,
                'date'      => $request->cheque_date,
                'cheque_number'   => $request->cheque_number ,
                'bank_name'       => $request->bank_name ,
                'branch_name'     => $request->branch_name ,
                'cheque_date'     => $request->cheque_date ,
                
            ]);
        }

        return redirect()
            ->route('due_payments.form.by.supplier', ['supplier_name' => $request->supplier_name])
            ->with('success', 'Payment successful!');
    }

    public function showBySupplier($supplierName)
    {
        $dues = SupplierDue::where('supplier_name', $supplierName)
                    ->where('balance', '>', 0)
                    ->orderBy('g_date')
                    ->get();

        $totalDue = $dues->sum('tobe_price');
        $totalPaid = $dues->sum('supplier_pay');
        $totalBalance = $dues->sum('balance');

        return view('bill.pay_due_by_supplier', [
    'dues' => $dues,
    'supplier_name' => $supplierName,
    'totalDue' => $totalDue,
    'totalPaid' => $totalPaid,
    'totalBalance' => $totalBalance,
]);

    }
}

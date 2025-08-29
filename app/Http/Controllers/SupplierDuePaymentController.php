<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

use App\Models\SupplierDue;
use App\Models\SupplierDuePayment;
use App\Models\SupplierGRNMaster;

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
        'cheque_date'     => 'nullable|required_if:payment_method,Cheque|date',
    ]);

    $paymentDetails = null;

    // Wrap the DB update inside a transaction to ensure atomicity
    DB::transaction(function () use ($request, &$paymentDetails) {
        // Fetch dues
        $dues = SupplierDue::where('supplier_name', $request->supplier_name)
                    ->where('balance', '>', 0)
                    ->orderBy('g_date')
                    ->get();

        $amountToPay = $request->amount;
        $totalPaid = 0;

        foreach ($dues as $due) {
            if ($amountToPay <= 0) break;

            $paying = min($amountToPay, $due->balance);

            // Create payment record
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

            $payment->save();

            // Update balances on due and GRN master
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
            $totalPaid += $paying;
        }

        $paymentDetails = [
            'supplier_name'  => $request->supplier_name,
            'payment_method' => $request->payment_method,
            'amount'         => $totalPaid,
            'cheque_number'  => $request->payment_method === 'Cheque' ? $request->cheque_number : null,
            'bank_name'      => $request->payment_method === 'Cheque' ? $request->bank_name : null,
            'branch_name'    => $request->payment_method === 'Cheque' ? $request->branch_name : null,
            'cheque_date'    => $request->payment_method === 'Cheque' ? $request->cheque_date : null,
            'payment_date'   => now()->format('Y-m-d'),
        ];
    });

    // After transaction is committed — generate and return PDF immediately
    $pdf = PDF::loadView('receipt.supplier_due_payment', $paymentDetails);

    $filename = 'receipt_'.$paymentDetails['supplier_name'].'_'.date('YmdHis').'.pdf';

    // This forces the browser to download the PDF immediately.
    return $pdf->download($filename);}

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

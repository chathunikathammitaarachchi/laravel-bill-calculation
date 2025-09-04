<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CustomerDue;
use App\Models\CustomerDuePayment;

class CustomerDuePaymentController extends Controller
{


public function showByCustomer(Request $request, $customerName)
{
    // Get all dues for this customer, optionally filtered by dates
    $duesQuery = CustomerDue::where('customer_name', $customerName);

    if ($request->filled('from_date')) {
        $duesQuery->whereDate('grn_date', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $duesQuery->whereDate('grn_date', '<=', $request->to_date);
    }

    $dues = $duesQuery->orderBy('grn_date')->get();

    // Calculate totals
    $totalDue = $dues->sum('tobe_price');
    $totalPaid = $dues->sum('customer_pay');
    $totalBalance = $dues->sum('balance');

    return view('grn.pay_due_by_customer', [
        'dues' => $dues,
        'customer_name' => $customerName,
        'totalDue' => $totalDue,
        'totalPaid' => $totalPaid,
        'totalBalance' => $totalBalance,
    ]);
}


    // Pay dues by customer
    public function payDueByCustomer(Request $request)
    {
        $request->validate([
            'customer_name'   => 'required|string',
            'payment_method'  => 'required|string|in:Cash,Cheque',
            'amount'          => 'required|numeric|min:0.01',
            'cheque_number'   => 'required_if:payment_method,Cheque',
            'bank_name'       => 'required_if:payment_method,Cheque',
            'branch_name'     => 'required_if:payment_method,Cheque',
            'cheque_date'     => 'nullable|required_if:payment_method,Cheque|date',
        ]);

        $paymentDetails = null;

        DB::transaction(function () use ($request, &$paymentDetails) {
            $dues = CustomerDue::where('customer_name', $request->customer_name)
                ->where('balance', '>', 0)
                ->orderBy('grn_date')
                ->get();

            $amountToPay = $request->amount;
            $totalPaid = 0;

            foreach ($dues as $due) {
                if ($amountToPay <= 0) break;

                $paying = min($amountToPay, $due->balance);

                $payment = new CustomerDuePayment();
                $payment->customer_due_id = $due->id;
                $payment->payment_method = $request->payment_method;
                $payment->amount = $paying;

                if ($request->payment_method === 'Cheque') {
                    $payment->cheque_number = $request->cheque_number;
                    $payment->bank_name = $request->bank_name;
                    $payment->branch_name = $request->branch_name;
                    $payment->cheque_date = $request->cheque_date;
                }

                $payment->save();

                $due->customer_pay += $paying;
                $due->balance = $due->tobe_price - $due->customer_pay;
                $due->save();

                $amountToPay -= $paying;
                $totalPaid += $paying;
            }

            $paymentDetails = [
                'customer_name'  => $request->customer_name,
                'payment_method' => $request->payment_method,
                'amount'         => $totalPaid,
                'cheque_number'  => $request->payment_method === 'Cheque' ? $request->cheque_number : null,
                'bank_name'      => $request->payment_method === 'Cheque' ? $request->bank_name : null,
                'branch_name'    => $request->payment_method === 'Cheque' ? $request->branch_name : null,
                'cheque_date'    => $request->payment_method === 'Cheque' ? $request->cheque_date : null,
                'payment_date'   => now()->format('Y-m-d'),
            ];
        });

        $pdf = Pdf::loadView('receipt.customer_due_payment', $paymentDetails);
        $filename = 'receipt_'.$paymentDetails['customer_name'].'_'.date('YmdHis').'.pdf';

        return $pdf->download($filename);
    }

    // Show list of customer dues summary
    public function showDues(Request $request)
    {
        $dues = CustomerDue::select(
            'customer_name',
            DB::raw('SUM(tobe_price) as total_due'),
            DB::raw('SUM(customer_pay) as total_paid'),
            DB::raw('SUM(balance) as total_balance'),
            DB::raw('MAX(grn_date) as last_date')
        )
        ->when($request->from_date, fn($q) => $q->whereDate('grn_date', '>=', $request->from_date))
        ->when($request->to_date, fn($q) => $q->whereDate('grn_date', '<=', $request->to_date))
        ->when($request->customer_name, fn($q) => $q->where('customer_name', $request->customer_name))
        ->groupBy('customer_name')
        ->orderBy('customer_name')
        ->get();

        return view('grn.customer_dues', compact('dues'));
    }

    // Export dues PDF for customers
    public function customerExportDuesPDF(Request $request)
    {
        $query = CustomerDue::query();

        if ($request->filled('from_date')) {
            $query->whereDate('grn_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('grn_date', '<=', $request->to_date);
        }

        if ($request->filled('customer_name')) {
            $query->where('customer_name', 'like', $request->customer_name . '%');
        }

        $dues = $query->orderBy('grn_date', 'desc')->get();

        $pdf = Pdf::loadView('bill.customer_dues_pdf', compact('dues'));
        return $pdf->download('customer_dues.pdf');
    }
}

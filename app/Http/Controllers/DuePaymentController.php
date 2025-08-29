<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

use App\Models\CustomerDue;
use App\Models\DuePayment;
use App\Models\GRNMaster;

class DuePaymentController extends Controller
{
    public function payDueByCustomer(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'payment_method' => 'required|string|in:Cash,Cheque',
            'amount' => 'required|numeric|min:0.01',
            'cheque_number' => 'required_if:payment_method,Cheque',
            'bank_name' => 'required_if:payment_method,Cheque',
            'branch_name' => 'required_if:payment_method,Cheque',
            'cheque_date' => 'nullable|required_if:payment_method,Cheque|date',
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

                // Create payment record
                $payment = new DuePayment();
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

                // Update CustomerDue
                $due->customer_pay += $paying;
                $due->balance = $due->tobe_price - $due->customer_pay;
                $due->save();

                // Update GRN Master
                $grn = GRNMaster::where('bill_no', $due->bill_no)->first();
                if ($grn) {
                    $grn->customer_pay += $paying;
                    $grn->balance = $grn->tobe_price - $grn->customer_pay;
                    $grn->save();
                }

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

        // Return PDF receipt
        $pdf = Pdf::loadView('receipt.customer_due_payment', $paymentDetails);
        $filename = 'receipt_' . $paymentDetails['customer_name'] . '_' . now()->format('YmdHis') . '.pdf';
        return $pdf->download($filename);
    }

    public function showByCustomer($customer_name)
    {
        $dues = CustomerDue::where('customer_name', $customer_name)
            ->where('balance', '>', 0)
            ->orderBy('grn_date')
            ->get();

        $totalDue = $dues->sum('tobe_price');
        $totalPaid = $dues->sum('customer_pay');
        $totalBalance = $dues->sum('balance');

        return view('grn.pay_due_by_customer', compact('dues', 'customer_name', 'totalDue', 'totalPaid', 'totalBalance'));
    }

    // ADDITIONAL FUNCTION TO CREATE DUE RECORD IF FULL PAYMENT NOT MADE
    public function createDueIfPartial(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'bill_no' => 'required|string',
            'grn_date' => 'required|date',
            'tobe_price' => 'required|numeric|min:0',
            'customer_pay' => 'required|numeric|min:0',
        ]);

        if ($request->customer_pay < $request->tobe_price) {
            CustomerDue::create([
                'customer_name' => $request->customer_name,
                'bill_no'       => $request->bill_no,
                'grn_date'      => $request->grn_date,
                'tobe_price'    => $request->tobe_price,
                'customer_pay'  => $request->customer_pay,
                'balance'       => $request->tobe_price - $request->customer_pay,
            ]);
        }

        return back()->with('success', 'GRN & due record added.');
    }
}

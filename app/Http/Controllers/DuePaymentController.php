<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerDue;
use App\Models\DuePayment;
use App\Models\GRNMaster;

class DuePaymentController extends Controller
{
    // public function showPaymentForm($id)
    // {
    //     $due = CustomerDue::findOrFail($id);
    //     return view('grn.pay_due', compact('due'));
    // }

    public function payDue(Request $request)
    {
        $request->validate([
            'customer_due_id' => 'required|exists:customer_dues,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            $due = CustomerDue::findOrFail($request->customer_due_id);

            // Save the payment in due_payments table
            DuePayment::create([
                'customer_due_id' => $due->id,
                'payment_method' => $request->payment_method,
                'amount' => $request->amount,
            ]);

            // Update CustomerDue amounts
            $due->customer_pay += $request->amount;
            $due->balance = $due->tobe_price - $due->customer_pay;
            $due->save();

            // Update GRNMaster (bill report)
            $grn = GRNMaster::where('bill_no', $due->bill_no)->first();
            if ($grn) {
                $grn->customer_pay += $request->amount;
                $grn->balance = $grn->tobe_price - $grn->customer_pay;
                $grn->save();
            }
        });

return redirect()->route('grn.dues')->with('success', 'Payment successful!');
    }
    public function payDueByCustomer(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            $dues = CustomerDue::where('customer_name', $request->customer_name)
                        ->where('balance', '>', 0)
                        ->orderBy('grn_date')
                        ->get();

            $amountToPay = $request->amount;

            foreach ($dues as $due) {
                if ($amountToPay <= 0) break;

                $paying = min($due->balance, $amountToPay);

                // Create DuePayment record
                DuePayment::create([
                    'customer_due_id' => $due->id,
                    'payment_method' => $request->payment_method,
                    'amount' => $paying,
                ]);

                // Update CustomerDue
                $due->customer_pay += $paying;
                $due->balance = $due->tobe_price - $due->customer_pay;
                $due->save();

                // Update GRNMaster
                $grn = GRNMaster::where('bill_no', $due->bill_no)->first();
                if ($grn) {
                    $grn->customer_pay += $paying;
                    $grn->balance = $grn->tobe_price - $grn->customer_pay;
                    $grn->save();
                }

                $amountToPay -= $paying;
            }
        });

return redirect()->route('due.success');
    }

    public function showByCustomer($customerName)
    {
        $dues = CustomerDue::where('customer_name', $customerName)
                    ->where('balance', '>', 0)
                    ->orderBy('grn_date')
                    ->get();

        $totalDue = $dues->sum('tobe_price');
        $totalPaid = $dues->sum('customer_pay');
        $totalBalance = $dues->sum('balance');

        return view('grn.pay_due_by_customer', compact(
            'dues', 'customerName', 'totalDue', 'totalPaid', 'totalBalance'
        ));
    }
}

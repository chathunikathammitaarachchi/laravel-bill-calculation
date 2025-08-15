<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CustomerDue;
use App\Models\DuePayment;
use App\Models\GRNMaster;
use App\Http\Controllers\Controller; 

class DuePaymentController extends Controller
{


    public function showPaymentForm($id)
{
    $due = \App\Models\CustomerDue::findOrFail($id);
    return view('grn.pay_due', compact('due'));
}

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

            // Update GRNMaster (bill report) balance and customer_pay accordingly
            $grn = GRNMaster::where('bill_no', $due->bill_no)->first();
            if ($grn) {
                $grn->customer_pay += $request->amount;
                $grn->balance = $grn->tobe_price - $grn->customer_pay;
                $grn->save();
            }
        });

return redirect()->route('due.success')->with('success', 'Payment recorded successfully.');
    }
}

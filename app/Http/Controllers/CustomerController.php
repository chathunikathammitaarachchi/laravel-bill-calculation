<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GRNMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('customer.index', compact('customers'));
    }

    public function create()
    {
        return view('customer.create');
    }
public function store(Request $request)
{
    $validated = $request->validate([
        'customer_id'   => 'required|integer|unique:customer,customer_id',
        'customer_name' => 'required|string', 
        'phone'         => 'required|string|max:15',
    ]);

    $customer = Customer::create($validated);

    if ($request->expectsJson()) {
        return response()->json($customer, 201);
    }

    // If it's a normal web form submission
    return redirect()->route('customer.index')
                     ->with('success', 'Customer added successfully.');
}


    public function edit(Customer $customer)
    {
        return view('customer.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'customer_id'   => 'required|integer|unique:customer,customer_id,' . $customer->id,
            'customer_name' => 'required|string',
            'phone'         => 'required|string|max:15',
        ]);

        $oldName = $customer->customer_name;

        $customer->update([
            'customer_id'   => $request->customer_id,
            'customer_name' => $request->customer_name,
            'phone'         => $request->phone,
        ]);

        if ($oldName !== $request->customer_name) {
            DB::table('dues') // Replace with actual dues table
                ->where('customer_name', $oldName)
                ->update(['customer_name' => $request->customer_name]);
        }

        return redirect()->route('customer.index')->with('success', '✅ Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customer.index')->with('success', '🗑️ Customer deleted successfully.');
    }


public function customerLedger(Request $request)
{
    $customerId = $request->input('customer_id');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    if (!$customerId) {
        return view('ledger.customer_ledger_form');
    }

    // Change this line - use 'id' instead of 'customer_id'
    $customer = Customer::find($customerId);  // or Customer::where('id', $customerId)->first();
    
    if (!$customer) {
        return back()->withErrors(['customer_id' => 'Invalid customer ID'])->withInput();
    }

    if ($startDate && $endDate && $startDate > $endDate) {
        return back()->withErrors(['end_date' => 'End date must be after start date'])->withInput();
    }

    $customerName = $customer->customer_name;

    // Calculate opening balance: sum of payments - total price before start date
    $openingBalanceQuery = GRNMaster::where('customer_name', $customerName);
    if ($startDate) {
        $openingBalanceQuery->where('grn_date', '<', $startDate);
    }
    $openingInvoices = $openingBalanceQuery->get();

    $openingBalance = 0;
    foreach ($openingInvoices as $invoice) {
        $debit = $invoice->customer_pay ?? 0;    // Amount paid by customer (Debit)
        $credit = $invoice->total_price ?? 0;    // Invoice total (Credit)
        $openingBalance += ($debit - $credit);   // Debit - Credit for running balance
    }

    // Fetch invoices within date range
    $invoicesQuery = GRNMaster::where('customer_name', $customerName);
    if ($startDate) $invoicesQuery->where('grn_date', '>=', $startDate);
    if ($endDate) $invoicesQuery->where('grn_date', '<=', $endDate);

    $invoices = $invoicesQuery->orderBy('grn_date')->get();

    $ledger = [];
    $runningBalance = $openingBalance;

 

    foreach ($invoices as $invoice) {
        $debit = $invoice->customer_pay ?? 0;
        $credit = $invoice->total_price ?? 0;

        $runningBalance += ($debit - $credit);

        $ledger[] = [
            'date' => $invoice->grn_date,
            'invoice_no' => $invoice->bill_no,    
            'description' => 'Sale - Invoice ' . $invoice->bill_no,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $runningBalance,
        ];
    }

    return view('ledger.customer_ledger', compact('ledger', 'customerName', 'startDate', 'endDate', 'openingBalance'));
}

    // search for customers
    public function customerSearch(Request $request)
    {
        $query = $request->get('query', '');
        if (!$query) return response()->json([]);

        $customers = Customer::where('customer_id', 'ILIKE', "%{$query}%")
            ->orWhere('customer_name', 'ILIKE', "%{$query}%")
            ->limit(10)
            ->get(['id', 'customer_id', 'customer_name']);

        return response()->json($customers);
    }
}

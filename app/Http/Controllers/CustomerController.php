<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

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
        $request->validate([
            'customer_id'   => 'required|integer|unique:customer,customer_id',
            'customer_name' => 'required|string',
            'phone'         => 'required|string',
        ]);

        $customer = Customer::create([
            'customer_id'   => $request->customer_id,
            'customer_name' => $request->customer_name,
            'phone'         => $request->phone,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'customer_id'   => $customer->customer_id,
                'customer_name' => $customer->customer_name,
            ]);
        }

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
            'customer_id' => 'required|integer|unique:customer,customer_id,' . $customer->id,
            'customer_name' => 'required|string',
            'phone' => 'required|string',
        ]);

        $customer->update($request->all());




        // Get the old customer name before update
    $oldCustomerName = $customer->customer_name;

    // Update customer
    $customer->update($request->all());

    // If customer name changed, update it in dues table (if dues use customer_name)
    if ($oldCustomerName !== $request->customer_name) {
        \DB::table('dues') // Replace 'dues' with your actual dues table
            ->where('customer_name', $oldCustomerName)
            ->update(['customer_name' => $request->customer_name]);
    }

        return redirect()->route('customer.index')->with('success', 'customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customer.index')->with('success', 'customer deleted successfully.');
    }
}

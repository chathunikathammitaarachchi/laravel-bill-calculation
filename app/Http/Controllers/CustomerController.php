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
    'customer_id' => 'required|integer|unique:customer,customer_id',
    'customer_name' => 'required|string',
    'phone' => 'required|string',

]);

        Customer::create($request->all());

        return redirect()->route('customer.index')->with('success', 'Customer added successfully.');
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

        return redirect()->route('customer.index')->with('success', 'customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customer.index')->with('success', 'customer deleted successfully.');
    }
}

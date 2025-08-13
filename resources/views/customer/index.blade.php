@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Customer List</h2>
        <a href="{{ route('customer.create') }}" class="btn btn-primary mb-3">Add New Customer</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer ID </th>
                    <th>Customer Name</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
       <tbody>
    @forelse ($customers as $customer)
        <tr>
            <td>{{ $customer->id }}</td>
            <td>{{ $customer->customer_id }}</td>
            <td>{{ $customer->customer_name }}</td>
            <td>{{ $customer->phone }}</td>
            <td>
                <a href="{{ route('customer.edit', $customer) }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ route('customer.destroy', $customer) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center">No customers found.</td>
        </tr>
    @endforelse
</tbody>

        </table>
    </div>
@endsection

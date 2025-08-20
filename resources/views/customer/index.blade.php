@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Customer List</h2>
        <a href="{{ route('customer.create') }}" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-lg"></i> Add New Customer
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-primary">
                <tr>
                    <th>Number</th>
                    <th>Customer ID</th>
                    <th>Customer Name</th>
                    <th>Phone Number</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $customer->customer_id }}</td>
                    <td>{{ $customer->customer_name }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td class="text-center">
                        <a href="{{ route('customer.edit', $customer) }}" class="btn btn-warning btn-sm me-2" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('customer.destroy', $customer) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted fst-italic py-4">No customers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<style>
  body {
    background: linear-gradient(135deg, #e0f7f1 0%, #dbe8f5 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
</style>
<!-- Make sure Bootstrap Icons are loaded -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

@endsection

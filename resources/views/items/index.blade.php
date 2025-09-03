@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Items List</h2>
        <a href="{{ route('items.create') }}" class="btn btn-success shadow-sm" title="Add New Item">
            <i class="bi bi-plus-lg"></i> Add New Item
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-striped table-hover align-middle mb-0 text-center">
            <thead class="table-primary">
                <tr>
                    <th scope="col">Number</th>
                    <th scope="col">Item Code</th>
                    <th scope="col" class="text-start">Item Name</th>
                    <th scope="col">Rate</th>
                    <th scope="col">Cost Price</th>
                    <th scope="col">Category</th>
                    <th scope="col">Unit</th>
                    <th scope="col">Stock</th>

                    <th scope="col">Discount 1</th> 
                    <th scope="col">Qty 1</th> 

                    <th scope="col">Discount 2</th> 
                    <th scope="col">Qty 2</th> 

                    <th scope="col">Discount 3</th> 
                    <th scope="col">Qty 3</th> 

                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->item_code }}</td>
                        <td class="text-start">{{ $item->item_name }}</td>
                        <td>Rs. {{ number_format($item->rate, 2) }}</td>
                        <td>Rs. {{ number_format($item->cost_price, 2) }}</td>
                        <td>{{ $item->category }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>{{ $item->stock }}</td>

                        <td>Rs. {{ $item->discount_1 ?? '-' }}</td>
                        <td>{{ $item->discount_1_qty ?? '-' }}</td>

                        <td>Rs. {{ $item->discount_2 ?? '-' }}</td>
                        <td>{{ $item->discount_2_qty ?? '-' }}</td>

                        <td>Rs. {{ $item->discount_3 ?? '-' }}</td>
                        <td>{{ $item->discount_3_qty ?? '-' }}</td>

                        <td class="text-center">
                            <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-warning me-2" title="Edit Item">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure to delete this item?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" title="Delete Item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="15" class="text-center text-muted fst-italic py-4">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Custom Styles -->
<style>
    body {
        background: linear-gradient(135deg, #e0f7f1 0%, #dbe8f5 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    table tbody tr:hover {
        background-color: #cce5ff !important;
        cursor: pointer;
    }
    .table thead th {
        vertical-align: middle;
        font-weight: 600;
    }
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.9rem;
        }
        .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    }
</style>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

@endsection

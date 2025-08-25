@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Items List</h2>
        <a href="{{ route('items.create') }}" class="btn btn-success shadow-sm">
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
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-primary">
                <tr>
                    <th>Number</th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Rate</th>
                    <th>Cost Price</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th>Stock</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->item_code }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>Rs: {{ number_format($item->rate, 2) }}</td>
                        <td>Rs: {{ number_format($item->cost_price, 2) }}</td>
                        <td>{{ $item->category }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>{{ $item->stock }}</td>
                        <td class="text-center">
                            <a href="{{ route('items.edit', $item) }}" class="btn btn-warning btn-sm me-2">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted fst-italic py-4">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Styles -->
<style>
  body {
    background: linear-gradient(135deg, #e0f7f1 0%, #dbe8f5 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
</style>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endsection

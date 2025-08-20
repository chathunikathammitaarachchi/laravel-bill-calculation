@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">Supplier List</h2>
        <a href="{{ route('supplier.create') }}" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-lg"></i> Add New Supplier
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
            <thead class="table-primary text-center">
                <tr>
                    <th>Number</th>
                    <th>Supplier ID</th>
                    <th>Supplier Name</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $supplier->supplier_id }}</td>
                        <td>{{ $supplier->supplier_name }}</td>
                        <td>{{ $supplier->phone }}</td>
                        <td>{{ $supplier->address }}</td>
                        <td class="text-center">
                            <a href="{{ route('supplier.edit', $supplier) }}" class="btn btn-warning btn-sm me-1" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('supplier.destroy', $supplier) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure?');">
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
                        <td colspan="6" class="text-center text-muted fst-italic py-4">No suppliers found.</td>
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


    .table thead th {
        background-color: #e3f2fd;
        color: #5186d6ff;
        font-weight: bold;
    }

    .table-hover tbody tr:hover {
        background-color: #e0f0ff;
    }

    .btn i {
        vertical-align: middle;
    }
</style>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endsection

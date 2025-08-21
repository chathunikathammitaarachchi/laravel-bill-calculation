@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color:white>
            <i class="bi bi-box-seam me-2"></i>Stock Management
        </h2>
        <a href="{{ route('items.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back to Items
        </a>
    </div>



    <form method="GET" action="{{ route('items.stock') }}" class="mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-auto">
            <label for="date" class="form-label">Filter by Date:</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-filter-circle me-1"></i> Apply Filter
            </button>
            <a href="{{ route('items.stock') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Clear
            </a>
        </div>
    </div>
</form>


    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom fw-semibold">
            <i class="bi bi-table me-2"></i>Inventory Overview
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center text-uppercase small">
                        <tr>
                            <th style="width: 20%;">Item Code</th>
                            <th style="width: 40%;">Item Name</th>
                            <th style="width: 20%;">Current Stock</th>
                            <th style="width: 20%;">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @forelse($items as $item)
                            <tr>
                                <td class="text-uppercase fw-semibold">{{ $item->item_code }}</td>
                                <td class="text-center">{{ $item->item_name }}</td>
                                <td class="fw-bold">{{ $item->stock }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($item->status) {
                                            'Available' => 'success',
                                            'Low Stock' => 'warning text-dark',
                                            'Out of Stock' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }} px-3 py-2 rounded-pill">
                                        {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-muted py-4">No items found.</td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if(count($items) > 0)
                    <tfoot>
                        <tr class="bg-light border-top">
                            <td colspan="2" class="text-end fw-semibold text-uppercase py-3">Total Current Stock</td>
                            <td class="text-center fw-bold">{{ $totalStock }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

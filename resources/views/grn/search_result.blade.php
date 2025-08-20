@extends('layouts.app')

@section('content')
<div class="container mt-5">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Card --}}
    <div class="card shadow-lg border-0 rounded-4 p-4">
        <h3 class="text-center mb-4 text-primary fw-bold">Bill Summary</h3>

        {{-- Bill Header --}}
        <div class="row mb-3">
            <div class="col-md-6"><strong>Bill No:</strong> {{ $grn->bill_no }}</div>
            <div class="col-md-6"><strong>Date:</strong> {{ $grn->grn_date }}</div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6"><strong>Customer Name:</strong> {{ $grn->customer_name }}</div>
        </div>

        {{-- Items Table --}}
        <h5 class="mt-4 mb-3 text-secondary">Items Purchased</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th class="text-end">Rate</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grn->details as $item)
                        <tr>
                            <td class="text-center">{{ $item->item_code }}</td>
                            <td class="text-center">{{ $item->item_name }}</td>
                            <td class="text-end">{{ number_format($item->rate, 2) }}</td>
                            <td class="text-end">{{ $item->quantity }}</td>
                            <td class="text-end">{{ number_format($item->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <h5 class="mt-4 text-secondary">Payment Summary</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>Total Price</th>
                    <td class="text-end">{{ number_format($grn->total_price, 2) }}</td>
                </tr>
                <tr>
                    <th>Total Discount</th>
                    <td class="text-end">{{ number_format($grn->total_discount, 2) }}</td>
                </tr>
                <tr>
                    <th>Amount to be Paid</th>
                    <td class="text-end">{{ number_format($grn->tobe_price, 2) }}</td>
                </tr>
                <tr>
                    <th>Customer Pay</th>
                    <td class="text-end">{{ number_format($grn->customer_pay, 2) }}</td>
                </tr>
                <tr>
                    <th>Balance</th>
                    <td class="text-end">{{ number_format($grn->balance, 2) }}</td>
                </tr>
            </table>
        </div>

        {{-- Issued/Received --}}
        <div class="row mt-4">
            <div class="col-md-6"><strong>Issued By:</strong> {{ $grn->issued_by }}</div>
            <div class="col-md-6"><strong>Received By:</strong> {{ $grn->received_by }}</div>
        </div>

        {{-- Buttons --}}
        <div class="mt-4 text-end">
            <a href="{{ route('grn.edit', $grn->bill_no) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
            <a href="{{ route('grn.create') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Search
            </a>
        </div>
    </div>
</div>
@endsection

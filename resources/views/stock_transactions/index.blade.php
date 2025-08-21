@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Stock In Hand</h2>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('stock.transactions') }}" class="row g-3 mb-3">
        <div class="col-auto">
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Start Date">
        </div>
        <div class="col-auto">
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="End Date">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-secondary">Filter by Date Range</button>
            <a href="{{ route('stock.transactions') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <!-- Filter Tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ request('type') === null ? 'active' : '' }}" 
                href="{{ route('stock.transactions', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}">
                All Transactions
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('type') === 'IN' ? 'active' : '' }}" 
                href="{{ route('stock.transactions', ['type' => 'IN', 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}">
                Stock In
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('type') === 'OUT' ? 'active' : '' }}" 
                href="{{ route('stock.transactions', ['type' => 'OUT', 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}">
                Stock Out
            </a>
        </li>
    </ul>

    <!-- PDF + Chart Buttons -->
    <a href="{{ route('stock.transactions.pdf', request()->query()) }}" class="btn btn-danger mb-3" target="_blank">
        Download PDF Report
    </a>

    <a href="{{ route('showPieChart', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-primary mb-3">
        View Stock Charts
    </a>

    <!-- Summary -->
    @if(request('start_date') && request('end_date'))
        <div class="mb-3">
            <strong>Summary for period {{ request('start_date') }} to {{ request('end_date') }}:</strong>
            <ul>
                <li>IN: {{ $summary['IN'] ?? 0 }}</li>
                <li>OUT: {{ $summary['OUT'] ?? 0 }}</li>
            </ul>
        </div>
    @endif

    <!-- Transactions Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Reference No</th>
                <th>Source</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->item_code }}</td>
                    <td>{{ $transaction->item_name }}</td>
                    <td>
                        <span class="badge bg-{{ $transaction->transaction_type === 'IN' ? 'success' : 'danger' }}">
                            {{ $transaction->transaction_type }}
                        </span>
                    </td>
                    <td class="{{ $transaction->transaction_type === 'IN' ? 'text-success' : 'text-danger' }}">
                        {{ $transaction->quantity }}
                    </td>
                    <td>{{ $transaction->reference_no }}</td>
                    <td>{{ $transaction->source }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No transactions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

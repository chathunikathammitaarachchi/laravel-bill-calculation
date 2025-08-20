@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-primary fw-bold">📦 Stock Transaction Ledger</h2>

    {{-- Filter Form --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('stock.history') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label fw-semibold">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label fw-semibold">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="item_code" class="form-label fw-semibold">Item Code</label>
                        <input type="text" name="item_code" id="item_code" value="{{ request('item_code') }}" class="form-control" placeholder="Search by code">
                    </div>
                    <div class="col-md-3">
                        <label for="item_name" class="form-label fw-semibold">Item Name</label>
                        <input type="text" name="item_name" id="item_name" value="{{ request('item_name') }}" class="form-control" placeholder="Search by name">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12 d-flex gap-2">
                        <button class="btn btn-primary w-100" type="submit">🔍 Filter</button>
                        <a href="{{ route('stock.history') }}" class="btn btn-secondary w-100">🔄 Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- PDF Download --}}
    <div class="mb-4 text-end">
        <a href="{{ route('stock.history.pdf', [
            'start_date' => request('start_date'),
            'end_date' => request('end_date'),
            'item_code' => request('item_code'),
            'item_name' => request('item_name')
        ]) }}" 
        class="btn btn-outline-danger">
            📄 Download PDF
        </a>
    </div>

    {{-- Transactions Grouped by Item --}}
    @forelse($groupedHistory as $code => $transactions)

    
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0 fw-bold">🧾 Item Code: {{ $code }} — {{ $transactions[0]['name'] }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-around text-center">
                    <div>
                        <p class="mb-1 text-muted">Opening IN</p>
                        <p class="fs-5 fw-semibold text-success">{{ $openingInBalance[$code] ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="mb-1 text-muted">Opening OUT</p>
                        <p class="fs-5 fw-semibold text-danger">{{ $openingOutBalance[$code] ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="mb-1 text-muted">Net Opening</p>
                        <p class="fs-5 fw-semibold text-info">
                            {{ ($openingInBalance[$code] ?? 0) - ($openingOutBalance[$code] ?? 0) }}
                        </p>
                    </div>
                </div>

                

                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $tx)
                                <tr>
                                    <td>{{ $tx['date'] }}</td>
                                    <td>
                                        <span class="badge {{ $tx['type'] === 'IN' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $tx['type'] }}
                                        </span>
                                    </td>
                                    <td>{{ $tx['qty'] }}</td>
                                    <td>{{ $tx['balance'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-around text-center">
                    <div>
                        <p class="mb-1 text-muted">Total IN</p>
                        <p class="fs-5 fw-semibold text-success">{{ $inTotal[$code] ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="mb-1 text-muted">Total OUT</p>
                        <p class="fs-5 fw-semibold text-danger">{{ $outTotal[$code] ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="mb-1 text-muted">Final Balance</p>
                        <p class="fs-5 fw-bold">
                            {{ ($openingInBalance[$code] ?? 0) - ($openingOutBalance[$code] ?? 0) + ($inTotal[$code] ?? 0) - ($outTotal[$code] ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center">
            No stock transactions found for the selected filters.
        </div>
    @endforelse
</div>
@endsection

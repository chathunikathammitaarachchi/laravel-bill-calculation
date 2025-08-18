@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Stock Transaction Ledger</h2>

    <form method="GET" action="{{ route('stock.history') }}" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label>Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary">Filter</button>
                <a href="{{ route('stock.history') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    @foreach($groupedHistory as $code => $transactions)
        <h5 class="mt-4">Item Code: {{ $code }} | Item Name: {{ $transactions[0]['name'] }}</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th>Stock In Hand</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row['date'])->format('Y-m-d') }}</td>
                        <td>{{ $row['type'] }}</td>
                        <td class="{{ $row['qty'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $row['qty'] >= 0 ? '+' : '' }}{{ $row['qty'] }}
                        </td>
                        <td>{{ $row['balance'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>
@endsection

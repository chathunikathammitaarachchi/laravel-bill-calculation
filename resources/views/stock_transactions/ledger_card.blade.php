@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Stock Ledger Card</h2>

    <form method="GET" action="{{ route('stock.ledger') }}" class="mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <input type="text" name="item_code" class="form-control" placeholder="Enter Item Code" value="{{ old('item_code', $search ?? '') }}" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div>
    </form>

    @if($itemCode)
        <h5>Item Code: {{ $itemCode }} @if($itemName) - {{ $itemName }} @endif</h5>
    @endif

    @if(count($ledger) === 0)
        <div class="alert alert-warning mt-3">
            No transactions found for this item.
        </div>
    @else
        <table class="table table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Quantity In</th>
                    <th>Quantity Out</th>
                    <th>Running Balance</th>
                    <th>Source</th>
                    <th>Reference No</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ledger as $entry)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($entry['date'])->format('Y-m-d') }}</td>
                    <td>{{ $entry['type'] }}</td>
                    <td>{{ $entry['qty_in'] }}</td>
                    <td>{{ $entry['qty_out'] }}</td>
                    <td>{{ $entry['balance'] }}</td>
                    <td>{{ $entry['source'] }}</td>
                    <td>{{ $entry['reference_no'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Stock On Hand Report</h1>

 

    <!-- Stock Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th class="text-end">Stock In</th>
                    <th class="text-end">Stock Out</th>
                    <th class="text-end">Stock Balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockInHands as $stock)
                    <tr>
                        <td>{{ $stock->item_code }}</td>
                        <td>{{ $stock->item_name }}</td>
                        <td class="text-end">{{ number_format($stock->stock_in, 2) }}</td>
                        <td class="text-end">{{ number_format($stock->stock_out, 2) }}</td>
                        <td class="text-end fw-bold">{{ number_format($stock->stock_balance, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No stock data found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

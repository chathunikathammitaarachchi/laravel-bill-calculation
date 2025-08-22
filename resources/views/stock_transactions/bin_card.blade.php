@extends('layouts.app')

@section('content')
<div class="container">
    <h2 style="color:white" >Stock Bin Card</h2>

    <!-- Search & Date Filter Form -->
    <form method="GET" action="{{ route('stock.bin_card') }}" class="mb-4">
        <div class="row g-3">
            <div class="col-md-3"style="color:white">
                <label for="search">Item Code</label>
                <input type="text" id="search" name="search" value="{{ old('search', $search) }}" class="form-control" placeholder="Enter item code">
            </div>

            <div class="col-md-3"style="color:white">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $startDate) }}" class="form-control">
            </div>

            <div class="col-md-3"style="color:white">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $endDate) }}" class="form-control">
            </div>

            <div class="col-md-3 d-flex flex-column flex-md-row align-items-end gap-2">
    <button type="submit" class="btn btn-primary flex-fill">Filter</button>
    <a href="{{ route('stock.bin_card') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
</div>

        </div>
    </form>


  <!-- Bin Card Table -->
@if(count($binCard) > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Source</th>
                    <th>Qty In</th>
                    <th>Qty Out</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($binCard as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row['date'])->format('Y-m-d') }}</td>
                        <td>{{ $row['source'] }}</td>
                        <td class="text-center">
                            @if($row['quantity_in'] > 0)
                                {{ $row['quantity_in'] }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($row['quantity_out'] > 0)
                                {{ $row['quantity_out'] }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $row['balance'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="table-secondary">
                    <th colspan="2" class="text-end">Totals:</th>
                    <th>{{ $totalIn > 0 ? $totalIn : '-' }}</th>
                    <th>{{ $totalOut > 0 ? $totalOut : '-' }}</th>
                    <th>{{ $finalBalance }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
@else
    <div class="alert alert-info">
        No transactions found for the given filters.
    </div>
@endif

</div>
@endsection

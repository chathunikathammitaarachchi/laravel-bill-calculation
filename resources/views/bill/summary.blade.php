@extends('layouts.app')

@section('content')
<style>
    .container {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }

    h2 {
        font-weight: 600;
        color: #333;
    }

    form.form-inline {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .form-group input {
        min-width: 180px;
    }

    .btn-primary {
        background-color: #0069d9;
        border-color: #0062cc;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    table.table {
        margin-top: 20px;
        background-color: #f9f9f9;
    }

    thead.thead-light th {
        background-color: #f1f1f1;
        color: #333;
        font-weight: 500;
        text-align: center;
    }

    tbody td {
        text-align: center;
        vertical-align: middle;
    }

    .btn-info {
        background-color: #17a2b8;
        border-color: #138496;
    }

    @media (max-width: 768px) {
        .form-inline {
            flex-direction: column;
            align-items: stretch;
        }

        .form-group,
        .btn {
            width: 100%;
        }
    }
</style>

<div class="container">
    <h2 class="mb-4">GRN Summary</h2>
 <form method="GET" class="form-inline mb-3">
        <div class="form-group mr-2">
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control" placeholder="From date">
        </div>
        <div class="form-group mr-2">
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control" placeholder="To date">
        </div>
        <button type="submit" class="btn btn-primary mr-2">Filter</button>
        <a href="{{ route('bill.summary') }}" class="btn btn-secondary">Clear</a>
    </form>
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Date</th>
                <th>GRN Count</th>
                <th>Total Price</th>
                <th>Total Discount</th>
                <th>Total Issued</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    @foreach($dailySummaries as $summary)
        <tr>
            <td>{{ $summary['date'] }}</td>
            <td>{{ $summary['grn_count'] }}</td>
            <td>Rs {{ number_format($summary['total_price'], 2) }}</td>
            <td>Rs {{ number_format($summary['total_discount'], 2) }}</td>
            <td>Rs {{ number_format($summary['total_issued'], 2) }}</td>
            <td>
                <a href="{{ route('bill.details', ['date' => $summary['date']]) }}" class="btn btn-sm btn-info">View</a>
            </td>
        </tr>
    @endforeach

    {{-- Total row --}}
    <tr style="font-weight: bold; background-color: #e9ecef;">
        <td>Total</td>
        <td>{{ $totals['grn_count'] }}</td>
        <td>Rs {{ number_format($totals['total_price'], 2) }}</td>
        <td>Rs {{ number_format($totals['total_discount'], 2) }}</td>
        <td>Rs {{ number_format($totals['total_issued'], 2) }}</td>
        <td></td>
    </tr>
</tbody>

    </table>

    <a href="{{ route('bill.summary.pdf', request()->only('from_date', 'to_date')) }}" 
   class="btn btn-success ml-2">
   ⬇ Download PDF
</a>

</div>
@endsection

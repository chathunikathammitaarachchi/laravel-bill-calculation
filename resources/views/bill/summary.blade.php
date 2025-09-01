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
        min-width: 30px;
        
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
   @if(request('from_date') && request('to_date') && count($dailySummaries) > 0)
    <table class="table table-bordered">
    <thead class="thead-light">
        <tr>
            <th>Date</th>
            <th>GRN Count</th>
            <th>Total Price</th>
            <th>Total Discount</th>
            <th>Total Issued</th>
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
            </tr>
        @endforeach

        {{-- Totals row --}}
        <tr style="font-weight: bold; background-color: #e9ecef;">
            <td>Total</td>
            <td>{{ $totals['grn_count'] }}</td>
            <td>Rs {{ number_format($totals['total_price'], 2) }}</td>
            <td>Rs {{ number_format($totals['total_discount'], 2) }}</td>
            <td>Rs {{ number_format($totals['total_issued'], 2) }}</td>
        </tr>
    </tbody>
</table>


<form method="GET" action="{{ route('bill.summary.pdf') }}" target="pdfFrame" class="d-inline">
    <input type="hidden" name="from_date" value="{{ request('from_date') }}">
    <input type="hidden" name="to_date" value="{{ request('to_date') }}">
    <button type="submit" class="btn btn-success ml-2">⬇ Download & Print PDF</button>
</form>

<iframe id="pdfFrame" name="pdfFrame" style="display:none;" onload="printIframe()"></iframe>

<script>
    function printIframe() {
        const iframe = document.getElementById('pdfFrame');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }
    }
</script>

    <a href="{{ route('bill.details', ['from' => request('from_date'), 'to' => request('to_date')]) }}" 
       class="btn btn-info ml-2">
        🔍 View Filtered Details
    </a>
@elseif(request('from_date') && request('to_date'))
    <div class="alert alert-warning mt-3">
        No GRNs found for the selected date range.
    </div>
@endif

</div>
@endsection

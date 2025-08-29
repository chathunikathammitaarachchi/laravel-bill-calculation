@extends('layouts.app')

@section('content')
<style>
    .container {
        background-color: #1e1e2f;
        color: #ffffff;
        padding: 30px;
        border-radius: 10px;
    }
    h3 {
        color: #ffffff;
        border-bottom: 2px solid #444;
        padding-bottom: 10px;
    }
    .alert-warning {
        background-color: #ffc107;
        color: #000;
        padding: 10px;
        border-radius: 5px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th {
        background-color: #343a40;
        color: #ffffff;
        padding: 12px;
        border: 1px solid #555;
        text-align: center;
    }
    td {
        background-color: #2c2f3a;
        color: #ffffff;
        padding: 10px;
        border: 1px solid #444;
        text-align: center;
    }
    .totals-row td {
        background-color: #3a3f51;
        font-weight: bold;
    }
    .btn {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
    }
    .btn-download {
        background-color: #28a745;
        color: #ffffff;
    }
    .btn-back {
        background-color: #6c757d;
        color: #ffffff;
        margin-left: 10px;
    }
</style>

<div class="container">
    <h3>GRN Details for {{ $rangeLabel }}</h3>

    {{-- Check if groupedGrns is empty instead of $grns --}}
    @if($groupedGrns->isEmpty())
        <div class="alert-warning">
            No GRNs found for this {{ strpos($rangeLabel, 'to') !== false ? 'range' : 'date' }}.
        </div>
    @else
        {{-- Loop through each date group --}}
        @foreach($groupedGrns as $date => $grns)
    <h4 style="margin-top: 30px;">Date: {{ $date }}</h4>
    <table>
        <thead>
            <tr>
                <th>GRN No</th>
                <th>Supplier</th>
                <th>Total Price</th>
                <th>Total Discount</th>
                <th>Total Issued</th>
                <th>Paid</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grns as $grn)
                <tr>
                    <td>{{ $grn->grn_no }}</td>
                    <td>{{ $grn->supplier_name }}</td>
                    <td>Rs {{ number_format($grn->total_price, 2) }}</td>
                    <td>Rs {{ number_format($grn->total_discount, 2) }}</td>
                    <td>Rs {{ number_format($grn->tobe_price, 2) }}</td>
                    <td>Rs {{ number_format($grn->supplier_pay, 2) }}</td>
                    <td>Rs {{ number_format($grn->balance, 2) }}</td>
                </tr>
            @endforeach

            {{-- Per-date totals row --}}
            <tr class="totals-row">
                <td colspan="2">Total for {{ $date }}</td>
                <td>Rs {{ number_format($grns->sum('total_price'), 2) }}</td>
                <td>Rs {{ number_format($grns->sum('total_discount'), 2) }}</td>
                <td>Rs {{ number_format($grns->sum('tobe_price'), 2) }}</td>
                <td>Rs {{ number_format($grns->sum('supplier_pay'), 2) }}</td>
                <td>Rs {{ number_format($grns->sum('balance'), 2) }}</td>
            </tr>
        </tbody>
    </table>
@endforeach


        {{-- Overall totals --}}
       <table style="margin-top: 40px;">
    <tr class="totals-row">
        <td colspan="2">Overall Total</td>
        <td>Rs {{ number_format($totals['total_price'], 2) }}</td>
        <td>Rs {{ number_format($totals['total_discount'], 2) }}</td>
        <td>Rs {{ number_format($totals['total_issued'], 2) }}</td>
        <td>Rs {{ number_format($totals['total_paid'], 2) }}</td>
        <td>Rs {{ number_format($totals['total_balance'], 2) }}</td>
    </tr>
</table>

    @endif

    {{-- Download PDF with parameters --}}
    @php
        $pdfParams = [];
        if (strpos($rangeLabel, 'to') !== false) {
            [$from, $to] = explode(' to ', $rangeLabel);
            $pdfParams = ['from' => $from, 'to' => $to];
        } else {
            $pdfParams = ['date' => $rangeLabel];
        }
    @endphp

    <a href="{{ route('bill.details.pdf', $pdfParams) }}" class="btn btn-download">
        ⬇ Download PDF
    </a>
    <a href="{{ route('bill.summary') }}" class="btn btn-back">
        ← Back to Summary
    </a>
</div>
@endsection

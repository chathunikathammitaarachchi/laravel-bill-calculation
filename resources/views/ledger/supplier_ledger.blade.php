@extends('layouts.app')

@section('content')
<div class="container">
<h2 style="color: #ffffff; font-family: Arial, sans-serif;">
    Supplier Ledger for: <strong style="color: #ffcc00;">{{ $supplierName }}</strong>
</h2>

@if($startDate && $endDate)
    <p style="color: #dddddd; font-family: Arial, sans-serif;">Period: {{ $startDate }} to {{ $endDate }}</p>
@elseif($startDate)
    <p style="color: #dddddd; font-family: Arial, sans-serif;">From: {{ $startDate }}</p>
@elseif($endDate)
    <p style="color: #dddddd; font-family: Arial, sans-serif;">Until: {{ $endDate }}</p>
@endif

<table class="table table-bordered table-striped" style="font-family: Arial, sans-serif; background-color: #222; color: #eee;">
    <thead>
        <tr>
            <th>Date</th>
            <th>Bill No</th>
            <th>Description</th>
            <th style="text-align: right;">Debit</th>
            <th style="text-align: right;">Credit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ledger as $entry)
            <tr @if(isset($entry['is_opening']) && $entry['is_opening']) style="font-weight:bold;" @endif>
                <td>{{ $entry['date'] }}</td>
                <td>{{ $entry['bill_no'] }}</td>
                <td>
                    {{ $entry['description'] }}
                    @if(!empty($entry['is_return']) && $entry['is_return'])
                        <span class="badge bg-danger">Cheque Return</span>
                    @endif
                </td>
                <td style="text-align: right;">
                    {{ $entry['debit'] !== '' ? number_format($entry['debit'], 2) : '' }}
                </td>
                <td style="text-align: right;">
                    {{ $entry['credit'] !== '' ? number_format($entry['credit'], 2) : '' }}
                </td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr style="background-color: #333; font-weight: bold;">
            <td colspan="3" style="text-align: right;">Totals:</td>
            <td style="text-align: right; background-color: #ffffffff;">{{ number_format($totalPaid, 2) }}</td>
            <td style="text-align: right; background-color: #ffffffff;">{{ number_format($totalToBePaid, 2) }}</td>
        </tr>
        <tr style="background-color: #333;">
            <td colspan="4" style="text-align: right; font-weight: bold;">Balance (Credit - Debit):</td>
            <td style="text-align: right; background-color: #ffffffff;">{{ number_format($totalToBePaid - $totalPaid, 2) }}</td>
        </tr>
        <tr style="background-color: #333;">
            <td colspan="4" style="text-align: right; font-weight: bold;">Total Returned (Cheque Returns):</td>
            <td style="text-align: right; background-color: #ffffffff;">{{ number_format($totalReturned, 2) }}</td>
        </tr>
    </tfoot>
</table>

<a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
</div>
@endsection

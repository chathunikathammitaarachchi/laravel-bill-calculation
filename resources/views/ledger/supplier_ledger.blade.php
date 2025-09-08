@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 900px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 10px;">
    <h4 class="text-center mb-3">Ledger for Supplier: {{ $supplierName }}</h4>
    @if($startDate && $endDate)
        <p class="text-center">From <strong>{{ $startDate }}</strong> to <strong>{{ $endDate }}</strong></p>
    @endif

    <table class="table table-bordered mt-4">
        <thead class="table-primary">
            <tr>
                <th>Date</th>
                <th>Bill No</th>
                <th>Description</th>
                <th class="text-end">Paid</th>
                <th class="text-end">Total to be paid</th>
                <th class="text-end">Balance</th>
            </tr>
        </thead>
        <tbody>
           

            @php
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp

            {{-- Ledger entries --}}
           @foreach($ledger as $entry)
    @php
        $totalDebit += (float)$entry['debit'];
        $totalCredit += (float)$entry['credit'];
    @endphp
    <tr>
        <td>{{ $entry['date'] }}</td>
        <td>{{ $entry['bill_no'] }}</td>
        <td>{{ $entry['description'] }}</td>
        <td class="text-end">{{ number_format((float) $entry['debit'], 2) }}</td>
        <td class="text-end">{{ number_format((float) $entry['credit'], 2) }}</td>
        <td class="text-end">{{ number_format((float) $entry['balance'], 2) }}</td>
    </tr>
@endforeach

        </tbody>
        <tfoot>
            <tr class="fw-bold">
                <td colspan="3" class="text-end">Total</td>
                <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection

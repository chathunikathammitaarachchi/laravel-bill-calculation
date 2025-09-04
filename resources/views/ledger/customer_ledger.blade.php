@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 900px; margin: 50px auto; background: #fff; padding: 20px; border-radius: 10px;">
    <h4 class="text-center mb-3">Ledger for Customer: {{ $customerName }}</h4>
    @if($startDate && $endDate)
        <p class="text-center">From <strong>{{ $startDate }}</strong> to <strong>{{ $endDate }}</strong></p>
    @endif

    <table class="table table-bordered mt-4">
        <thead class="table-primary">
            <tr>
                <th>Date</th>
                <th>Invoice No</th>
                <th>Description</th>
                <th class="text-end">paid</th>
                <th class="text-end">Total to be paid</th>
                <th class="text-end">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebit = 0;
                $totalCredit = 0;
                $balance = $openingBalance;
            @endphp

            <tr>
                <td>{{ $startDate ? \Carbon\Carbon::parse($startDate)->subDay()->toDateString() : '-' }}</td>
                <td>-</td>
                <td>Opening Balance</td>
                <td class="text-end">-</td>
                <td class="text-end">-</td>
                <td class="text-end">{{ number_format($openingBalance, 2) }}</td>
            </tr>

            @foreach($ledger as $entry)
                @php
                    $debit = is_numeric($entry['debit']) ? $entry['debit'] : null;
                    $credit = is_numeric($entry['credit']) ? $entry['credit'] : null;
                @endphp
                <tr>
                    <td>{{ $entry['date'] }}</td>
                    <td>{{ $entry['invoice_no'] }}</td>
                    <td>{{ $entry['description'] }}</td>
                    <td class="text-end">{{ number_format($debit, 2) }}</td>
                    <td class="text-end">{{ number_format($credit, 2) }}</td>
                    <td class="text-end">{{ number_format($balance, 2) }}</td>
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

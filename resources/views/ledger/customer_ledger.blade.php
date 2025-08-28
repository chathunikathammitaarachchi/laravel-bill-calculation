@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 900px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); font-family: Arial, sans-serif;">

  

<h4 style="text-align: center; color: #222; margin-bottom: 25px;">Ledger for {{ $customerName }}</h4>
    @if($startDate && $endDate)
        <p style="text-align: center; font-size: 16px; color: #555;">
            From <strong>{{ $startDate }}</strong> to <strong>{{ $endDate }}</strong>
        </p>
    @endif

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #007bff; color: white; text-align: left;">
                <th style="padding: 12px; border: 1px solid #ddd;">Date</th>
                <th style="padding: 12px; border: 1px solid #ddd;">Bill No</th>
                <th style="padding: 12px; border: 1px solid #ddd;">Description</th>
                <th style="padding: 12px; border: 1px solid #ddd; text-align: right;">Debit</th>
                <th style="padding: 12px; border: 1px solid #ddd; text-align: right;">Credit</th>
                <th style="padding: 12px; border: 1px solid #ddd; text-align: right;">Balance</th>
            </tr>
        </thead>
     <tbody>
    @php
        $totalDebit = 0;
        $totalCredit = 0;
        $totalBalance = 0;
    @endphp

@foreach ($ledger as $entry)
        @php
            $debit = is_numeric($entry['debit']) ? $entry['debit'] : 0;
            $credit = is_numeric($entry['credit']) ? $entry['credit'] : 0;

            $totalDebit += $debit;
            $totalCredit += $credit;
            $totalBalance += $credit - $debit;
        @endphp

        <tr style="background-color: {{ $loop->even ? '#f9f9f9' : 'white' }};">
            <td style="padding: 10px; border: 1px solid #ddd;">{{ $entry['date'] ?? '-' }}</td>
            <td style="padding: 10px; border: 1px solid #ddd;">{{ $entry['bill_no'] ?: '-' }}</td>
            <td style="padding: 10px; border: 1px solid #ddd;">{{ $entry['description'] }}</td>
            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">
                {{ $entry['debit'] !== '' ? number_format($entry['debit'], 2) : '-' }}
            </td>
            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">
                {{ $entry['credit'] !== '' ? number_format($entry['credit'], 2) : '-' }}
            </td>
            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">
                {{ number_format($totalBalance, 2) }}
            </td>
        </tr>
    @endforeach
</tbody>

        <tfoot>
            <tr style="font-weight: bold; background-color: #e9ecef;">
                <td colspan="3" style="padding: 12px; border: 1px solid #ddd; text-align: right;">Totals</td>
                <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">{{ number_format($totalDebit, 2) }}</td>
                <td style="padding: 12px; border: 1px solid #ddd; text-align: right;">{{ number_format($totalCredit, 2) }}</td>
                <td style="padding: 12px; border: 1px solid #ddd;"></td>
            </tr>
        </tfoot>
    </table>
    <hr/>
      <form method="GET" action="{{ route('ledger.customer.pdf') }}" target="_blank">
    <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
    <input type="hidden" name="start_date" value="{{ $startDate }}">
    <input type="hidden" name="end_date" value="{{ $endDate }}">
    <button type="submit" class="btn btn-sm btn-danger">Download PDF</button>
</form>
</div>
@endsection

<!DOCTYPE html>
<html>
<head>
    <title>GRN Summary Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #444; padding: 8px; text-align: center; }
        thead { background-color: #f0f0f0; }
        h2 { text-align: center; }
        .totals-row { font-weight: bold; background-color: #e0e0e0; }
        .date-range { margin-top: 5px; font-size: 14px; text-align: center; }
    </style>
</head>
<body>
    <h2>GRN Summary Report</h2>
    @if($from || $to)
        <p class="date-range">
            Date Range: 
            {{ $from ? \Carbon\Carbon::parse($from)->format('Y-m-d') : 'Beginning' }} 
            to 
            {{ $to ? \Carbon\Carbon::parse($to)->format('Y-m-d') : 'Now' }}
        </p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>GRN Count</th>
                <th>Total Price (Rs)</th>
                <th>Total Discount (Rs)</th>
                <th>Total Amount (Rs)</th>
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

            <tr class="totals-row">
                <td>Total</td>
                <td>{{ $totals['grn_count'] }}</td>
                <td>{{ number_format($totals['total_price'], 2) }}</td>
                <td>{{ number_format($totals['total_discount'], 2) }}</td>
                <td>{{ number_format($totals['total_issued'], 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>

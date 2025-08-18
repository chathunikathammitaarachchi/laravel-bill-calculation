<!DOCTYPE html>
<html>
<head>
    <title>Daily Stock Summary</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <h2>Daily Stock Summary</h2>

    @if($startDate && $endDate)
        <p>From: <strong>{{ $startDate }}</strong> To: <strong>{{ $endDate }}</strong></p>
    @endif

    @php
        $totalIn = 0;
        $totalOut = 0;
    @endphp

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Stock In</th>
                <th>Stock Out</th>
                <th>Net Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailySummary as $day)
                @php
                    $date = \Carbon\Carbon::parse($day->transaction_date)->format('Y-m-d');
                    $in = $day->stock_in ?? 0;
                    $out = $day->stock_out ?? 0;
                    $net = $in - $out;
                    $totalIn += $in;
                    $totalOut += $out;
                @endphp
                <tr>
                    <td>{{ $date }}</td>
                    <td>{{ $day->item_code }}</td>
                    <td>{{ $day->item_name }}</td>
                    <td>{{ $in }}</td>
                    <td>{{ $out }}</td>
                    <td>{{ $net }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="3"><strong>Total</strong></td>
                <td><strong>{{ $totalIn }}</strong></td>
                <td><strong>{{ $totalOut }}</strong></td>
                <td><strong>{{ $totalIn - $totalOut }}</strong></td>
            </tr>
        </tbody>
    </table>

</body>
</html>

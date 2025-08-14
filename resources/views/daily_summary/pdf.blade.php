<!DOCTYPE html>
<html>
<head>
    <title>Daily Stock Summary PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        table, th, td {
            border: 1px solid black;
            padding: 6px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h2>Daily Stock Summary</h2>
    @if($startDate && $endDate)
        <p>From <strong>{{ $startDate }}</strong> to <strong>{{ $endDate }}</strong></p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Source</th>
                <th>Stock In</th>
                <th>Stock Out</th>
                <th>Net Stock</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalIn = 0;
                $totalOut = 0;
            @endphp

            @foreach($dailySummary as $day)
                @php
                    $net = $day->stock_in - $day->stock_out;
                    $totalIn += $day->stock_in;
                    $totalOut += $day->stock_out;
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->transaction_date)->format('Y-m-d') }}</td>
                    <td>{{ $day->source }}</td>
                    <td>{{ $day->stock_in }}</td>
                    <td>{{ $day->stock_out }}</td>
                    <td>{{ $net }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="2" style="text-align: right;"><strong>Total</strong></td>
                <td><strong>{{ $totalIn }}</strong></td>
                <td><strong>{{ $totalOut }}</strong></td>
                <td><strong>{{ $totalIn - $totalOut }}</strong></td>
            </tr>
        </tbody>
    </table>

</body>
</html>

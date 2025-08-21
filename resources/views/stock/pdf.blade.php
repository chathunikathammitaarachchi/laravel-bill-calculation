<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Ledger PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 5px;
            margin-bottom: 30px;
        }

        h4 {
            margin-top: 40px;
            color: #2980b9;
        }

        p {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        th {
            background-color: #ecf0f1;
            border: 1px solid #bdc3c7;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }

        td {
            border: 1px solid #bdc3c7;
            padding: 8px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-green {
            color: #27ae60;
            font-weight: bold;
        }

        .text-red {
            color: #c0392b;
            font-weight: bold;
        }

        .summary-row {
            background-color: #f6f6f6;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2> Stock Transaction Ledger</h2>

    @if($start && $end)
        <p><strong>From:</strong> {{ $start }} &nbsp;&nbsp; <strong>To:</strong> {{ $end }}</p>
    @endif

    @foreach($groupedHistory as $code => $transactions)
        <h4>Item Code: {{ $code }} | Item Name: {{ $transactions[0]['name'] }}</h4>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th>Stock In Hand</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row['date'])->format('Y-m-d') }}</td>
                        <td>{{ $row['type'] }}</td>
                        <td class="{{ $row['qty'] >= 0 ? 'text-green' : 'text-red' }}">
                            {{ $row['qty'] >= 0 ? '+' : '' }}{{ $row['qty'] }}
                        </td>
                        <td>{{ $row['balance'] }}</td>
                    </tr>
                @endforeach

                <tr class="summary-row">
                    <td colspan="2">Total IN</td>
                    <td colspan="2" class="text-green">{{ $inTotal[$code] ?? 0 }}</td>
                </tr>
                <tr class="summary-row">
                    <td colspan="2">Total OUT</td>
                    <td colspan="2" class="text-red">{{ $outTotal[$code] ?? 0 }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

</body>
</html>

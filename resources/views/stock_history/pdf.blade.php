<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Ledger PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #f0f0f0; }
        h4 { margin-top: 30px; }
    </style>
</head>
<body>
    <h2>Stock Transaction Ledger</h2>
    @if($start && $end)
        <p>From: {{ $start }} To: {{ $end }}</p>
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
                        <td>{{ $row['qty'] >= 0 ? '+' : '' }}{{ $row['qty'] }}</td>
                        <td>{{ $row['balance'] }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2"><strong>Total IN</strong></td>
                    <td colspan="2">{{ $inTotal[$code] ?? 0 }}</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Total OUT</strong></td>
                    <td colspan="2">{{ $outTotal[$code] ?? 0 }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach
</body>
</html>

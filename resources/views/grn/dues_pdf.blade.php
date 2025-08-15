<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Dues Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Customer Dues Report</h2>

    <table>
        <thead>
            <tr>
                <th>Bill No</th>
                <th>Customer</th>
                <th>Due</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dues as $due)
                <tr>
                    <td>{{ $due->bill_no }}</td>
                    <td>{{ $due->customer_name }}</td>
                    <td>{{ number_format($due->tobe_price, 2) }}</td>
                    <td>{{ number_format($due->customer_pay, 2) }}</td>
                    <td>{{ number_format($due->balance, 2) }}</td>
                    <td>{{ $due->grn_date }}</td>
                    <td>{{ $due->balance > 0 ? 'Pending' : 'Settled' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

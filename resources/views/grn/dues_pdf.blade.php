<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Dues Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff;
            color: #333;
            padding: 40px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 20px;
        }

        thead {
            background-color: #3498db;
            color: #fff;
        }

        th, td {
            padding: 10px 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #eef5fb;
        }

        .status-pending {
            color: #d35400;
            font-weight: bold;
        }

        .status-settled {
            color: #27ae60;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            font-size: 12px;
            text-align: center;
            color: #999;
        }
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
                    <td>
                        @if($due->balance > 0)
                            <span class="status-pending">Pending</span>
                        @else
                            <span class="status-settled">Settled</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('Y-m-d H:i') }}
    </div>

</body>
</html>

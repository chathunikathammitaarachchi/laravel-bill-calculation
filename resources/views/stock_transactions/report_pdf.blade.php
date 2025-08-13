<!DOCTYPE html>
<html>
<head>
    <title>Stock Transactions Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 5px;
            font-size: 14px;
            border-bottom: 1px solid #999;
            padding-bottom: 3px;
        }

        .info {
            margin-bottom: 10px;
        }

        .summary {
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            padding: 10px;
            margin-top: 10px;
            width: 300px;
        }

        .summary ul {
            margin: 0;
            padding-left: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #e0e0e0;
        }

        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <h2>Stock Transactions Report</h2>

    <div class="info">
        @if($startDate && $endDate)
            <p><strong>Period:</strong> {{ $startDate }} to {{ $endDate }}</p>
        @endif

        @if($type)
            <p><strong>Transaction Type:</strong> {{ $type }}</p>
        @else
            <p><strong>Transaction Type:</strong> All</p>
        @endif
    </div>

    <div class="summary">
        <strong>Summary:</strong>
        <ul>
            <li>Stock IN: {{ $summary['IN'] ?? 0 }}</li>
            <li>Stock OUT: {{ $summary['OUT'] ?? 0 }}</li>
        </ul>
    </div>

    <div class="section-title">Detailed Transactions</div>

    <table>
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Price</th>
                <th>Reference No</th>
                <th>Source</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->item_code }}</td>
                    <td>{{ $transaction->item_name }}</td>
                    <td>{{ $transaction->transaction_type }}</td>
                    <td>{{ $transaction->quantity }}</td>
                    <td>{{ number_format($transaction->rate, 2) }}</td>
                    <td>{{ number_format($transaction->price, 2) }}</td>
                    <td>{{ $transaction->reference_no }}</td>
                    <td>{{ $transaction->source }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No transactions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

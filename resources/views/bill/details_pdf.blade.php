<!DOCTYPE html>
<html>
<head>
    <title>GRN Details Report - {{ $rangeLabel }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #444;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <h2>GRN Details Report</h2>
    <p><strong>Date Range:</strong> {{ $rangeLabel }}</p>

    @foreach ($groupedGrns as $date => $items)
        <h4>Date: {{ $date }}</h4>
        <table>
            <thead>
                <tr>
                    <th>GRN ID</th>
                    <th>Total Price</th>
                    <th>Total Discount</th>
                    <th>Issued Price</th>
                    <th>Supplier Pay</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ number_format($item->total_price, 2) }}</td>
                        <td>{{ number_format($item->total_discount, 2) }}</td>
                        <td>{{ number_format($item->tobe_price, 2) }}</td>
                        <td>{{ number_format($item->supplier_pay, 2) }}</td>
                        <td>{{ number_format($item->balance, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <h3>Overall Totals</h3>
    <table>
        <tr><th>Total Price</th><td>{{ number_format($totals['total_price'], 2) }}</td></tr>
        <tr><th>Total Discount</th><td>{{ number_format($totals['total_discount'], 2) }}</td></tr>
        <tr><th>Total Issued</th><td>{{ number_format($totals['total_issued'], 2) }}</td></tr>
        <tr><th>Total Paid</th><td>{{ number_format($totals['total_paid'], 2) }}</td></tr>
        <tr><th>Total Balance</th><td>{{ number_format($totals['total_balance'], 2) }}</td></tr>
    </table>
</body>
</html>

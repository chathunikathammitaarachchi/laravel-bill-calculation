<!DOCTYPE html>
<html>
<head>
    <title>GRN Details PDF</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
        }
        thead {
            background-color: #f2f2f2;
        }
        h3 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h3>GRN Details for {{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</h3>

    @if($grns->isEmpty())
        <p>No GRNs found for this date.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>GRN No</th>
                    <th>Supplier</th>
                    <th>Total Price</th>
                    <th>Total Discount</th>
                    <th>Total Issued</th>
                    <th>Paid</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grns as $grn)
                    <tr>
                        <td>{{ $grn->grn_no }}</td>
                        <td>{{ $grn->supplier_name }}</td>
                        <td>Rs {{ number_format($grn->total_price, 2) }}</td>
                        <td>Rs {{ number_format($grn->total_discount, 2) }}</td>
                        <td>Rs {{ number_format($grn->tobe_price, 2) }}</td>
                        <td>Rs {{ number_format($grn->supplier_pay, 2) }}</td>
                        <td>Rs {{ number_format($grn->balance, 2) }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold;">
                    <td colspan="2">Total</td>
                    <td>Rs {{ number_format($totals['total_price'], 2) }}</td>
                    <td>Rs {{ number_format($totals['total_discount'], 2) }}</td>
                    <td>Rs {{ number_format($totals['total_issued'], 2) }}</td>
                    <td>Rs {{ number_format($totals['total_paid'], 2) }}</td>
                    <td>Rs {{ number_format($totals['total_balance'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GRN Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 30px;
            color: #333;
        }
        h3, h4 {
            margin-bottom: 5px;
            color: #2c3e50;
        }
        p {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .summary-table {
            width: 50%;
            margin-top: 10px;
            margin-bottom: 30px;
            border: 1px solid #999;
        }
        .summary-table th {
            background-color: #dfe6e9;
            text-align: center;
            font-size: 13px;
        }
        .summary-table td {
            padding: 6px 10px;
        }
        .section-divider {
            border-top: 2px solid #34495e;
            margin: 30px 0;
        }
    </style>
</head>
<body>

    <h3>🧾 GRN Report</h3>

    @if($fromDate || $toDate)
        <p><strong>From:</strong> {{ $fromDate ?? '---' }} &nbsp;&nbsp; <strong>To:</strong> {{ $toDate ?? '---' }}</p>
    @endif

    @foreach($groupedGrns as $date => $grnsForDate)
        <h4>📅 Date: {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</h4>

        <table>
            <thead>
                <tr>
                    <th>GRN No</th>
                    <th>Supplier</th>
                    <th>Total</th>
                    <th>Discount</th>
                    <th>To be Paid</th>
                    <th>Paid</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grnsForDate as $grn)

                   <tr>
    <td>{{ $grn->grn_no }}</td>
    <td>{{ $grn->supplier_name }}</td>
    <td>{{ number_format($grn->total_price, 2) }}</td>
    <td>{{ number_format($grn->total_discount, 2) }}</td>
    <td>{{ number_format($grn->tobe_price, 2) }}</td>
    <td>{{ number_format($grn->supplier_pay, 2) }}</td>
    <td>{{ number_format($grn->balance, 2) }}</td>
</tr>

                @endforeach
            </tbody>
        </table>

        {{-- 🧮 Daily Summary --}}
        <table class="summary-table">
            <thead>
                <tr>
                    <th colspan="2">📊 Summary for {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Total Sales</strong></td>
                    <td>{{ number_format($dailySummaries[$date]['totalSales'], 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Total Discount</strong></td>
                    <td>{{ number_format($dailySummaries[$date]['totalDiscount'], 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Total Paid Balance</strong></td>
                    <td>{{ number_format($dailySummaries[$date]['totalIssued'], 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-divider"></div>
    @endforeach

    {{-- 🧾 Grand Summary at the bottom --}}
    <h4>📌 Overall Summary</h4>
    <table class="summary-table">
        <thead>
            <tr>
                <th colspan="2">Grand Totals</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Total Sales</strong></td>
                <td>{{ number_format($totalSales, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Discount</strong></td>
                <td>{{ number_format($totalDiscount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total To Be Paid</strong></td>
                <td>{{ number_format($totalIssued, 2) }}</td>
            </tr>
        </tbody>
    </table>

</body>
</html>

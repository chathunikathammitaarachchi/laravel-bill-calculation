<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Supplier Ledger - {{ $supplierName }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2, h4 {
            text-align: center;
            margin-bottom: 5px;
        }
        p {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table, th, td {
            border: 1px solid #999;
        }
        th, td {
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f2f2f2;
        }
        th.date, td.date,
        th.bill_no, td.bill_no,
        th.description, td.description {
            text-align: left;
        }
        tfoot td {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Supplier Ledger</h2>
    <h4>{{ $supplierName }}</h4>

    @if($startDate && $endDate)
        <p>From <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong></p>
    @elseif($startDate)
        <p>From <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong></p>
    @elseif($endDate)
        <p>Up to <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong></p>
    @endif

    <table>
        <thead>
            <tr>
                <th class="date">Date</th>
                <th class="bill_no">GRN No</th>
                <th class="description">Description</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebit = 0;
                $totalCredit = 0;
            @endphp

            @foreach ($ledger as $entry)
                @php
                    $debit = is_numeric($entry['debit']) ? $entry['debit'] : null;
                    $credit = is_numeric($entry['credit']) ? $entry['credit'] : null;
                @endphp
                <tr>
                    <td class="date">{{ $entry['date'] ?? '-' }}</td>
                    <td class="bill_no">{{ $entry['bill_no'] ?? '-' }}</td>
                    <td class="description">{{ $entry['description'] }}</td>
                    <td>{{ $debit !== null ? number_format($debit, 2) : '-' }}</td>
                    <td>{{ $credit !== null ? number_format($credit, 2) : '-' }}</td>
                    <td>{{ number_format($entry['balance'], 2) }}</td>
                </tr>

                @php
                    $totalDebit += $debit ?? 0;
                    $totalCredit += $credit ?? 0;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;">Total</td>
                <td>{{ number_format($totalDebit, 2) }}</td>
                <td>{{ number_format($totalCredit, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <p style="text-align: center; font-style: italic;">Generated on {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
</body>
</html>

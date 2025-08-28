<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Ledger</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h2, p {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h2>Customer Ledger</h2>
    <p><strong>{{ $customerName }}</strong></p>

    @if($startDate && $endDate)
        <p>From {{ $startDate }} to {{ $endDate }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Bill No</th>
                <th>Description</th>
                <th class="right">Debit</th>
                <th class="right">Credit</th>
                <th class="right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebit = 0;
                $totalCredit = 0;
                $totalBalance = 0;
            @endphp
            @foreach ($ledger as $entry)
                @php
                    $debit = is_numeric($entry['debit']) ? $entry['debit'] : 0;
                    $credit = is_numeric($entry['credit']) ? $entry['credit'] : 0;
                    $totalDebit += $debit;
                    $totalCredit += $credit;
                    $totalBalance += $credit - $debit;
                @endphp
                <tr>
                    <td>{{ $entry['date'] ?? '-' }}</td>
                    <td>{{ $entry['bill_no'] ?: '-' }}</td>
                    <td>{{ $entry['description'] }}</td>
                    <td class="right">{{ $entry['debit'] !== '' ? number_format($entry['debit'], 2) : '-' }}</td>
                    <td class="right">{{ $entry['credit'] !== '' ? number_format($entry['credit'], 2) : '-' }}</td>
                    <td class="right">{{ number_format($totalBalance, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="right">Totals</th>
                <th class="right">{{ number_format($totalDebit, 2) }}</th>
                <th class="right">{{ number_format($totalCredit, 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>

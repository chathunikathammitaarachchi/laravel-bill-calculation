<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Bill Report PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 20px; color: #333; }
        h3, h4 { color: #2c3e50; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-right { text-align: right; }
        .section-divider { border-top: 2px solid #34495e; margin: 30px 0; }
        .summary-table { width: 50%; border: 1px solid #999; margin-bottom: 20px; }
        .summary-table th { background: #dfe6e9; text-align: center; font-size: 13px; }
    </style>
</head>
<body>

    <h3>Bill Report</h3>

    @if($fromDate || $toDate)
        <p><strong>From:</strong> {{ $fromDate ?? '---' }} &nbsp;&nbsp; <strong>To:</strong> {{ $toDate ?? '---' }}</p>
    @endif

    @if(isset($customerName, $fromDate))
        <p><strong>Opening Balance (before {{ $fromDate }}):</strong> Rs. {{ number_format($openingBalance, 2) }}</p>
    @endif

    @foreach($groupedGrns as $date => $grnsForDate)
        <h4>Date: {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</h4>

        <table>
            <thead>
                <tr>
                    <th>Bill No</th>
                    <th>Customer</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">To be Paid</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grnsForDate as $grn)
                    <tr>
                        <td>{{ $grn->bill_no }}</td>
                        <td>{{ $grn->customer_name }}</td>
                        <td class="text-right">{{ number_format($grn->total_price, 2) }}</td>
                        <td class="text-right">{{ number_format($grn->total_discount, 2) }}</td>
                        <td class="text-right">{{ number_format($grn->tobe_price, 2) }}</td>
                        <td class="text-right">{{ number_format($grn->customer_pay, 2) }}</td>
                        <td class="text-right">{{ number_format($grn->balance, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $totalSales = $grnsForDate->sum('total_price');
            $totalDiscount = $grnsForDate->sum('total_discount');
            $totalToBePaid = $grnsForDate->sum('tobe_price');
        @endphp

        <table class="summary-table">
            <thead>
                <tr>
                    <th colspan="2">Summary for {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Total Sales</td><td class="text-right">{{ number_format($totalSales, 2) }}</td></tr>
                <tr><td>Total Discount</td><td class="text-right">{{ number_format($totalDiscount, 2) }}</td></tr>
                <tr><td>Total To Be Paid</td><td class="text-right">{{ number_format($totalToBePaid, 2) }}</td></tr>
            </tbody>
        </table>

        <div class="section-divider"></div>
    @endforeach

   
<h4>Overall Summary</h4>
<table class="summary-table">
    <tbody>
        <tr>
            <td>Total Sales</td>
            <td class="text-right">{{ number_format($totalTotal, 2) }}</td>
        </tr>
        <tr>
            <td>Total Discount</td>
            <td class="text-right">{{ number_format($totalDiscount, 2) }}</td>
        </tr>
        <tr>
            <td>Total To Be Paid</td>
            <td class="text-right">{{ number_format($totalToBePaid, 2) }}</td>
        </tr>
        <tr>
            <td>Total Opening balance</td>
            <td class="text-right">{{ number_format($openingBalance, 2) }}</td>
        </tr>

        {{-- 🔄 Grand Total including Opening Balance --}}
        @if(isset($customerName, $fromDate))
            <tr style="background-color: #fff3cd; font-weight: bold;">
                <td>Grand To Be Paid (Opening + Range)</td>
                <td class="text-right">{{ number_format($openingBalance + $totalToBePaid, 2) }}</td>
            </tr>
        @endif
    </tbody>
</table>

</body>
</html>

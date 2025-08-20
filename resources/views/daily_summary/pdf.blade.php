<!DOCTYPE html>
<html>
<head>
    <title>Daily Stock Summary PDF</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 5px; text-align: center; }
        .text-success { color: green; }
        .text-danger { color: red; }
        .text-end { text-align: right; }
    </style>
</head>
<body>

<h2 style="text-align:center;">Daily Stock Summary</h2>
<p><strong>From:</strong> {{ $startDate ?? 'N/A' }} <strong>To:</strong> {{ $endDate ?? 'N/A' }}</p>

@php
    $openingIn = 0;
    $openingOut = 0;
    $totalIn = 0;
    $totalOut = 0;
@endphp

@if(($openingBalances && count($openingBalances)) || ($dailySummary && count($dailySummary)))
    <h4>Stock Summary (Opening + Transactions)</h4>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th class="text-success">Stock IN</th>
                <th class="text-danger">Stock OUT</th>
                <th>Net Stock</th>
            </tr>
        </thead>
        <tbody>

            {{-- Opening Balance Rows --}}
            @if($openingBalances && count($openingBalances))
                @foreach($openingBalances as $opening)
                    @php
                        $in = $opening->opening_in ?? 0;
                        $out = $opening->opening_out ?? 0;
                        $net = $in - $out;

                        $openingIn += $in;
                        $openingOut += $out;
                    @endphp
                    <tr>
                        <td><em>Before {{ $startDate }}</em></td>
                        <td>{{ $opening->item_code }}</td>
                        <td>{{ $opening->item_name }}</td>
                        <td class="text-success">+{{ $in }}</td>
                        <td class="text-danger">-{{ $out }}</td>
                        <td>{{ $net }}</td>
                    </tr>
                @endforeach
            @endif

            {{-- Daily Stock Transactions --}}
            @if($dailySummary && count($dailySummary))
                @foreach($dailySummary as $day)
                    @php
                        $date = \Carbon\Carbon::parse($day->transaction_date)->format('Y-m-d');
                        $in = $day->stock_in ?? 0;
                        $out = $day->stock_out ?? 0;
                        $net = $in - $out;

                        $totalIn += $in;
                        $totalOut += $out;
                    @endphp
                    <tr>
                        <td>{{ $date }}</td>
                        <td>{{ $day->item_code }}</td>
                        <td>{{ $day->item_name }}</td>
                        <td class="text-success">+{{ $in }}</td>
                        <td class="text-danger">-{{ $out }}</td>
                        <td>{{ $net }}</td>
                    </tr>
                @endforeach
            @endif

            {{-- Total Row --}}
            <tr style="font-weight: bold;">
                <td colspan="3" class="text-end">Total (Opening + Transactions)</td>
                <td class="text-success">+{{ $openingIn + $totalIn }}</td>
                <td class="text-danger">-{{ $openingOut + $totalOut }}</td>
                <td>{{ ($openingIn + $totalIn) - ($openingOut + $totalOut) }}</td>
            </tr>
        </tbody>
    </table>
@endif

{{-- Optional Charts --}}
@if(!empty($pieChartImage) || !empty($barChartImage))
    <h4>Visual Summary</h4>
    @if($pieChartImage)
        <p>Pie Chart:</p>
        <img src="{{ $pieChartImage }}" style="width:300px; height:auto;" />
    @endif
    @if($barChartImage)
        <p>Bar Chart:</p>
        <img src="{{ $barChartImage }}" style="width:300px; height:auto;" />
    @endif
@endif

</body>
</html>

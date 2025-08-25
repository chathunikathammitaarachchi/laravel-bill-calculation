<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width:100%; border-collapse:collapse; margin-bottom:20px; }
        th, td { border:1px solid #ccc; padding:8px; font-size:12px; }
        th { background:#eee; }
        h3 { margin-bottom:10px; }
        .chart { text-align:center; margin-bottom:20px; }
    </style>
</head>
<body>
    <h3>Item Summary Report</h3>
    <p><strong>Start Date:</strong> {{ $start_date }}</p>
    <p><strong>End Date:</strong> {{ $end_date }}</p>
    <p><strong>Search:</strong> {{ $search }}</p>

   

    <table>
        <thead>
            <tr>
                <th>Item Code</th><th>Item Name</th><th>Date</th>
                <th>Quantity Sold</th><th>Total Price (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailySummary as $row)
                <tr>
                    <td>{{ $row['item_code'] }}</td>
                    <td>{{ $row['item_name'] }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ number_format($row['quantity']) }}</td>
                    <td>Rs. {{ number_format($row['total_price'], 2) }}</td>
                </tr>
            @endforeach
            {{-- Totals row --}}
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Total</strong></td>
                <td><strong>{{ number_format(collect($dailySummary)->sum('quantity')) }}</strong></td>
                <td><strong>Rs. {{ number_format(collect($dailySummary)->sum('total_price'), 2) }}</strong></td>
            </tr>
        </tbody>
    </table>


     <div class="chart">
        <img src="{{ $barChartImage }}" width="400">
    </div>
    <div class="chart">
        <img src="{{ $lineChartImage }}" width="400">
    </div>
</body>
</html>

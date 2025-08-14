<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Item Summary Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #eee; }
        h2, h4 { margin-bottom: 0; }
        .text-end { text-align: right; }
    </style>
</head>
<body>
    <h2>🧾 Item Summary Report</h2>

    @if($request->filled('start_date') || $request->filled('end_date'))
        <p><strong>Date Range:</strong>
            {{ $request->start_date ?? 'N/A' }} to {{ $request->end_date ?? 'N/A' }}
        </p>
    @endif

    @if($request->filled('search'))
        <p><strong>Search:</strong> "{{ $request->search }}"</p>
    @endif

    <h4>Item-wise Summary</h4>
    <table>
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Item Name</th>
                <th class="text-end">Total Quantity</th>
                <th class="text-end">Total Sales (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($itemTotals as $code => $data)
                <tr>
                    <td>{{ $code }}</td>
                    <td>{{ $data['item_name'] }}</td>
                    <td class="text-end">{{ number_format($data['quantity']) }}</td>
                    <td class="text-end">Rs. {{ number_format($data['total_price'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Optional: You can add charts here as images if you want to generate charts in PDF --}}
    {{-- For simplicity, this example skips charts in PDF --}}

</body>
</html>

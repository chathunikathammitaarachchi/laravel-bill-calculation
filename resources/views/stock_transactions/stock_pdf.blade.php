<!DOCTYPE html>
<html>
<head>
    <title>Stock IN vs OUT Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #333; text-align: center; }
        h2 { text-align: center; margin-bottom: 0; }
        .period { text-align: center; margin-top: 0; }
    </style>
</head>
<body>

    <h2>Stock IN vs OUT Report</h2>
    <p class="period"><strong>Period:</strong> {{ $startDate }} to {{ $endDate }}</p>

    <table>
        <thead>
            <tr>
                <th>Transaction Type</th>
                <th>Total Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($labels as $index => $label)
                <tr>
                    <td>Stock {{ $label }}</td>
                    <td>{{ $values[$index] ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>

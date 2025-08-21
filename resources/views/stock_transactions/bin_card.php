{{-- resources/views/stock_transactions/bin_card.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bin Card</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #aaa;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #eee;
        }
        .error {
            color: red;
            margin-top: 15px;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
        }
        a.back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
        }
        a.back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Bin Card for Item: {{ $itemName }} ({{ $itemCode }})</h2>

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    @if(count($binCard) > 0)
        <table>
            <thead>
            <tr>
                <th>Date</th>
                <th>Reference No</th>
                <th>Source</th>
                <th>In Quantity</th>
                <th>Out Quantity</th>
                <th>Balance</th>
            </tr>
            </thead>
            <tbody>
            @foreach($binCard as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['reference_no'] }}</td>
                    <td>{{ $row['source'] }}</td>
                    <td>{{ $row['in'] ?? '-' }}</td>
                    <td>{{ $row['out'] ?? '-' }}</td>
                    <td>{{ $row['balance'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>No transactions found for this item.</p>
    @endif

    <a href="{{ url()->previous() }}" class="back-link">Back</a>
</div>
</body>
</html>

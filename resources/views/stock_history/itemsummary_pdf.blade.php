<!DOCTYPE html>
<html>
<head>
    <title>Daily Item Summary PDF</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }

        .summary-box {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #000;
        }

        .highlight {
            font-weight: bold;
            color: #000;
        }
    </style>
</head>
<body>

    <h2>Daily Item Summary</h2>

    {{-- Summary Section --}}
    @if(isset($maxItem))
        <div class="summary-box">
            <span class="highlight">Highest OUT:</span>
            {{ $minItem['item_code'] }} - {{ $minItem['item_name'] }} ({{ $minItem['quantity_total'] }})
        </div>
    @endif

    @if(isset($minItem))
        <div class="summary-box">
            <span class="highlight">Lowest OUT:</span>
            {{ $maxItem['item_code'] }} - {{ $maxItem['item_name'] }} ({{ $maxItem['quantity_total'] }})
        </div>
    @endif

    {{-- Data Table --}}
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Quantity OUT (Total)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedByDate as $date => $items)
                @php $itemCount = count($items); @endphp
                @foreach($items as $code => $data)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{ $itemCount }}">
                                {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}
                            </td>
                        @endif
                        <td>{{ $code }}</td>
                        <td>{{ $data['item_name'] }}</td>
                        <td>{{ $data['quantity_total'] }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Item Summary Report</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f4f8;
      margin: 30px;
      color: #2c3e50;
      font-size: 15px;
    }
    h2 {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 28px;
      font-weight: 700;
      color: #34495e;
      margin-bottom: 25px;
    }
    p {
      margin: 6px 0;
      font-size: 14px;
      color: #555;
    }
    strong {
      color: #2c3e50;
    }
    h4 {
      font-size: 20px;
      margin-top: 35px;
      margin-bottom: 12px;
      border-bottom: 3px solid #2980b9;
      padding-bottom: 5px;
      color: #2980b9;
      font-weight: 600;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      box-shadow: 0 4px 15px rgb(0 0 0 / 0.1);
      border-radius: 12px;
      overflow: hidden;
      background: white;
    }
    thead {
      background: linear-gradient(90deg, #2980b9, #3498db);
      color: white;
    }
    thead th {
      padding: 15px 20px;
      text-align: left;
      font-weight: 600;
      font-size: 15px;
    }
    tbody tr {
      border-bottom: 1px solid #eee;
      transition: background-color 0.3s ease;
    }
    tbody tr:hover {
      background-color: #ecf6fc;
      cursor: default;
    }
    tbody td {
      padding: 15px 20px;
      font-size: 14px;
      vertical-align: middle;
      color: #34495e;
    }
    tbody td:nth-child(3),
    tbody td:nth-child(4) {
      text-align: right;
      font-feature-settings: "tnum";
      font-variant-numeric: tabular-nums;
    }
    @media (max-width: 700px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }
      thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
      }
      tbody tr {
        margin-bottom: 20px;
        background: white;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        border-radius: 12px;
        padding: 15px 20px;
      }
      tbody td {
        padding-left: 50%;
        position: relative;
        text-align: left !important;
        font-size: 14px;
        border: none;
        border-bottom: 1px solid #eee;
      }
      tbody td:last-child {
        border-bottom: 0;
      }
      tbody td::before {
        position: absolute;
        top: 15px;
        left: 20px;
        width: 45%;
        font-weight: 700;
        color: #2980b9;
        white-space: nowrap;
      }
      tbody td:nth-of-type(1)::before { content: "Item Code"; }
      tbody td:nth-of-type(2)::before { content: "Item Name"; }
      tbody td:nth-of-type(3)::before { content: "Total Quantity"; }
      tbody td:nth-of-type(4)::before { content: "Total Sales (Rs.)"; }
    }
  </style>
</head>
<body>

  <h2> Item Summary Report</h2>

  @if($request->filled('start_date') || $request->filled('end_date'))
      <p><strong>Date Range:</strong> {{ $request->start_date ?? 'N/A' }} to {{ $request->end_date ?? 'N/A' }}</p>
  @endif

  @if($request->filled('search'))
      <p><strong>Search:</strong> "{{ $request->search }}"</p>
  @endif

  <h4> Item-wise Summary</h4>

  <table>
      <thead>
          <tr>
              <th>Item Code</th>
              <th>Item Name</th>
              <th>Total Quantity</th>
              <th>Total Sales (Rs.)</th>
          </tr>
      </thead>
      <tbody>
          @foreach($itemTotals as $code => $data)
              <tr>
                  <td>{{ $code }}</td>
                  <td>{{ $data['item_name'] }}</td>
                  <td>{{ number_format($data['quantity']) }}</td>
                  <td>Rs. {{ number_format($data['total_price'], 2) }}</td>
              </tr>
          @endforeach
      </tbody>
  </table>

</body>
</html>

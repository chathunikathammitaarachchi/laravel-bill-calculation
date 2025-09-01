@extends('layouts.app')

@section('content')
<style>
/* --- Styling (cleaned & merged) --- */
.container {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #2c3e50;
    background: #f9fafb;
    padding-bottom: 3rem;
}
h2 {
    font-weight: 800;
    color: #34495e;
    margin-bottom: 1.5rem;
    letter-spacing: 0.05em;
    text-shadow: 1px 1px 1px #ecf0f1;
}
form label {
    font-weight: 600;
    color: #34495e;
    margin-bottom: 0.4rem;
}
form input.form-control {
    border: 1.5px solid #bdc3c7;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
}
form input.form-control:focus {
    border-color: #2980b9;
    box-shadow: 0 0 6px rgba(41, 128, 185, 0.5);
    outline: none;
}
.btn-primary {
    background: linear-gradient(45deg, #2980b9, #3498db);
    border: none;
    font-weight: 700;
    border-radius: 0.5rem;
    box-shadow: 0 5px 15px rgba(41, 128, 185, 0.4);
    transition: background 0.4s ease;
}
.btn-primary:hover, .btn-primary:focus {
    background: linear-gradient(45deg, #3498db, #2980b9);
    box-shadow: 0 7px 20px rgba(41, 128, 185, 0.7);
}
.btn-secondary {
    background: #7f8c8d;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: background 0.3s ease;
    color: white;
}
.btn-secondary:hover, .btn-secondary:focus {
    background: #95a5a6;
    color: white;
}
.table-responsive {
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    background: white;
}
table.table-bordered {
    border-collapse: separate;
    border-spacing: 0 1rem;
    font-size: 0.95rem;
}
table.table-bordered thead th {
    background: #34495e;
    color: #ecf0f1;
    font-weight: 700;
    border: none !important;
    letter-spacing: 0.04em;
    padding: 1rem 1rem;
    position: sticky;
    top: 0;
    z-index: 11;
}
table.table-bordered tbody tr {
    background: #fdfdfd;
    box-shadow: 0 2px 12px rgba(52, 73, 94, 0.08);
    border-radius: 1rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
table.table-bordered tbody tr:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 20px rgba(41, 128, 185, 0.3);
    cursor: pointer;
}
table.table-bordered tbody td {
    vertical-align: middle;
    padding: 1rem 1rem;
    border: none !important;
}
.text-end {
    text-align: right !important;
}
.fw-bold.text-primary {
    color: #2980b9 !important;
    font-weight: 700;
    font-size: 1.1rem;
}
.fw-semibold.text-success {
    color: #27ae60 !important;
    font-weight: 600;
    font-size: 1rem;
}
td.text-center.text-muted.py-4 {
    font-style: italic;
    color: #95a5a6;
    font-size: 1.1rem;
}
.mt-4 {
    margin-top: 2rem !important;
}
h5.text-center {
    font-weight: 700;
    color: #2980b9;
    margin-bottom: 1rem;
    letter-spacing: 0.04em;
}
@media (max-width: 768px) {
    table.table-bordered thead th, table.table-bordered tbody td {
        padding: 0.75rem 0.8rem;
        font-size: 0.9rem;
    }
}
</style>

<div class="container py-4">
    <h2>🧾 Item Summary Table</h2>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('item_summaries.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="{{ old('start_date', request('start_date')) }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" value="{{ old('end_date', request('end_date')) }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="search">Search (Item Code or Item Name)</label>
            <input list="item-list" type="text" id="search" name="search" class="form-control" placeholder="Search by Item Code or Item Name" value="{{ old('search', request('search')) }}">
            <datalist id="item-list">
                @foreach($allItems as $item)
                    <option value="{{ $item->item_code }}">{{ $item->item_name }}</option>
                    <option value="{{ $item->item_name }}">{{ $item->item_code }}</option>
                @endforeach
            </datalist>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Apply</button>
            <a href="{{ route('item_summaries.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

     {{-- Filters Display --}}
    @if($filtersApplied)
        <table class="table-sm mb-4" style="width: 100%;">
            <thead>
                <tr><th>Start Date</th><th>End Date</th><th>Search Item</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ request('start_date') ?? 'N/A' }}</td>
                    <td>{{ request('end_date') ?? 'N/A' }}</td>
                    <td>{{ request('search') ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>

       <form action="{{ route('item_summaries.download_pdf') }}" method="POST" id="pdfForm" target="pdfFrame">
    @csrf
    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
    <input type="hidden" name="search" value="{{ request('search') }}">
    <input type="hidden" name="chartData" id="chartDataInput">
    <input type="hidden" name="lineChartData" id="lineChartDataInput">

    <button type="submit" class="btn btn-danger mb-4">
        ⬇️ Download & Print PDF
    </button>
</form>

<iframe id="pdfFrame" name="pdfFrame" style="display:none;" onload="printIframe()"></iframe>

<script>
    function printIframe() {
        const iframe = document.getElementById('pdfFrame');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }
    }
</script>

        @if($dailySummary->count())
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Item Code</th><th>Item Name</th><th>Date</th>
                        <th class="text-end">Qty</th><th class="text-end">Total (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailySummary as $row)
                        <tr>
                            <td>{{ $row['item_code'] }}</td>
                            <td>{{ $row['item_name'] }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td class="text-end">{{ number_format($row['quantity']) }}</td>
                            <td class="text-end">Rs. {{ number_format($row['total_price'], 2) }}</td>
                        </tr>
                    @endforeach
                    {{-- Totals row --}}
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total</td>
                        <td class="text-end fw-bold">{{ number_format($dailySummary->sum('quantity')) }}</td>
                        <td class="text-end fw-bold">Rs. {{ number_format($dailySummary->sum('total_price'), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Charts --}}
        <div class="row my-4">
            <div class="col-md-6"><canvas id="barChart"></canvas></div>
            <div class="col-md-6"><canvas id="lineChart"></canvas></div>
        </div>
        @else
            <p class="text-muted">🚫 No records found.</p>
        @endif

    @else
        {{-- Your main table view when no filters applied --}}
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dailySummary = @json($dailySummary);
    if (!dailySummary.length) return;

    const grouped = {};
    dailySummary.forEach(row => {
        if (!grouped[row.item_code]) grouped[row.item_code] = {dates: [], qty: []};
        grouped[row.item_code].dates.push(row.date);
        grouped[row.item_code].qty.push(row.quantity);
    });

    const code = Object.keys(grouped)[0];
    const rawLabels = grouped[code].dates;
    const labels = rawLabels.map(d => {
        const dt = new Date(d);
        return `${String(dt.getMonth()+1).padStart(2,'0')}.${String(dt.getDate()).padStart(2,'0')}`;
    });
    const dataPts = grouped[code].qty;

    const barCtx = document.getElementById('barChart').getContext('2d');
    const lineCtx = document.getElementById('lineChart').getContext('2d');

    const barChart = new Chart(barCtx, {
        type: 'bar',
        data: {labels, datasets:[{label:`Qty (${code})`, data:dataPts, backgroundColor:'rgba(54,162,235,0.7)', borderColor:'rgba(54,162,235,1)', borderWidth:1}]},
        options: { responsive:true, plugins: { title:{display:true, text:'Quantity Sold (MM.DD)'} }, scales:{ y:{beginAtZero:true}} }
    });

    const lineChart = new Chart(lineCtx, {
        type: 'line',
        data: {labels, datasets:[{label:`Qty (${code})`, data:dataPts, fill:false, borderColor:'rgba(255,99,132,1)', backgroundColor:'rgba(255,99,132,0.5)', tension:0.3, pointRadius:5}]},
        options:{ responsive:true, plugins:{ title:{display:true, text:'Quantity Sold (MM.DD)'} }, scales:{ y:{beginAtZero:true}} }
    });

    document.getElementById('pdfForm').addEventListener('submit', () => {
        document.getElementById('chartDataInput').value = barChart.toBase64Image();
        document.getElementById('lineChartDataInput').value = lineChart.toBase64Image();
    });
});
</script>
@endsection
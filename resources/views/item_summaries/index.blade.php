@extends('layouts.app')

@section('content')
<style>
/* Container & Fonts */
.container {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #2c3e50;
    background: #f9fafb;
    padding-bottom: 3rem;
}

/* Heading */
h2 {
    font-weight: 800;
    color: #34495e;
    margin-bottom: 1.5rem;
    letter-spacing: 0.05em;
    text-shadow: 1px 1px 1px #ecf0f1;
}

/* Form Styles */
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

/* Buttons */
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

/* Table Container */
.table-responsive {
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    background: white;
}

/* Table Styles */
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

/* Text Align */
.text-end {
    text-align: right !important;
}

/* Highlight Bill No and Totals */
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

/* Empty message style */
td.text-center.text-muted.py-4 {
    font-style: italic;
    color: #95a5a6;
    font-size: 1.1rem;
}

/* Pagination */
.mt-4 {
    margin-top: 2rem !important;
}

/* Item-wise summary table */
.table-sm.table-bordered {
    margin-top: 1.5rem;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(41, 128, 185, 0.15);
}

.table-sm.table-bordered thead th {
    background: #ecf0f1;
    color: #34495e;
    font-weight: 700;
    letter-spacing: 0.03em;
    padding: 0.75rem 1rem;
}

/* Chart Titles */
h5.text-center {
    font-weight: 700;
    color: #2980b9;
    margin-bottom: 1rem;
    letter-spacing: 0.04em;
}

/* Responsive */
@media (max-width: 768px) {
    table.table-bordered thead th, table.table-bordered tbody td {
        padding: 0.75rem 0.8rem;
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .table-responsive {
        overflow-x: auto;
    }
}
</style>

<div class="container py-4">
    <h2>🧾 Stock Hand Table</h2>

    {{-- Filter & Search Form --}}
    <form method="GET" action="{{ route('item_summaries.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="search">Search (Item Code or Item Name)</label>
            <input list="item-list" type="text" id="search" name="search" class="form-control" placeholder="Search by Item Code or Item Name" value="{{ request('search') }}">
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

    {{-- Data Table --}}
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark sticky-top">
                <tr>
                    <th style="width: 10%;">Bill No</th>
                    <th style="width: 10%;">Item Code</th>
                    <th style="width: 25%;">Item Name</th>
                    <th class="text-end" style="width: 10%;">Quantity</th>
                    <th class="text-end" style="width: 10%;">Rate</th>
                    <th class="text-end" style="width: 15%;">Total Price</th>
                    <th style="width: 15%;">Created At</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $prevBill = null;
                    $rowCounts = $summaries->groupBy('bill_no')->map->count();
                @endphp

                @forelse($summaries as $summary)
                    <tr>
                        @if ($summary->bill_no !== $prevBill)
                            <td rowspan="{{ $rowCounts[$summary->bill_no] }}" class="fw-bold text-primary">
                                {{ $summary->bill_no }}
                            </td>
                            @php $prevBill = $summary->bill_no; @endphp
                        @endif
                        <td>{{ $summary->item_code }}</td>
                        <td>{{ $summary->item_name }}</td>
                        <td class="text-end">{{ number_format($summary->quantity) }}</td>
                        <td class="text-end">Rs. {{ number_format($summary->rate, 2) }}</td>
                        <td class="text-end fw-semibold text-success">Rs. {{ number_format($summary->total_price, 2) }}</td>
                        <td>{{ $summary->grn_date }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            🚫 No item summary records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $summaries->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

    @php
        $filtersApplied = request('start_date') || request('end_date') || request('search');
    @endphp

    @if($filtersApplied && $itemTotals->count())
        {{-- Download PDF button --}}
        <div class="mt-4 mb-3">
            <a href="{{ route('item_summaries.download_pdf', request()->query()) }}" class="btn btn-danger">
                📄 Download PDF
            </a>
        </div>

        {{-- Item-wise summary table --}}
        <div class="mt-3">
            <h4>📊 Item-wise Summary for Selected Filters</h4>
            <table class="table table-sm table-bordered mt-3">
                <thead class="table-light">
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
        </div>

        {{-- Charts --}}
        <div class="row mt-5">
            <div class="col-md-6">
                <h5 class="text-center">📊 Item Quantity Distribution (Pie Chart)</h5>
                <canvas id="itemPieChart" style="max-height: 400px;"></canvas>
            </div>
            <div class="col-md-6">
                <h5 class="text-center">📈 Item-wise Quantity (Bar Chart)</h5>
                <canvas id="itemBarChart" style="max-height: 400px;"></canvas>
            </div>
        </div>
    @endif
</div>

{{-- Include Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if($filtersApplied && $itemTotals->count())
<script>
    const itemLabels = {!! json_encode($itemTotals->map(fn($d, $code) => $d['item_name'] . ' (' . $code . ')')->values()) !!};
    const itemQuantities = {!! json_encode($itemTotals->pluck('quantity')->values()) !!};

    // Pie Chart
    new Chart(document.getElementById('itemPieChart'), {
        type: 'pie',
        data: {
            labels: itemLabels,
            datasets: [{
                data: itemQuantities,
                backgroundColor: itemLabels.map((_, i) =>
                    `hsl(${i * 360 / itemLabels.length}, 70%, 60%)`
                ),
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: 'Item-wise Stock Quantity Distribution' }
            }
        }
    });

    // Bar Chart
    new Chart(document.getElementById('itemBarChart'), {
        type: 'bar',
        data: {
            labels: itemLabels,
            datasets: [{
                label: 'Quantity',
                data: itemQuantities,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Item-wise Quantity' }
            }
        }
    });
</script>

@endif

@endsection

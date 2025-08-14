@extends('layouts.app')

@section('content')
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

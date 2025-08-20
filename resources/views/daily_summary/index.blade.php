@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">Stock In Hand</h2>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('daily.summary') }}" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('daily.summary') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    {{-- Prepare Totals --}}
    @php
        $openingIn = 0;
        $openingOut = 0;
        $totalIn = 0;
        $totalOut = 0;
    @endphp

    {{-- PDF Export Form --}}
    @if(count($openingBalances) || count($dailySummary))
        <form method="POST" id="chartForm" action="{{ route('daily.summary.pdf') }}">
            @csrf
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            <input type="hidden" name="pie_chart_image" id="pie_chart_image">
            <input type="hidden" name="bar_chart_image" id="bar_chart_image">
            <button type="button" id="downloadPdfBtn" class="btn btn-danger mb-3">Download PDF with Charts</button>
        </form>
    @endif

    {{-- Combined Table --}}
@if((request('start_date') && count($openingBalances)) || count($dailySummary))
        <h4>Stock Summary (Opening + Transactions)</h4>
        <table class="table table-bordered">
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
                {{-- Opening Rows --}}
@if(request('start_date'))
    @foreach($openingBalances as $opening)
        @php
            $in = $opening->opening_in;
            $out = $opening->opening_out;
            $net = $in - $out;
            $openingIn += $in;
            $openingOut += $out;
        @endphp
        <tr>
            <td><em>Before {{ request('start_date') }}</em></td>
            <td>{{ $opening->item_code }}</td>
            <td>{{ $opening->item_name }}</td>
            <td class="text-success">+{{ $in }}</td>
            <td class="text-danger">-{{ $out }}</td>
            <td>{{ $net }}</td>
        </tr>
    @endforeach
@endif

                {{-- Daily Transactions Rows --}}
                @foreach($dailySummary as $day)
                    @php
                        $date = \Carbon\Carbon::parse($day->transaction_date)->format('Y-m-d');
                        $code = $day->item_code;
                        $name = $day->item_name;
                        $in = $day->stock_in ?? 0;
                        $out = $day->stock_out ?? 0;
                        $net = $in - $out;

                        $totalIn += $in;
                        $totalOut += $out;
                    @endphp
                    <tr>
                        <td>{{ $date }}</td>
                        <td>{{ $code }}</td>
                        <td>{{ $name }}</td>
                        <td class="text-success">+{{ $in }}</td>
                        <td class="text-danger">-{{ $out }}</td>
                        <td>{{ $net }}</td>
                    </tr>
                @endforeach

                {{-- Final Totals Row --}}
                <tr style="font-weight: bold; background-color: #f8f9fa;">
                    <td colspan="3" class="text-end">Total (Opening + Transactions)</td>
                    <td class="text-success">+{{ $openingIn + $totalIn }}</td>
                    <td class="text-danger">-{{ $openingOut + $totalOut }}</td>
                    <td>{{ ($openingIn + $totalIn) - ($openingOut + $totalOut) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- Charts --}}
    @if(request('start_date') || request('end_date'))
        <div class="row mb-5" style="width: 80%; margin: auto;">
            <div class="col-md-6">
                <h5 class="text-center">Pie Chart</h5>
                <canvas id="inOutPieChart"></canvas>
            </div>
            <div class="col-md-6">
                <h5 class="text-center">Bar Chart</h5>
                <canvas id="inOutBarChart"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const labels = ['Opening IN', 'Opening OUT', 'Filtered IN', 'Filtered OUT'];
            const values = [
                {{ $openingIn }},
                {{ $openingOut }},
                {{ $totalIn }},
                {{ $totalOut }}
            ];

            new Chart(document.getElementById('inOutPieChart'), {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(255, 159, 64, 0.7)'
                        ],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Stock IN/OUT Distribution' }
                    }
                }
            });

            new Chart(document.getElementById('inOutBarChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Quantity',
                        data: values,
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(255, 159, 64, 0.7)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Quantity' }
                        },
                        x: {
                            title: { display: true, text: 'Transaction Type' }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        title: { display: true, text: 'Stock IN vs OUT (Bar Chart)' }
                    }
                }
            });
        </script>
    @endif

</div>

{{-- JS: Handle PDF Chart Export --}}
<script>
document.querySelector("#downloadPdfBtn").addEventListener("click", function () {
    const pieChart = document.getElementById("inOutPieChart");
    const barChart = document.getElementById("inOutBarChart");

    const pieImage = pieChart.toDataURL("image/png");
    const barImage = barChart.toDataURL("image/png");

    document.getElementById("pie_chart_image").value = pieImage;
    document.getElementById("bar_chart_image").value = barImage;

    document.getElementById("chartForm").submit();
});
</script>
@endsection

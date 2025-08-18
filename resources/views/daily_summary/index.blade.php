@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">Stock In hand</h2>

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

    @if(count($dailySummary))
        <form method="GET" action="{{ route('daily.summary.pdf') }}" target="_blank" class="mb-3">
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            <button type="submit" class="btn btn-danger">Download PDF</button>
        </form>

        @php
            $totalIn = 0;
            $totalOut = 0;
        @endphp

        <table class="table table-bordered mb-5">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th class="text-success">Stock In</th>
                    <th class="text-danger">Stock Out</th>
                    <th>Net Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailySummary as $day)
                    @php
                        $date = \Carbon\Carbon::parse($day->transaction_date)->format('Y-m-d');
                        $code = $day->item_code ?? 'N/A';
                        $name = $day->item_name ?? 'N/A';
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

                <tr style="font-weight: bold; background-color: #f8f9fa;">
                    <td colspan="3" class="text-end">Total</td>
                    <td class="text-success">+{{ $totalIn }}</td>
                    <td class="text-danger">-{{ $totalOut }}</td>
                    <td>{{ $totalIn - $totalOut }}</td>
                </tr>
            </tbody>
        </table>

       

       
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
                const labels = ['IN', 'OUT'];
                const values = [{{ $totalIn }}, {{ $totalOut }}];

                new Chart(document.getElementById('inOutPieChart'), {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(255, 99, 132, 0.8)'
                            ],
                            borderColor: '#fff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' },
                            title: {
                                display: true,
                                text: 'Stock IN vs OUT (Pie Chart)'
                            }
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
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(255, 99, 132, 0.8)'
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)'
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
                            title: {
                                display: true,
                                text: 'Stock IN vs OUT (Bar Chart)'
                            }
                        }
                    }
                });
            </script>
        @endif
    @endif
</div>
@endsection

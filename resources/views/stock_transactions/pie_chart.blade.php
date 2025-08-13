@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Stock IN vs OUT Chart</h2>

    <!-- Date Range Info -->
    @if($startDate && $endDate)
        <p><strong>Period:</strong> {{ $startDate }} to {{ $endDate }}</p>
    @endif



<a href="{{ route('stock.download', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
   class="btn btn-danger mb-3">
    <i class="bi bi-file-earmark-pdf"></i> Download PDF Report
</a>
 
    <div class="row" style="width: 80%; margin: auto;">
        <div class="col-md-6">
            <h5 class="text-center">Pie Chart</h5>
            <canvas id="inOutPieChart"></canvas>
        </div>

        <div class="col-md-6">
            <h5 class="text-center">Bar Chart</h5>
            <canvas id="inOutBarChart"></canvas>
        </div>
    </div>

    <!-- Summary Table Below Charts -->
    <div class="mt-4" style="width: 60%; margin: auto;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Transaction Type</th>
                    <th>Total Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($labels as $index => $label)
                    <tr>
                        <td> Stock {{ $label }}</td>
                        <td>{{ $values[$index] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const labels = @json($labels);
        const values = @json($values);

        // Pie Chart
        const ctxPie = document.getElementById('inOutPieChart').getContext('2d');
        new Chart(ctxPie, {
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
                        text: 'Stock Quantities: Stock IN vs Stock OUT (Pie)'
                    }
                }
            }
        });

        // Bar Chart
        const ctxBar = document.getElementById('inOutBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Stock Quantity',
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
                        title: {
                            display: true,
                            text: 'Quantity'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Transaction Type'
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Stock Quantities: Stock IN vs Stock OUT (Bar)'
                    }
                }
            }
        });
    </script>
</div>
@endsection

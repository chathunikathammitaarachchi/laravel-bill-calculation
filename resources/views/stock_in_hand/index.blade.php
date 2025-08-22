@extends('layouts.app')

@section('content')
<div class="stock-container">
    <div class="header-section">
        <h1 class="page-title">Stock In Hand Report</h1>
    </div>

   <form method="GET" action="{{ route('stock_in_hand.index') }}" class="filter-form">
    <div class="filter-group">
        <label for="date" class="filter-label">Select Date & Time:</label>
        <input 
            type="datetime-local" 
            name="date" 
            id="date" 
            value="{{ request('date') ?? now()->format('Y-m-d\TH:i') }}" 
            class="date-input"
        >
        <button type="submit" class="btn-filter">Filter</button>
        <a href="{{ route('stock_in_hand.index') }}" class="btn-reset">Reset</a>
    </div>
</form>


    <!-- Stock Table -->
    <div class="table-wrapper">
        <table class="stock-table">
            <thead>
                <tr class="table-header">
                    <th class="th-item-code">Item Code</th>
                    <th class="th-item-name">Item Name</th>
                    <th class="th-stock-balance">Stock Balance</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse($stockInHands as $stock)
                    @php
                        $balance = $stock->stock_balance;
                        $balanceClass = $balance < 0 ? 'negative' : ($balance == 0 ? 'zero' : 'positive');
                    @endphp
                    <tr class="table-row" data-balance="{{ $balanceClass }}">
                        <td class="td-item-code">{{ $stock->item_code }}</td>
                        <td class="td-item-name">{{ $stock->item_name }}</td>
                        <td class="td-stock-balance {{ $balanceClass }}">
                            {{ number_format($balance, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="3" class="empty-message">No stock data found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.stock-container {
    padding: 20px;
    font-family: Arial, sans-serif;
    
    min-height: 100vh;
}

/* Title */
.page-title {
    font-size: 28px;
    font-weight: bold;
    color: #ffffffff;
    text-align: left;
    margin-bottom: 25px;
}

/* Filter form */
.filter-form {
    margin-bottom: 20px;
    text-align: left;
}

.filter-label {
    margin-right: 10px;
    font-weight: 600;
    color: #ffffffff;
}

.date-input {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.btn-filter, .btn-reset {
    padding: 8px 16px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    margin-left: 8px;
    transition: background-color 0.3s ease;
}

.btn-filter {
    background-color: #007bff;
    color: white;
}

.btn-filter:hover {
    background-color: #0056b3;
}

.btn-reset {
    background-color: #e2e6ea;
    color: #333;
}

.btn-reset:hover {
    background-color: #cacfd4;
}

/* Table styling */
.table-wrapper {
    overflow-x: auto;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.stock-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 450px;
}

.stock-table th, .stock-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.stock-table th {
    background-color: #007bff;
    color: white;
    font-weight: 700;
}

.th-stock-balance, .td-stock-balance {
    text-align: right;
    font-family: monospace;
}

/* Balance colors */
.positive {
    color: #2c7a2c;
    font-weight: 700;
}

.negative {
    color: #c62828;
    font-weight: 700;
}

.zero {
    color: #555;
    font-weight: 700;
}

/* Empty row */
.empty-message {
    text-align: center;
    padding: 20px;
    color: #888;
    font-style: italic;
}

/* Responsive */
@media (max-width: 600px) {
    .filter-form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .date-input, .btn-filter, .btn-reset {
        width: 100%;
        margin: 5px 0;
    }
}

</style>
@endsection



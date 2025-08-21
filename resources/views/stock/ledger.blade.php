@extends('layouts.app')

@section('content')
<div class="container py-5">
<!-- jQuery & jQuery UI CSS -->
<link href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" rel="stylesheet" />

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <h1 class="mb-4 fw-bold text-primary">📒 Stock Ledger</h1>



    {{-- Filters --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Filter Transactions</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('stock.ledger') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label fw-semibold">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label fw-semibold">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-control">
                    </div>
                   <div class="col-md-3">
                        <label for="item_code" class="form-label fw-semibold">Item Code</label>
                        <input type="text" name="item_code" id="item_code" value="{{ request('item_code') }}" class="form-control" placeholder="e.g. ITEM001" autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label for="item_name" class="form-label fw-semibold">Item Name</label>
                        <input type="text" name="item_name" id="item_name" value="{{ request('item_name') }}" class="form-control" placeholder="e.g. Widget" autocomplete="off">
                    </div>

                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel-fill me-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('stock.ledger') }}" class="btn btn-outline-secondary ms-2">Clear</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Opening Balances --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Opening Balances</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Item Code</th>
                            <th>Opening IN</th>
                            <th>Opening OUT</th>
                            <th>Opening Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($openingInBalance as $code => $in)
                            <tr>
                                <td class="fw-semibold">{{ $code }}</td>
                                <td class="text-success">{{ $in }}</td>
                                <td class="text-danger">{{ $openingOutBalance[$code] ?? 0 }}</td>
                                <td class="fw-bold">{{ $in - ($openingOutBalance[$code] ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  <form method="GET" action="{{ route('stock.ledger.pdf') }}" target="_blank" class="mt-3">
    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
    <input type="hidden" name="item_code" value="{{ request('item_code') }}">
    <input type="hidden" name="item_name" value="{{ request('item_name') }}">

    <button type="submit" class="btn btn-outline-danger">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF
    </button>
</form>
    {{-- Ledger Transactions --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Transaction History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered table-sm mb-0 align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Running Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($flatHistory as $entry)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($entry['date'])->format('Y-m-d') }}</td>
                                <td>{{ $entry['code'] }}</td>
                                <td>{{ $entry['name'] }}</td>
                                <td>{{ $entry['type'] }}</td>
                                <td class="{{ $entry['qty'] < 0 ? 'text-danger fw-semibold' : 'text-success fw-semibold' }}">
                                    {{ $entry['qty'] }}
                                </td>
                                <td class="fw-bold">{{ $entry['balance'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center fst-italic text-muted">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Totals --}}
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Totals</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Item Code</th>
                            <th>Total IN</th>
                            <th>Total OUT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inTotal as $code => $totalIn)
                            <tr>
                                <td class="fw-semibold">{{ $code }}</td>
                                <td class="text-success fw-bold">{{ $totalIn }}</td>
                                <td class="text-danger fw-bold">{{ $outTotal[$code] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<script>
$(function() {
    function syncFields(item) {
        $('#item_code').val(item.value);
        $('#item_name').val(item.name);
    }

    $("#item_code").autocomplete({
        source: "{{ route('items.autocomplete') }}",
        minLength: 1,
        select: function(event, ui) {
            event.preventDefault();
            syncFields(ui.item);
        },
        focus: function(event, ui) {
            event.preventDefault();
            $('#item_code').val(ui.item.value);
        }
    });

    $("#item_name").autocomplete({
        source: "{{ route('items.autocomplete') }}",
        minLength: 1,
        select: function(event, ui) {
            event.preventDefault();
            syncFields(ui.item);
        },
        focus: function(event, ui) {
            event.preventDefault();
            $('#item_name').val(ui.item.name);
        }
    });
});
</script>

@endsection

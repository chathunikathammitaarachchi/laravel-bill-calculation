@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Customer Dues</h2>

    {{-- Filter Form --}}
   <form method="GET" action="{{ route('grn.dues') }}" class="row g-3 mb-4">
    <div class="col-md-3">
        <label for="from_date" class="form-label">From Date</label>
        <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" class="form-control">
    </div>
    <div class="col-md-3">
        <label for="to_date" class="form-label">To Date</label>
        <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" class="form-control">
    </div>
    <div class="col-md-3">
        <label for="customer_name" class="form-label">Customer Name</label>
        <input list="customers" name="customer_name" id="customer_name" value="{{ request('customer_name') }}" class="form-control" placeholder="Type customer name...">
        <datalist id="customers">
            @foreach(\App\Models\CustomerDue::select('customer_name')->distinct()->orderBy('customer_name')->get() as $customer)
                <option value="{{ $customer->customer_name }}">
            @endforeach
        </datalist>
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary me-2">Filter</button>
        <a href="{{ route('grn.dues') }}" class="btn btn-secondary me-2">Reset</a>

        @if(count($dues) > 0)
            <a href="{{ route('grn.dues.export', request()->only(['from_date', 'to_date', 'customer_name'])) }}" class="btn btn-success">Download PDF</a>
        @endif
    </div>
</form>


    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    @if($dues->isEmpty())
        <p>No dues available for selected date range.</p>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Bill No</th>
                    <th>Customer Name</th>
                    <th>Due Amount</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dues as $due)
                    <tr>
                        <td>{{ $due->bill_no }}</td>
                        <td>{{ $due->customer_name }}</td>
                        <td>{{ number_format($due->tobe_price, 2) }}</td>
                        <td>{{ number_format($due->customer_pay, 2) }}</td>
                        <td>{{ number_format($due->balance, 2) }}</td>
                        <td>{{ $due->grn_date }}</td>
                        <td>
                            @if ($due->balance > 0)
                                <span class="badge bg-warning text-dark">Pending</span>
                            @else
                                <span class="badge bg-success">Settled</span>
                            @endif
                        </td>
                        <td>
                            @if ($due->balance > 0)
                                <a href="{{ route('due_payments.form', $due->id) }}" class="btn btn-sm btn-primary">Pay Now</a>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled>Paid</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection

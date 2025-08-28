@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1550px; margin-top: 40px;">
    <h2 class="mb-4" style="font-weight: 700; color: #ffffffff; border-bottom: 2px solid #3490dc; padding-bottom: 10px;">
        Customer Dues
    </h2>

    {{-- Filter Form --}}
   <form method="GET" action="{{ route('grn.dues') }}" class="row g-3 mb-4" style="background: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <div class="col-md-3">
        <label for="from_date" class="form-label" style="font-weight: 600;">From Date</label>
        <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" class="form-control" style="border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div class="col-md-3">
        <label for="to_date" class="form-label" style="font-weight: 600;">To Date</label>
        <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" class="form-control" style="border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div class="col-md-3">
        <label for="customer_name" class="form-label" style="font-weight: 600;">Customer Name</label>
        <input list="customers" name="customer_name" id="customer_name" value="{{ request('customer_name') }}" class="form-control" placeholder="Type customer name..." style="border: 1px solid #ccc; border-radius: 4px;">
        <datalist id="customers">
            @foreach(\App\Models\CustomerDue::select('customer_name')->distinct()->orderBy('customer_name')->get() as $customer)
                <option value="{{ $customer->customer_name }}">
            @endforeach
        </datalist>
    </div>
    <div class="col-md-3 d-flex align-items-end" style="gap: 10px;">
        <button type="submit" class="btn btn-primary" style="flex: 1;">Filter</button>
        <a href="{{ route('grn.dues') }}" class="btn btn-secondary" style="flex: 1;">Reset</a>

        @if(count($dues) > 0)
            <a href="{{ route('grn.dues.export', request()->only(['from_date', 'to_date', 'customer_name'])) }}" class="btn btn-success" style="flex: 5;">Download PDF</a>
        @endif
    </div>
</form>
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


    {{-- Table --}}
    @if($dues->isEmpty())
        <p style="font-style: italic; color: #888;">No dues available for selected date range.</p>
    @else
    <div class="table-responsive" style="box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;">
        <table class="table table-bordered table-striped" style="margin-bottom: 0;">
            <thead class="table-dark" style="background-color: #2c3e50; color: #fff;">
                <tr>
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
    @forelse ($dues as $due)
        <tr>
            <td style="vertical-align: middle;"><strong>{{ $due->customer_name }}</strong></td>
            <td style="vertical-align: middle; text-align: right;">{{ number_format($due->total_due, 2) }}</td>
            <td style="vertical-align: middle; text-align: right;">{{ number_format($due->total_paid, 2) }}</td>
            <td style="vertical-align: middle; text-align: right;">{{ number_format($due->total_balance, 2) }}</td>
            <td style="vertical-align: middle;">{{ $due->last_date }}</td>
            <td style="vertical-align: middle;">
                @if ($due->total_balance > 0)
                    <span class="badge bg-warning text-dark" style="font-weight: 600;">Pending</span>
                @else
                    <span class="badge bg-success" style="font-weight: 600;">Settled</span>
                @endif
            </td>
            <td style="vertical-align: middle;">
                @if ($due->total_balance > 0)
<a href="{{ route('due_payments.form.by.customer', ['customer_name' => $due->customer_name]) }}" class="btn btn-sm btn-primary">Pay Now</a>
                @else
                    <button class="btn btn-sm btn-secondary" disabled>Paid</button>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center">No dues available.</td>
        </tr>
    @endforelse
</tbody>

        </table>
    </div>
    @endif
</div>
@endsection

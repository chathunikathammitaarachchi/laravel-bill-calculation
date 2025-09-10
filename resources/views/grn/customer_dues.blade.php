@extends('layouts.app')

@section('content')
<div class="container py-4">
     <h2 class="mb-4" style="font-weight: 700; color: #ffffffff; border-bottom: 2px solid #3490dc; padding-bottom: 10px;">
        Customer Dues
    </h2>
    {{-- Filter Form --}}
    <form method="GET" action="{{ route('customer_dues.list') }}" class="row g-3 mb-4" style="background: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
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
            <a href="{{ route('customer_dues.list') }}" class="btn btn-secondary" style="flex: 1;">Reset</a>

           
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Total </th>
                <th>pay</th>
                <th>To be paid </th>
                <th>Last Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dues as $due)
                <tr>
                    <td>{{ $due->customer_name }}</td>
                    <td>Rs. {{ number_format($due->total_due, 2) }}</td>
                    <td>Rs. {{ number_format($due->total_paid, 2) }}</td>
                    <td class="text-danger fw-bold">Rs. {{ number_format($due->total_balance, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($due->last_date)->format('Y-m-d') }}</td>
                   <td>
    @if($due->total_balance > 0)
        <a href="{{ route('customer_due.show', $due->customer_name) }}" class="btn btn-primary btn-sm">Pay Due</a>
    @else
        <span class="badge bg-success">Settled</span>
    @endif
</td>

                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('home') }}" class="btn btn-secondary mb-3">Back</a>

</div>
@endsection

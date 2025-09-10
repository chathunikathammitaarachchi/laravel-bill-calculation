@extends('layouts.app')

@section('content')
<div class="container">
<h2 style="color: white; font-weight: bold;">All Cheque Payments (View Only)</h2>

{{-- Search Filter Form --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">
        <strong>Filter Cheque Payments</strong>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('cheque.list') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Cheque Number</label>
                    <input type="text" name="cheque_number" class="form-control" value="{{ request('cheque_number') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" value="{{ request('bank_name') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Branch Name</label>
                    <input type="text" name="branch_name" class="form-control" value="{{ request('branch_name') }}">
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-success">Apply Filters</button>
                <a href="{{ route('cheque.list') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

    {{-- Cheque Table --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Payment Method</th>
                <th>Amount</th>
                <th>Cheque No</th>
                <th>Bank</th>
                <th>Branch</th>
                <th>Cheque Date</th>
                <th>Status</th>
            </tr>
        </thead>
       <tbody>
    @forelse($payments as $payment)
        <tr>
            <td>{{ $payment->due->supplier_name ?? '-' }}</td>
            <td>{{ $payment->payment_method }}</td>
            <td>{{ number_format($payment->amount, 2) }}</td>
            <td>{{ $payment->cheque_number ?? '-' }}</td>
            <td>{{ $payment->bank_name ?? '-' }}</td>
            <td>{{ $payment->branch_name ?? '-' }}</td>
            <td>{{ $payment->cheque_date ?? '-' }}</td>
            <td>
                @if($payment->is_returned)
                    <span class="badge bg-danger">Returned</span>
                @else
                    <span class="badge bg-success">Paid</span>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center">
                @if(request()->hasAny(['cheque_number', 'bank_name', 'branch_name']))
                    No cheque payments found for the given filters.
                @else
                    Please use the filter above to search cheque payments.
                @endif
            </td>
        </tr>
    @endforelse
</tbody>

    </table>


    
</div>
@endsection

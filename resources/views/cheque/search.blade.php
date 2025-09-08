@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Search Cheque Payments</h4>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('cheque.search') }}">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Cheque Number</label>
                        <input type="text" name="cheque_number" class="form-control" placeholder="Enter cheque number" value="{{ request('cheque_number') }}">
                    </div>
                    <div class="col-md-4">
                        <label>Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" placeholder="Enter bank name" value="{{ request('bank_name') }}">
                    </div>
                    <div class="col-md-4">
                        <label>Branch Name</label>
                        <input type="text" name="branch_name" class="form-control" placeholder="Enter branch name" value="{{ request('branch_name') }}">
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-success">Search</button>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('cheque_number') || request()->has('bank_name') || request()->has('branch_name'))
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Search Results</h5>
            </div>
            <div class="card-body">
                @if($results->count())
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Supplier</th>
                                <th>Amount</th>
                                <th>Cheque No</th>
                                <th>Bank</th>
                                <th>Branch</th>
                                <th>Status</th>
                                <th>Return</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $payment)
                            <tr>
                                <td>{{ $payment->due->supplier_name ?? '-' }}</td>
                                <td>Rs {{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->cheque_number }}</td>
                                <td>{{ $payment->bank_name }}</td>
                                <td>{{ $payment->branch_name }}</td>
                                <td>
                                    @if($payment->is_returned)
                                        <span class="badge bg-danger">Returned</span>
                                    @else
                                        <span class="badge bg-success">Paid</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$payment->is_returned)
                                    <form method="POST" action="{{ route('cheque.return', $payment->id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-warning" onclick="return confirm('Are you sure to return this cheque?')">Return</button>
                                    </form>
                                    @else
                                        <em>N/A</em>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning mb-0">No matching cheque records found.</div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

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
                @if($groupedPayments->count())
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Cheque No</th>
                <th>Bank</th>
                <th>Branch</th>
                <th>Cheque Date</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Return</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedPayments as $key => $chequeGroup)
                @php
                    $first = $chequeGroup->first();
                    [$chequeNumber, $bankName, $branchName, $chequeDate] = explode('|', $key);
                    $totalAmount = $chequeGroup->sum('amount');
                    $isReturned = $chequeGroup->contains('is_returned', true);
                @endphp

                <tr class="table-primary">
                    <td>{{ $chequeNumber }}</td>
                    <td>{{ $bankName }}</td>
                    <td>{{ $branchName }}</td>
                    <td>{{ $chequeDate }}</td>
                    <td>Rs {{ number_format($totalAmount, 2) }}</td>
                    <td>
                        @if($isReturned)
                            <span class="badge bg-danger">Returned</span>
                        @else
                            <span class="badge bg-success">Paid</span>
                        @endif
                    </td>
                    <td>
                        @if(!$isReturned)
                            {{-- Take first payment ID to return --}}
                            <form method="POST" action="{{ route('cheque.return', $first->id) }}">
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
    <div class="alert alert-warning">No matching cheque records found.</div>
@endif

            </div>
        </div>
    @endif
</div>
@endsection

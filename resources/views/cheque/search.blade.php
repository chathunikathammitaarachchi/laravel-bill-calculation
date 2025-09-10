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

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

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
                                            <!-- Button to Open Modal -->
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#returnChequeModal{{ $first->id }}">
                                                Return
                                            </button>
                                        @else
                                            <em>N/A</em>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Modal -->
                                @if(!$isReturned)
                                <div class="modal fade" id="returnChequeModal{{ $first->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $first->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('cheque.return', $first->id) }}">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel{{ $first->id }}">Return Cheque</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="reason" class="form-label">Reason</label>
                                                        <input type="text" name="reason" class="form-control" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="return_date" class="form-label">Return Date</label>
                                                        <input type="date" name="return_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-danger">Confirm Return</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endif
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

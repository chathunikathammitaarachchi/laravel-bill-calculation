<-- resources/views/cheque/payments.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Supplier Payments</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Payment Method</th>
                <th>Amount</th>
                <th>Cheque No</th>
                <th>Bank</th>
                <th>Cheque Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->due->supplier_name ?? '-' }}</td>
                <td>{{ $payment->payment_method }}</td>
                <td>{{ number_format($payment->amount, 2) }}</td>
                <td>{{ $payment->cheque_number ?? '-' }}</td>
                <td>{{ $payment->bank_name ?? '-' }}</td>
                <td>{{ $payment->cheque_date ?? '-' }}</td>
                <td>
                    @if($payment->is_returned)
                        <span class="badge bg-danger">Returned</span>
                    @else
                        <span class="badge bg-success">Paid</span>
                    @endif
                </td>
                <td>
                    @if(!$payment->is_returned && $payment->payment_method == 'Cheque')
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#returnChequeModal{{ $payment->id }}">
                            Return Cheque
                        </button>
                    @else
                        <em>N/A</em>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- All modals outside table -->
    @foreach($payments as $payment)
        @if(!$payment->is_returned && $payment->payment_method == 'Cheque')
        <div class="modal fade" id="returnChequeModal{{ $payment->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $payment->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('cheque.return', $payment->id) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel{{ $payment->id }}">Return Cheque</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
</div>
@endsection

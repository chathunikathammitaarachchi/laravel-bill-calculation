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
                        <form method="POST" action="{{ route('cheque.return', $payment->id) }}" onsubmit="return confirm('Are you sure to mark this cheque as returned?')">
                            @csrf
                            @method('POST')
                            <button class="btn btn-sm btn-warning">Return Cheque</button>
                        </form>
                    @else
                        <em>N/A</em>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

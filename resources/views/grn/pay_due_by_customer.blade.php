@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow" style="max-width: 600px; margin: auto;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Pay Due - Customer: {{ $customerName }}</h5>
        </div>


        <div class="card-body">
            <div class="mb-3">
                <label class="form-label"><strong>Customer:</strong></label>
                <div class="form-control-plaintext">{{ $customerName }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label"><strong>Total Due:</strong></label>
                <div class="form-control-plaintext">Rs. {{ number_format($totalDue, 2) }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label"><strong>Total Paid:</strong></label>
                <div class="form-control-plaintext">Rs. {{ number_format($totalPaid, 2) }}</div>
            </div>

            <div class="mb-4">
                <label class="form-label"><strong>Total Balance:</strong></label>
                <div class="form-control-plaintext text-danger fw-bold">Rs. {{ number_format($totalBalance, 2) }}</div>
            </div>

            <form action="{{ route('due.pay') }}" method="POST">
                @csrf
                <input type="hidden" name="customer_name" value="{{ $customerName }}">

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="form-select" required>
                        <option value="">-- Select Payment Method --</option>
                        <option value="Cash">Cash</option>
                        <option value="Cheque">Cheque</option>
                        <!-- Add more methods as needed -->
                    </select>
                </div>

                <div class="mb-4">
                    <label for="amount" class="form-label">Amount to Pay</label>
                    <div class="input-group">
                        <span class="input-group-text">Rs.</span>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="amount" 
                            name="amount" 
                            step="0.01" 
                            max="{{ $totalBalance }}" 
                            min="0.01" 
                            required
                            placeholder="Enter amount (max {{ $totalBalance }})"
                        >
                    </div>
                    <div class="form-text">Maximum: Rs. {{ number_format($totalBalance, 2) }}</div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('grn.dues') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
 
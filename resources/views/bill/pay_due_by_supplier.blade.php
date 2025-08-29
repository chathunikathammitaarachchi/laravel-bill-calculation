@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow" style="max-width: 600px; margin: auto;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Pay Due - Supplier: {{ $supplier_name }}</h5>
        </div>


        <div class="card-body">
            <div class="mb-3">
                <label class="form-label"><strong>Supplier:</strong></label>
                <div class="form-control-plaintext">{{ $supplier_name }}</div>
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
                <input type="hidden" name="supplier_name" value="{{ $supplier_name }}">

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                  <select name="payment_method" id="payment_method" class="form-select" required>
    <option value="">-- Select Payment Method --</option>
    <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
    <option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
</select>

                </div>
<div class="mb-3" id="cheque-fields" style="display: none;">
    <label for="cheque_number" class="form-label">Cheque Number</label>
<input type="text" name="cheque_number" class="form-control" value="{{ old('cheque_number') }}">

    <label for="bank_name" class="form-label mt-2">Bank Name</label>
<input type="text" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
   <label for="branch_name" class="form-label mt-2">Branch Name</label>
<input type="text" name="branch_name" class="form-control" value="{{ old('branch_name') }}">

    <label for="cheque_date" class="form-label mt-2">Cheque Date</label>
<input type="date" name="cheque_date" class="form-control" value="{{ old('cheque_date') }}">
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
                    <a href="{{ route('bill.dues') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const methodSelect = document.getElementById('payment_method');
    const chequeFields = document.getElementById('cheque-fields');

    function toggleChequeFields() {
        if (methodSelect.value === 'Cheque') {
            chequeFields.style.display = 'block';
        } else {
            chequeFields.style.display = 'none';
        }
    }

    methodSelect.addEventListener('change', toggleChequeFields);
    toggleChequeFields(); // call once on page load
});

</script>

@endsection
 
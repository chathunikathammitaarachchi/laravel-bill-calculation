@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Customer</h2>
        <form action="{{ route('customer.update', $customer) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label>Customer ID</label>
                <input type="number" name="customer_id" class="form-control" value="{{ $customer->customer_id }}" required>
            </div>
            <div class="mb-3">
<label for="customer_name">Customer Name</label>
<input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ $customer->customer_name }}" required>

            </div>
            <div class="mb-3">
                <label>Phone Number</label>
                <input type="number" name="phone" class="form-control" value="{{ $customer->phone }}" required oninput="limitInput(this, 10)">
            </div>
            <button type="submit" class="btn btn-primary">Update Customer</button>
        </form>
    </div>

     <script>
function limitInput(elem, maxLength) {
    if (elem.value.length > maxLength) {
        elem.value = elem.value.slice(0, maxLength);
    }
}
</script>
@endsection

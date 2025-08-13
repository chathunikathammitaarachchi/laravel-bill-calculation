@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Add New Customer</h2>
        <form action="{{ route('customer.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="customer_id">Customer ID</label>
                <input type="number" name="customer_id" id="customer_id" class="form-control" autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label for="customer_name">Customer Name</label>
                <input type="text" name="customer_name" id="customer_name" class="form-control" autocomplete="name" required>
            </div>
            <div class="mb-3">
                <label for="phone">Phone Number</label>
               
               <input type="number" name="phone" id="phone" class="form-control" autocomplete="tel" required oninput="limitInput(this, 10)">

            </div>
            <button type="submit" class="btn btn-success">Add Customer</button>
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

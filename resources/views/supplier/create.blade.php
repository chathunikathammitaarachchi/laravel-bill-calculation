@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Add New Supplier</h2>
        <form action="{{ route('supplier.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="supplier_id">Supplier ID</label>
                <input type="number" name="supplier_id" id="customer_id" class="form-control" autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label for="supplier_name">Supplier Name</label>
                <input type="text" name="supplier_name" id="supplier_name" class="form-control" autocomplete="name" required>
            </div>
            <div class="mb-3">
                <label for="phone">Phone Number</label>
               
               <input type="number" name="phone" id="phone" class="form-control" autocomplete="tel" required oninput="limitInput(this, 10)">

            </div>

            <div class="mb-3">
                <label for="address">Supplier Address</label>
                <input type="text" name="address" id="address" class="form-control" autocomplete="address" required>
            </div>

            
            <button type="submit" class="btn btn-success">Add Supplier</button>
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

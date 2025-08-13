@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Supplier</h2>
        <form action="{{ route('supplier.update', $supplier) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label>Supplier ID</label>
                <input type="number" name="supplier_id" class="form-control" value="{{ $supplier->supplier_id }}" required>
            </div>
            <div class="mb-3">
<label for="supplier_name">Supplier Name</label>
<input type="text" name="supplier_name" id="supplier_name" class="form-control" value="{{ $supplier->supplier_name }}" required>

            </div>

           
            <div class="mb-3">
                <label>Phone Number</label>
                <input type="number" name="phone" class="form-control" value="{{ $supplier->phone }}" required oninput="limitInput(this, 10)">
            </div>


             <div class="mb-3">
<label for="address">Supplier Address</label>
<input type="text" name="address" id="address" class="form-control" value="{{ $supplier->address }}" required>

            </div>

            
            <button type="submit" class="btn btn-primary">Update Supplier</button>
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

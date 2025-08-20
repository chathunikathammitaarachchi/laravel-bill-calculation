@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Edit Supplier</h4>
                </div>
                <div class="card-body bg-light">
                    <form action="{{ route('supplier.update', $supplier) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Supplier ID</label>
                            <input type="number" name="supplier_id" id="supplier_id" class="form-control" value="{{ $supplier->supplier_id }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="supplier_name" class="form-label">Supplier Name</label>
                            <input type="text" name="supplier_name" id="supplier_name" class="form-control" value="{{ $supplier->supplier_name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="number" name="phone" id="phone" class="form-control" value="{{ $supplier->phone }}" required oninput="limitInput(this, 10)">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Supplier Address</label>
                            <input type="text" name="address" id="address" class="form-control" value="{{ $supplier->address }}" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">Update Supplier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function limitInput(elem, maxLength) {
    if (elem.value.length > maxLength) {
        elem.value = elem.value.slice(0, maxLength);
    }
}
</script>
@endsection

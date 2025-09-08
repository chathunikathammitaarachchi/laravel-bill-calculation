@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow-lg rounded-4 p-4" style="max-width: 480px; width: 100%;">
    <div class="text-center mb-4">
      <h2 class="fw-bold text-success">Add New Supplier</h2>
      <p class="text-muted">Enter supplier details to register</p>
    </div>
@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

    <form action="{{ route('supplier.store') }}" method="POST" novalidate>
      @csrf

      <div class="form-floating mb-4">
        <input type="number" name="supplier_id" id="supplier_id" class="form-control form-control-lg" placeholder="Supplier ID" required>
        <label for="supplier_id" class="text-muted">Supplier ID</label>
      </div>

      <div class="form-floating mb-4">
        <input type="text" name="supplier_name" id="supplier_name" class="form-control form-control-lg" placeholder="Supplier Name" required>
        <label for="supplier_name" class="text-muted">Supplier Name</label>
      </div>

      <div class="form-floating mb-4">
        <input type="number" name="phone" id="phone" class="form-control form-control-lg" placeholder="Phone Number" required oninput="limitInput(this, 10)">
        <label for="phone" class="text-muted">Phone Number</label>
      </div>

      <div class="form-floating mb-4">
        <input type="text" name="address" id="address" class="form-control form-control-lg" placeholder="Supplier Address" required>
        <label for="address" class="text-muted">Supplier Address</label>
      </div>

      <button type="submit" class="btn btn-success btn-lg w-100 fw-semibold shadow-sm">
        Add Supplier
      </button>
    </form>
  </div>
</div>

<style>
  body {
    background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .card {
    background: #fff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
  }

  .form-control:focus {
    border-color: #43cea2;
    box-shadow: 0 0 8px #43cea2;
  }

  button.btn-success {
    background: #43cea2;
    border: none;
    transition: background 0.3s ease;
  }

  button.btn-success:hover {
    background: #2abf91;
    box-shadow: 0 8px 20px rgba(67, 206, 162, 0.6);
  }

  label {
    user-select: none;
  }
</style>

<script>
  function limitInput(elem, maxLength) {
    if (elem.value.length > maxLength) {
      elem.value = elem.value.slice(0, maxLength);
    }
  }
</script>
@endsection

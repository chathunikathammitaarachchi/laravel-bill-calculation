@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow-lg rounded-5 p-4" style="max-width: 420px; width: 100%;">
    <div class="text-center mb-4">
      <h2 class="fw-bold text-primary">Edit Customer</h2>
      <p class="text-muted">Update the customer details below</p>
    </div>
    <form action="{{ route('customer.update', $customer) }}" method="POST" novalidate>
      @csrf
      @method('PUT')

      <div class="form-floating mb-4">
        <input type="number" class="form-control form-control-lg" id="customer_id" name="customer_id" placeholder="Customer ID" value="{{ $customer->customer_id }}" autocomplete="off" required>
        <label for="customer_id" class="text-muted">Customer ID</label>
      </div>

      <div class="form-floating mb-4">
        <input type="text" class="form-control form-control-lg" id="customer_name" name="customer_name" placeholder="Customer Name" value="{{ $customer->customer_name }}" autocomplete="name" required>
        <label for="customer_name" class="text-muted">Customer Name</label>
      </div>

      <div class="form-floating mb-4">
        <input type="number" class="form-control form-control-lg" id="phone" name="phone" placeholder="Phone Number" value="{{ $customer->phone }}" autocomplete="tel" required oninput="limitInput(this,10)">
        <label for="phone" class="text-muted">Phone Number</label>
      </div>

      <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold shadow-sm">
        Update Customer
      </button>
    </form>
  </div>
</div>

<script>
  function limitInput(elem, maxLength) {
    if (elem.value.length > maxLength) {
      elem.value = elem.value.slice(0, maxLength);
    }
  }
</script>

<style>
  body {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  .card {
    background: #fff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(33, 136, 56, 0.3);
  }
  .form-control:focus {
    border-color: #2575fc;
    box-shadow: 0 0 10px #2575fc;
  }
  label {
    user-select: none;
  }
  button.btn-primary {
    background: #2575fc;
    border: none;
    transition: background 0.3s ease;
  }
  button.btn-primary:hover {
    background: #1a59d9;
    box-shadow: 0 8px 20px rgba(37, 117, 252, 0.6);
  }
</style>
@endsection

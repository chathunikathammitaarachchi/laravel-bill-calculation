@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow-lg rounded-5 p-4" style="max-width: 420px; width: 100%;">
    <div class="text-center mb-4">
      <h2 class="fw-bold text-primary">Add New Customer</h2>
      <p class="text-muted">Fill the details below to add a new customer</p>
    </div>

    {{-- Show validation errors --}}
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('customer.store') }}" method="POST" id="customerForm" novalidate>
      @csrf

      <div class="form-floating mb-4">
        <input type="number" class="form-control form-control-lg" id="customer_id" name="customer_id" placeholder="Customer ID" required>
        <label for="customer_id" class="text-muted">Customer ID</label>
      </div>

      <div class="form-floating mb-4">
        <input type="text" class="form-control form-control-lg" id="customer_name" name="customer_name" placeholder="Customer Name" required>
        <label for="customer_name" class="text-muted">Customer Name</label>
      </div>

      <div class="form-floating mb-4">
        <input type="tel" class="form-control form-control-lg" id="phone" name="phone" placeholder="Phone Number" required oninput="limitInput(this,10)">
        <label for="phone" class="text-muted">Phone Number</label>
      </div>

      <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold shadow-sm">
        Add Customer
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

  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('customerForm');

    form.addEventListener('submit', function (e) {
      const id = document.getElementById('customer_id').value.trim();
      const name = document.getElementById('customer_name').value.trim();
      const phone = document.getElementById('phone').value.trim();

      if (!id || !name || !phone) {
        alert("🚫 All fields are required.");
        e.preventDefault();
        return;
      }

      if (phone.length < 10) {
        alert("📱 Phone number must be at least 10 digits.");
        e.preventDefault();
      }
    });
  });
</script>

<style>
  body {
    background: linear-gradient(135deg, #6a11cb 0%, #ec98d0ff 100%);
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

@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow-lg rounded-4 p-4" style="max-width: 480px; width: 100%;">
    <div class="text-center mb-4">
      <h2 class="fw-bold text-success">Add New Item</h2>
      <p class="text-muted">Enter item details to add to inventory</p>
    </div>

    <form action="{{ route('items.store') }}" method="POST" novalidate>
      @csrf

      <div class="form-floating mb-4">
        <input type="number" name="item_code" id="item_code" class="form-control form-control-lg" placeholder="Item Code" required>
        <label for="item_code" class="text-muted">Item Code</label>
      </div>

      <div class="form-floating mb-4">
        <input type="text" name="item_name" id="item_name" class="form-control form-control-lg" placeholder="Item Name" required>
        <label for="item_name" class="text-muted">Item Name</label>
      </div>

      <div class="form-floating mb-4">
        <input type="number" step="0.01" name="rate" id="rate" class="form-control form-control-lg" placeholder="Rate" required>
        <label for="rate" class="text-muted">Rate (Rs.)</label>
      </div>

      <div class="form-floating mb-4">
        <input type="number" step="0.01" name="cost_price" id="cost_price" class="form-control form-control-lg" placeholder="Cost Price" required>
        <label for="cost_price" class="text-muted">Cost Price (Rs.)</label>
      </div>


      <div class="form-floating mb-4">
        <input type="number" name="stock" id="stock" class="form-control form-control-lg" placeholder="Stock" required>
        <label for="stock" class="text-muted">Stock</label>
      </div>

      <button type="submit" class="btn btn-success btn-lg w-100 fw-semibold shadow-sm">
        Add Item
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
  label {
    user-select: none;
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
</style>
@endsection

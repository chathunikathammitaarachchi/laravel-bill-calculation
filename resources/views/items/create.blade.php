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

      {{-- Unit Selection --}}
      <div class="form-floating mb-4">
        <select name="unit_select" id="unit_select" class="form-select form-select-lg" onchange="toggleCustomUnit()" required>
          <option value="">-- Select Unit --</option>
          <option value="pcs">Pieces (pcs)</option>
          <option value="kg">Kilogram (kg)</option>
          <option value="g">Gram (g)</option>
          <option value="l">Litre (l)</option>
          <option value="ml">Millilitre (ml)</option>
          <option value="box">Box</option>
          <option value="pack">Pack</option>
          <option value="other">Other</option>
        </select>
        <label for="unit_select" class="text-muted">Unit</label>
      </div>

      <div class="form-floating mb-4" id="custom_unit_wrapper" style="display: none;">
        <input type="text" name="unit_custom" id="unit_input" class="form-control form-control-lg" placeholder="Custom Unit">
        <label for="unit_input" class="text-muted">Custom Unit</label>
      </div>

      {{-- Final hidden unit value --}}
      <input type="hidden" name="unit" id="unit_hidden">

      {{-- Category Selection --}}
      <div class="form-floating mb-4">
        <select name="category_select" id="category_select" class="form-select form-select-lg" onchange="toggleCustomCategory()" required>
          <option value="">-- Select Category --</option>
          <option value="groceries">Groceries</option>
          <option value="beverages">Beverages</option>
          <option value="fruits">Fruits</option>
          <option value="vegetables">Vegetables</option>
          <option value="dairy">Dairy Products</option>
          <option value="bakery">Bakery Items</option>
          <option value="snacks">Snacks</option>
          <option value="household">Household Items</option>
          <option value="cleaning">Cleaning Supplies</option>
          <option value="personal_care">Personal Care</option>
          <option value="stationery">Stationery</option>
          <option value="toys">Toys</option>
          <option value="clothing">Clothing</option>
          <option value="hardware">Hardware</option>
          <option value="other">Other</option>
        </select>
        <label for="category_select" class="text-muted">Category</label>
      </div>

      <div class="form-floating mb-4" id="custom_category_wrapper" style="display: none;">
        <input type="text" name="custom_category" id="custom_category" class="form-control form-control-lg" placeholder="Enter custom category">
        <label for="custom_category" class="text-muted">Enter Custom Category</label>
      </div>

      <input type="hidden" name="category" id="category_hidden">

      <div class="form-floating mb-4">
        <input type="number" name="stock" id="stock" class="form-control form-control-lg" placeholder="Stock" required>
        <label for="stock" class="text-muted">Stock</label>
      </div>
<div class="form-group">
    <label for="discount_1">Discount 1 </label>
    <input type="number" name="discount_1" class="form-control" value="{{ old('discount_1', $item->discount_1 ?? '') }}" min="0" max="100">
</div>

<div class="form-group">
    <label for="discount_2">Discount 2 </label>
    <input type="number" name="discount_2" class="form-control" value="{{ old('discount_2', $item->discount_2 ?? '') }}" min="0" max="100">
</div>

<div class="form-group">
    <label for="discount_3">Discount 3 </label>
    <input type="number" name="discount_3" class="form-control" value="{{ old('discount_3', $item->discount_3 ?? '') }}" min="0" max="100">
</div>

      <button type="submit" class="btn btn-success btn-lg w-100 fw-semibold shadow-sm">
        Add Item
      </button>
    </form>
  </div>
</div>

{{-- Scripts --}}
<script>
  function toggleCustomUnit() {
    const select = document.getElementById('unit_select');
    const customInput = document.getElementById('unit_input');
    const wrapper = document.getElementById('custom_unit_wrapper');
    const hidden = document.getElementById('unit_hidden');

    if (select.value === 'other') {
      wrapper.style.display = 'block';
      customInput.addEventListener('input', () => {
        hidden.value = customInput.value;
      });
      hidden.value = customInput.value || '';
    } else {
      wrapper.style.display = 'none';
      hidden.value = select.value;
    }
  }

  function toggleCustomCategory() {
  const select = document.getElementById('category_select');
  const customInput = document.getElementById('custom_category');
  const wrapper = document.getElementById('custom_category_wrapper');
  const hidden = document.getElementById('category_hidden');

  if (select.value === 'other') {
    wrapper.style.display = 'block';
    hidden.value = customInput.value;
    customInput.addEventListener('input', () => {
      hidden.value = customInput.value;
    });
  } else {
    wrapper.style.display = 'none';
    customInput.value = '';
    hidden.value = select.value;
  }
}


document.addEventListener('DOMContentLoaded', () => {
  toggleCustomUnit();
  toggleCustomCategory();
});
</script>

{{-- Styles --}}
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

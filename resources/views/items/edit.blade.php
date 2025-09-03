@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow-lg rounded-4 p-4" style="max-width: 480px; width: 100%;">
    <div class="text-center mb-4">
      <h2 class="fw-bold text-primary">Edit Item</h2>
      <p class="text-muted">Update the item details below</p>
    </div>

    <form action="{{ route('items.update', $item) }}" method="POST" novalidate>
      @csrf
      @method('PUT')

      <div class="form-floating mb-4">
        <input type="number" name="item_code" id="item_code" class="form-control form-control-lg" value="{{ $item->item_code }}" required>
        <label for="item_code" class="text-muted">Item Code</label>
      </div>

      <div class="form-floating mb-4">
        <input type="text" name="item_name" id="item_name" class="form-control form-control-lg" value="{{ $item->item_name }}" required>
        <label for="item_name" class="text-muted">Item Name</label>
      </div>

      <div class="form-floating mb-4">
        <input type="number" step="0.01" name="rate" id="rate" class="form-control form-control-lg" value="{{ $item->rate }}" required>
        <label for="rate" class="text-muted">Rate (Rs.)</label>
      </div>

      <div class="form-floating mb-4">
        <input type="number" step="0.01" name="cost_price" id="cost_price" class="form-control form-control-lg" value="{{ $item->cost_price }}" required>
        <label for="cost_price" class="text-muted">Cost Price (Rs.)</label>
      </div>

      <!-- UNIT SELECT -->
      <div class="form-floating mb-4">
        <select name="unit_select" id="unit_select" class="form-select form-select-lg" onchange="toggleCustomUnit()" required>
          <option value="">-- Select Unit --</option>
          @php
            $standardUnits = ['pcs', 'kg', 'g', 'l', 'ml', 'box', 'pack'];
          @endphp
          @foreach($standardUnits as $u)
            <option value="{{ $u }}" {{ $item->unit == $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
          @endforeach
          <option value="other" {{ !in_array($item->unit, $standardUnits) ? 'selected' : '' }}>Other</option>
        </select>
        <label for="unit_select" class="text-muted">Unit</label>
      </div>

      <!-- CUSTOM UNIT -->
      <div class="form-floating mb-4" id="custom_unit_wrapper" style="display: none;">
        <input type="text" id="unit_input" class="form-control form-control-lg" placeholder="Enter custom unit">
        <label for="unit_input" class="text-muted">Custom Unit</label>
      </div>

      <input type="hidden" name="unit" id="unit_hidden">

      <!-- CATEGORY SELECT -->
      <div class="form-floating mb-4">
        <select name="category_select" id="category_select" class="form-select form-select-lg" onchange="toggleCustomCategory()" required>
          <option value="" disabled>Select a category</option>
          @php
            $categories = ['groceries','beverages','fruits','vegetables','dairy','bakery','snacks','household','cleaning','personal_care','stationery','toys','clothing','hardware'];
          @endphp
          @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ $item->category == $cat ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
          @endforeach
          <option value="other" {{ !in_array($item->category, $categories) ? 'selected' : '' }}>Other</option>
        </select>
        <label for="category_select" class="text-muted">Category</label>
      </div>

      <!-- CUSTOM CATEGORY -->
      <div class="form-floating mb-4" id="custom_category_wrapper" style="display: none;">
        <input type="text" id="custom_category" class="form-control form-control-lg" placeholder="Enter custom category">
        <label for="custom_category" class="text-muted">Custom Category</label>
      </div>

      <input type="hidden" name="category" id="category_hidden">

    <!-- STOCK -->
<div class="form-floating mb-4">
  <input type="number" class="form-control form-control-lg" id="stock" name="stock" value="{{ $item->stock }}" readonly>
  <label for="stock" class="text-muted">Stock</label>
</div>

<div class="form-group">
    <label for="discount_1">Discount 1 (%)</label>
    <input type="number" name="discount_1" class="form-control" value="{{ old('discount_1', $item->discount_1 ?? '') }}" min="0" max="100">
</div>

<div class="form-group">
    <label for="discount_2">Discount 2 (%)</label>
    <input type="number" name="discount_2" class="form-control" value="{{ old('discount_2', $item->discount_2 ?? '') }}" min="0" max="100">
</div>

<div class="form-group">
    <label for="discount_3">Discount 3 (%)</label>
    <input type="number" name="discount_3" class="form-control" value="{{ old('discount_3', $item->discount_3 ?? '') }}" min="0" max="100">
</div>

      <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold shadow-sm">
        Update Item
      </button>
    </form>
  </div>
</div>

<!-- SCRIPTS -->
<script>
  function toggleCustomUnit() {
    const select = document.getElementById('unit_select');
    const wrapper = document.getElementById('custom_unit_wrapper');
    const input = document.getElementById('unit_input');
    const hidden = document.getElementById('unit_hidden');

    if (select.value === 'other') {
      wrapper.style.display = 'block';
      input.addEventListener('input', () => {
        hidden.value = input.value;
      });
      hidden.value = input.value;
    } else {
      wrapper.style.display = 'none';
      input.value = '';
      hidden.value = select.value;
    }
  }

  function toggleCustomCategory() {
    const select = document.getElementById('category_select');
    const wrapper = document.getElementById('custom_category_wrapper');
    const input = document.getElementById('custom_category');
    const hidden = document.getElementById('category_hidden');

    if (select.value === 'other') {
      wrapper.style.display = 'block';
      input.addEventListener('input', () => {
        hidden.value = input.value;
      });
      hidden.value = input.value;
    } else {
      wrapper.style.display = 'none';
      input.value = '';
      hidden.value = select.value;
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const currentUnit = "{{ $item->unit }}";
    const currentCategory = "{{ $item->category }}";

    const unitInput = document.getElementById('unit_input');
    const unitHidden = document.getElementById('unit_hidden');

    const categoryInput = document.getElementById('custom_category');
    const categoryHidden = document.getElementById('category_hidden');

    const standardUnits = ['pcs','kg','g','l','ml','box','pack'];
    const standardCategories = {!! json_encode($categories) !!};

    if (!standardUnits.includes(currentUnit)) {
      document.getElementById('unit_select').value = 'other';
      document.getElementById('custom_unit_wrapper').style.display = 'block';
      unitInput.value = currentUnit;
      unitHidden.value = currentUnit;
    } else {
      document.getElementById('unit_select').value = currentUnit;
      unitHidden.value = currentUnit;
    }

    if (!standardCategories.includes(currentCategory)) {
      document.getElementById('category_select').value = 'other';
      document.getElementById('custom_category_wrapper').style.display = 'block';
      categoryInput.value = currentCategory;
      categoryHidden.value = currentCategory;
    } else {
      document.getElementById('category_select').value = currentCategory;
      categoryHidden.value = currentCategory;
    }
  });

    document.querySelector('form').addEventListener('submit', function(e) {
    const unitSelect = document.getElementById('unit_select');
    const unitInput = document.getElementById('unit_input');
    const unitHidden = document.getElementById('unit_hidden');
    
    const categorySelect = document.getElementById('category_select');
    const categoryInput = document.getElementById('custom_category');
    const categoryHidden = document.getElementById('category_hidden');

    if (unitSelect.value === 'other') {
      unitHidden.value = unitInput.value;
    } else {
      unitHidden.value = unitSelect.value;
    }

    if (categorySelect.value === 'other') {
      categoryHidden.value = categoryInput.value;
    } else {
      categoryHidden.value = categorySelect.value;
    }
  });
</script>

<!-- STYLES -->
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
  button.btn-primary:hover {
    background: #2b85d6;
    box-shadow: 0 8px 20px rgba(67, 134, 206, 0.6);
  }
</style>
@endsection

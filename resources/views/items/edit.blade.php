@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Item</h2>
        <form action="{{ route('items.update', $item) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label>Item Code</label>
                <input type="number" name="item_code" class="form-control" value="{{ $item->item_code }}" required>
            </div>
            <div class="mb-3">
                <label>Item Name</label>
                <input type="text" name="item_name" class="form-control" value="{{ $item->item_name }}" required>
            </div>
            <div class="mb-3">
                <label>Rate</label>
                <input type="number" name="rate" class="form-control" value="{{ $item->rate }}" required>
            </div>
             <div class="mb-3">
                <label>Cost Price</label>
                <input type="number" name="cost_price" class="form-control" value="{{ $item->cost_price }}" required>
            </div>
                  <div class="mb-3">
                <label>Sale Price</label>
                <input type="number" name="sale_price" class="form-control" value="{{ $item->sale_price }}" required>
            </div>
                 <div class="mb-3">
                <label>Stock</label>
                <input type="number" name="stock" class="form-control" value="{{ $item->stock }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Item</button>
        </form>
    </div>
@endsection

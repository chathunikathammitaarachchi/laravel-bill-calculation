@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Add New Item</h2>
        <form action="{{ route('items.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Item Code</label>
                <input type="number" name="item_code" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Item Name</label>
                <input type="text" name="item_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Rate</label>
                <input type="number" name="rate" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Stock</label>
                <input type="number" name="stock" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Add Item</button>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Supplier List</h2>
        <a href="{{ route('supplier.create') }}" class="btn btn-primary mb-3">Add New Supplier</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Supplier ID </th>
                    <th>Supplier Name</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
       <tbody>
    @forelse ($suppliers as $supplier)
        <tr>
            <td>{{ $supplier->id }}</td>
            <td>{{ $supplier->supplier_id }}</td>
            <td>{{ $supplier->supplier_name }}</td>
            <td>{{ $supplier->phone }}</td>
            <td>{{ $supplier->address }}</td>
            <td>
                <a href="{{ route('supplier.edit', $supplier) }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ route('supplier.destroy', $supplier) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center">No Supplier found.</td>
        </tr>
    @endforelse
</tbody>

        </table>
    </div>
@endsection

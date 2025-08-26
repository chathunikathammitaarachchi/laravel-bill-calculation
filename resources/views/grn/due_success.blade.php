@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow p-4" style="max-width: 500px; width: 100%;">
        <div class="card-body text-center">
            <h2 class="text-success mb-3">Payment Successful!</h2>
            <p class="text-muted">The customer due has been updated successfully.</p>

            <a href="{{ route('grn.dues') }}" class="btn btn-outline-primary mt-4">Back to Dues</a>
        </div>
    </div>
</div>
@endsection

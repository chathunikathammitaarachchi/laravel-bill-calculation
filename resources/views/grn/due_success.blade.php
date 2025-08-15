@extends('layouts.app')

@section('content')
<div style="text-align: center; margin-top: 50px;">
    <h2>Payment Successful!</h2>
    <p>The customer due has been updated successfully.</p>

    <a href="{{ route('grn.dues') }}" class="btn btn-primary">Back to Dues</a>
</div>
@endsection

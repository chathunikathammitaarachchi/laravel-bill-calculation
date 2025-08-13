@extends('layouts.app')

@section('content')
<div class="container mt-5">

    @if(session('success'))
        <script>alert("{{ session('success') }}");</script>
    @endif

    @if(session('error'))
        <script>alert("{{ session('error') }}");</script>
    @endif

    <div class="card shadow-sm p-4">
        <h3 class="text-center mb-4">Bill Details</h3>

        <div class="row mb-3">
            <div class="col-md-6"><strong>Bill No:</strong> {{ $grn->bill_no }}</div>
            <div class="col-md-6"><strong>Date:</strong> {{ $grn->grn_date }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6"><strong>Customer Name:</strong> {{ $grn->customer_name }}</div>
        </div>

        

        
        <h5 class="mt-5">Items</h5>
        <table class="table table-striped table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Rate</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($grn->details as $item)
                    <tr>
                        <td>{{ $item->item_code }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td style="text-align: right;">{{ number_format($item->rate, 2) }}</td>
                        <td style="text-align: right;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ number_format($item->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
<hr>
        <table class="table table-bordered">
            <tr>
                <th>Total Price</th>
                <td style="text-align: right;">{{ $grn->total_price }}</td>
            </tr>
            <tr>
                <th>Total Discount</th>
                <td style="text-align: right;">{{ $grn->total_discount }}</td>
            </tr>
            <tr>
                <th>Amount to be Paid</th>
                <td style="text-align: right;">{{ $grn->tobe_price }}</td>
            </tr>
            
             <tr>
                <th>Customer Pay</th>
                <td style="text-align: right;">{{ $grn->customer_pay }}</td>
            </tr>
             <tr>
                <th>Balance</th>
                <td style="text-align: right;">{{ $grn->balance }}</td>
            </tr>
        </table>
       
        <hr>
        <div class="row mb-4">
            <div class="col-md-6"><strong>Issued By:</strong> {{ $grn->issued_by }}</div>
        </div>

 <div class="row mb-4">
              <div class="col-md-6"><strong>Received By:</strong> {{ $grn->received_by }}</div>
        </div>
      


        <div class="mt-4 text-end">
            <a href="{{ route('grn.edit', $grn->bill_no) }}" class="btn btn-warning me-2">Edit</a>
            <a href="{{ route('grn.create') }}" class="btn btn-secondary">Back to Search</a>
        </div>
    </div>
</div>
@endsection

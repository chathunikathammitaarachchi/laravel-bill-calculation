@extends('layouts.app')

@section('content')

<div class="container">
    <h3 class="mb-4">GRN Reports</h3>

<form action="{{ route('bill.report') }}" method="GET" class="row g-3 mb-4">


    <div class="col-md-3">
        <label>From Date</label>
        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
    </div>
    <div class="col-md-3">
        <label>To Date</label>
        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
    </div>


    
    <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>



<div class="col-md-3 d-flex align-items-end">
    <a href="{{ route('bill.report.pdf', ['from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" 
       class="btn btn-danger">
        Download PDF
    </a>
</div>
<hr/>

@if(request('from_date') || request('to_date'))
    <div class="mb-3">
        <strong>Showing GRNs from 
            {{ request('from_date') ?? 'beginning' }} 
            to 
            {{ request('to_date') ?? 'now' }}
        </strong>
    </div>
@endif



    @if($bills->count())
        <table class="table table-bordered shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>GRN No</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Total</th>
                    <th>Discount</th>
                    <th>To be Paid</th>
                    <th>Paid</th>
                    <th>Balance</th>
                   
                </tr>
            </thead>
            <tbody>
                @foreach($bills as $bill)
                    <tr>
                        <td>{{ $bill->grn_no }}</td>
                        <td>{{ $bill->g_date }}</td>
                        <td>{{ $bill->supplier_name }}</td>
                        <td style="text-align: right;">{{ number_format($bill->total_price, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($bill->total_discount, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($bill->tobe_price, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($bill->supplier_pay, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($bill->balance, 2) }}</td>
                        
                    </tr>
                @endforeach
            </tbody>
        </table> 
    @else
        <div class="alert alert-warning">No Bills found for the selected date.</div>
    @endif
</div>

@endsection

@extends('layouts.app')

@section('content')
<div style="
    max-width: 900px; 
    margin: 40px auto; 
    background: #ffffff; 
    padding: 40px 50px; 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    border-radius: 10px; 
    border: 1px solid #ddd; 
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
">

    <div style="text-align: center; border-bottom: 4px solid #5686bd; margin-bottom: 30px; padding-bottom: 15px;">
        <h2 style="margin: 0; font-size: 30px; font-weight: 700; color: #2c3e50;">Bill No: {{ $bill->grn_no }}</h2>
        <p style="margin: 5px 0; font-size: 16px; color: #7f8c8d;"><strong>Date:</strong> {{ $bill->g_date }}</p>
        <p style="margin: 5px 0; font-size: 16px; color: #7f8c8d;"><strong>Customer Name:</strong> {{ $bill->supplier_name }}</p>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 15px;" border="1">
        <thead style="background-color: #6a90bb; color: white;">
            <tr>
                <th style="padding: 14px; text-align: center;">Item Code</th>
                <th style="padding: 14px; text-align: center;">Name</th>
                <th style="padding: 14px; text-align: center;">Rate</th>
                <th style="padding: 14px; text-align: center;">Quantity</th>
                <th style="padding: 14px; text-align: center;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bill->details as $detail)
            <tr style="background-color: {{ $loop->even ? '#f9f9f9' : '#ffffff' }};">
                <td style="padding: 12px; text-align: center; color: #2d3436;">{{ $detail->item_code }}</td>
                <td style="padding: 12px; text-align: center; color: #2d3436;">{{ $detail->item_name }}</td>
                <td style="padding: 12px; text-align: center; color: #2d3436;">{{ number_format($detail->rate, 2) }}</td>
                <td style="padding: 12px; text-align: center; color: #2d3436;">{{ $detail->quantity }}</td>
                <td style="padding: 12px; text-align: center; color: #2d3436;">{{ number_format($detail->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="
        margin-top: 35px; 
        border-top: 2px solid #7e9fc5; 
        padding-top: 20px; 
        font-size: 16px; 
        color: #2c3e50; 
        max-width: 450px; 
        margin-left: auto;
    ">
        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
            <strong>Total Price:</strong> 
            <span>{{ number_format($bill->total_price, 2) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
            <strong>Discount:</strong> 
            <span>{{ number_format($bill->total_discount, 2) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
            <strong>To Be Paid:</strong> 
            <span>{{ number_format($bill->tobe_price, 2) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
            <strong>Supplier Pay:</strong> 
            <span>{{ number_format($bill->supplier_pay, 2) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
            <strong>Balance:</strong> 
            <span>{{ number_format($bill->balance, 2) }}</span>
        </div>
    </div>

    <a href="{{ route('bill.create') }}" style="
        display: inline-block; 
        margin-top: 30px; 
        background-color: #3498db; 
        color: white; 
        padding: 12px 25px; 
        font-size: 16px; 
        font-weight: 600; 
        text-decoration: none; 
        border-radius: 5px; 
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    " 
    onmouseover="this.style.backgroundColor='#2980b9'" 
    onmouseout="this.style.backgroundColor='#3498db'">
        Back To Home
    </a>

</div>
@endsection
+

+








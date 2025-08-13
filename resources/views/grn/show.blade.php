@extends('layouts.app')

@section('content')
<div style="
    max-width: 800px; 
    margin: 40px auto; 
    background: #fff; 
    padding: 30px 40px; 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    border-radius: 8px; 
    border: 1px solid #ddd; 
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
">
    <div style="text-align: center; border-bottom: 3px solid #5686bdff; margin-bottom: 25px; padding-bottom: 15px;">
        <h2 style="margin: 0; font-size: 28px; font-weight: 700; color: #333;">Bill No: {{ $grn->bill_no }}</h2>
        <p style="margin: 4px 0; font-size: 16px; color: #666;"><strong>Date:</strong> {{ $grn->grn_date }}</p>
        <p style="margin: 4px 0; font-size: 16px; color: #666;"><strong>Customer Name:</strong> {{ $grn->customer_name }}</p>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 16px;" border="1">
        <thead style="background-color: #6a90bbff; color: white;">
            <tr>
                <th style="padding: 12px; text-align: center;">Item Code</th>
                <th style="padding: 12px; text-align: center;">Name</th>
                <th style="padding: 12px; text-align: center;">Rate</th>
                <th style="padding: 12px; text-align: center;">Quantity</th>
                <th style="padding: 12px; text-align: center;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grn->details as $detail)
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 12px; text-align: center; color: #555;">{{ $detail->item_code }}</td>
                <td style="padding: 12px; text-align: center; color: #555;">{{ $detail->item_name }}</td>
                <td style="padding: 12px; text-align: center; color: #555;">{{ number_format($detail->rate, 2) }}</td>
                <td style="padding: 12px; text-align: center; color: #555;">{{ $detail->quantity }}</td>
                <td style="padding: 12px; text-align: center; color: #555;">{{ number_format($detail->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

   <div style="margin-top: 30px; border-top: 2px solid #7e9fc5ff; padding-top: 20px; font-size: 16px; color: #333; max-width: 400px; margin-left: auto;">
  <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
    <strong>Total Price:</strong> 
    <span>{{ number_format($grn->total_price, 2) }}</span>
  </div>
  <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
    <strong>Discount:</strong> 
    <span>{{ number_format($grn->total_discount, 2) }}</span>
  </div>
  <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
    <strong>To Be Paid:</strong> 
    <span>{{ number_format($grn->tobe_price, 2) }}</span>
  </div>
  <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
    <strong>Customer Pay:</strong> 
    <span>{{ number_format($grn->customer_pay, 2) }}</span>
  </div>
  <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
    <strong>Balance:</strong> 
    <span>{{ number_format($grn->balance, 2) }}</span>
  </div>
  <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
    <strong>Received By:</strong> 
    <span>{{ $grn->received_by }}</span>
  </div>
  <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
    <strong>Issued By:</strong> 
    <span>{{ $grn->issued_by }}</span>
  </div>
</div>

    <a href="{{ route('grn.create') }}" style="
        display: inline-block; 
        margin-top: 25px; 
        background-color: #f39c12; 
        color: white; 
        padding: 12px 25px; 
        font-size: 16px; 
        font-weight: 600; 
        text-decoration: none; 
        border-radius: 5px; 
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        transition: background-color 0.3s ease;
    " onmouseover="this.style.backgroundColor='#d68910'" onmouseout="this.style.backgroundColor='#f39c12'">
        Back To Home
    </a>
</div>
@endsection

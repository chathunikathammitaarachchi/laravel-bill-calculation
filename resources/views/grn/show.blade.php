@extends('layouts.app')

@section('content')
<div style="
    max-width: 900px;
    margin: 50px auto;
    background: #fefefe;
    padding: 40px 50px;
    font-family: 'Segoe UI', sans-serif;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
">

    <!-- Header -->
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="font-size: 32px; font-weight: bold; color: #2c3e50; margin-bottom: 10px;">Bill Details</h1>
        <p style="font-size: 18px; color: #34495e; margin: 4px 0;">
            <strong>Bill No:</strong> {{ $grn->bill_no }}
        </p>
        <p style="font-size: 16px; color: #555;">
            <strong>Date:</strong> {{ $grn->grn_date }} |
            <strong>Customer:</strong> {{ $grn->customer_name }}
        </p>
    </div>

    <!-- Items Table -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 15px;">
        <thead>
            <tr style="background-color: #3c8dbc; color: white;">
                <th style="padding: 12px; border-right: 1px solid #fff;">Item Code</th>
                <th style="padding: 12px; border-right: 1px solid #fff;">Name</th>
                <th style="padding: 12px; text-align: right; border-right: 1px solid #fff;">Rate</th>
                <th style="padding: 12px; text-align: right; border-right: 1px solid #fff;">Qty</th>
                <th style="padding: 12px; text-align: right;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grn->details as $detail)
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 10px; text-align: center;">{{ $detail->item_code }}</td>
                <td style="padding: 10px; text-align: center;">{{ $detail->item_name }}</td>
                <td style="padding: 10px; text-align: right;">{{ number_format($detail->rate, 2) }}</td>
                <td style="padding: 10px; text-align: right;">{{ $detail->quantity }}</td>
                <td style="padding: 10px; text-align: right;">{{ number_format($detail->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <div style="
        max-width: 450px;
        margin-left: auto;
        font-size: 16px;
        color: #2d3436;
        border-top: 2px solid #3c8dbc;
        padding-top: 20px;
    ">
        @php
            $summary = [
                'Total Price' => number_format($grn->total_price, 2),
                'Discount' => number_format($grn->total_discount, 2),
                'To Be Paid' => number_format($grn->tobe_price, 2),
                'Customer Pay' => number_format($grn->customer_pay, 2),
                'Balance' => number_format($grn->balance, 2),
                'Received By' => $grn->received_by,
                'Issued By' => $grn->issued_by,
            ];
        @endphp

        @foreach($summary as $label => $value)
        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #ccc;">
            <span style="font-weight: 500;">{{ $label }}:</span>
            <span style="font-weight: 600;">{{ $value }}</span>
        </div>
        @endforeach
    </div>

    <!-- Action Button -->
    <div style="text-align: center; margin-top: 40px;">
        <a href="{{ route('grn.create') }}" style="
            background-color: #3c8dbc;
            color: white;
            padding: 14px 35px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        "
        onmouseover="this.style.backgroundColor='#2f6fa4'"
        onmouseout="this.style.backgroundColor='#3c8dbc'">
            ← Back to Menu
        </a>
    </div>
</div>
@endsection

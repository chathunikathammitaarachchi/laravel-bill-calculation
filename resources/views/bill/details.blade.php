@extends('layouts.app')

@section('content')
<div class="container" style="background-color: #1e1e2f; color: #ffffff; padding: 30px; border-radius: 10px;">
    <h3 class="mb-4" style="color: #ffffff; border-bottom: 2px solid #444; padding-bottom: 10px;">
        GRN Details for {{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}
    </h3>

    @if($grns->isEmpty())
        <div style="background-color: #ffc107; color: #000; padding: 10px; border-radius: 5px;">
            No GRNs found for this date.
        </div>
    @else
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #343a40; color: #ffffff;">
                    <th style="padding: 12px; border: 1px solid #555;">GRN No</th>
                    <th style="padding: 12px; border: 1px solid #555;">Supplier</th>
                    <th style="padding: 12px; border: 1px solid #555;">Total Price</th>
                    <th style="padding: 12px; border: 1px solid #555;">Total Discount</th>
                    <th style="padding: 12px; border: 1px solid #555;">Total Issued</th>
                    <th style="padding: 12px; border: 1px solid #555;">Paid</th>
                    <th style="padding: 12px; border: 1px solid #555;">Balance</th>
                </tr>
            </thead>
            <tbody>
    @foreach($grns as $grn)
        <tr style="background-color: #2c2f3a;">
            <td style="padding: 10px; border: 1px solid #444;">{{ $grn->grn_no }}</td>
            <td style="padding: 10px; border: 1px solid #444;">{{ $grn->supplier_name }}</td>
            <td style="padding: 10px; border: 1px solid #444;">Rs {{ number_format($grn->total_price, 2) }}</td>
            <td style="padding: 10px; border: 1px solid #444;">Rs {{ number_format($grn->total_discount, 2) }}</td>
            <td style="padding: 10px; border: 1px solid #444;">Rs {{ number_format($grn->tobe_price, 2) }}</td>
            <td style="padding: 10px; border: 1px solid #444;">Rs {{ number_format($grn->supplier_pay, 2) }}</td>
            <td style="padding: 10px; border: 1px solid #444;">Rs {{ number_format($grn->balance, 2) }}</td>
        </tr>
    @endforeach

    {{-- Total row --}}
    <tr style="background-color: #3a3f51; font-weight: bold;">
        <td colspan="2" style="padding: 10px; border: 1px solid #444;">Total</td>
        <td style="padding: 10px; border: 1px solid #444;">Rs {{ number_format($totals['total_price'], 2) }}</td>
        <td style="padding: 10px; border: 1px solid #444;">Rs {{ number_format($totals['total_discount'], 2) }}</td>
        <td style="padding: 10px; border: 1px solid #444;">Rs {{ number_format($totals['total_issued'], 2) }}</td>
        <td style="padding: 10px; border: 1px solid #444;">Rs {{ number_format($totals['total_paid'], 2) }}</td>
        <td style="padding: 10px; border: 1px solid #444;">Rs {{ number_format($totals['total_balance'], 2) }}</td>
    </tr>
</tbody>

        </table>
    @endif
<a href="{{ route('grn.details.pdf', ['date' => $date]) }}"
   style="display: inline-block; margin-top: 20px; margin-left: 10px; padding: 10px 20px; background-color: #28a745; color: #ffffff; text-decoration: none; border-radius: 5px;">
   ⬇ Download PDF
</a>

    <a href="{{ route('bill.summary') }}"
       style="display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #6c757d; color: #ffffff; text-decoration: none; border-radius: 5px;">
       ← Back to Summary
    </a>
</div>
@endsection

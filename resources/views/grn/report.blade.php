@extends('layouts.app')

@section('content')

<div class="container">
    <h3 class="mb-4" style="font-weight: 700; color: #ffffffff; border-bottom: 2px solid #3490dc; padding-bottom: 10px;">Bill Reports</h3>

   <form action="{{ route('grn.report') }}" method="GET" class="row g-3 mb-4">
   <style>
    .white-label {
        color: white;
    }
</style>

<div class="col-md-3">
    <label class="white-label">From Date</label>
    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
</div>
<div class="col-md-3">
    <label class="white-label">To Date</label>
    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
</div>



    
    <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>



<div class="col-md-3 d-flex align-items-end">
    <a href="{{ route('grn.report.pdf', ['from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" 
       target="pdfFrame"
       class="btn btn-danger">
        Download & Print PDF
    </a>

    <iframe id="pdfFrame" name="pdfFrame" style="display:none;" onload="printIframe()"></iframe>

    <script>
        function printIframe() {
            const iframe = document.getElementById('pdfFrame');
            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }
    </script>
</div>

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



    @if($grns->count())
        <table class="table table-bordered shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Bill No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Discount</th>
                    <th>To be Paid</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Issued By</th>
                    <th>Received By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grns as $grn)
                    <tr>
                        <td>{{ $grn->bill_no }}</td>
                        <td>{{ $grn->grn_date }}</td>
                        <td>{{ $grn->customer_name }}</td>
                        <td style="text-align: right;">{{ number_format($grn->total_price, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($grn->total_discount, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($grn->tobe_price, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($grn->customer_pay, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($grn->balance, 2) }}</td>
                        <td>{{ $grn->issued_by }}</td>
                        <td>{{ $grn->received_by }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-warning">No Bills found for the selected date.</div>
    @endif
</div>

@endsection

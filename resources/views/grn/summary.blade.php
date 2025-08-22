@extends('layouts.app')

@section('content')

<div class="container">
    <h3  style="color: white;">🧾 Bill Reports</h3>

    <form action="{{ route('grn.summary') }}" method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label  style="color: white;">From Date</label>
            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>
        <div class="col-md-3">
            <label  style="color: white;">To Date</label>
            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>
        <div class="col-md-3">
            <label  style="color: white;">Customer Name</label>
            <input type="text" name="customer_name" class="form-control" value="{{ request('customer_name') }}" placeholder="Enter customer name">
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('grn.summary') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="mb-3">
        <a href="{{ route('grn.summary.pdf', ['from_date' => request('from_date'), 'to_date' => request('to_date'), 'customer_name' => request('customer_name')]) }}" 
           class="btn btn-danger">
            Download PDF
        </a>
    </div>

    @if(request('from_date') || request('to_date') || request('customer_name'))
        <div class="alert alert-info">
            <strong>Filter:</strong> 
            From <strong>{{ request('from_date') ?? 'Beginning' }}</strong> 
            to <strong>{{ request('to_date') ?? 'Now' }}</strong>, 
            Customer: <strong>{{ request('customer_name') ?? 'All' }}</strong>
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
                @if(isset($customerName, $fromDate))
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="6"><strong>Opening Balance (before {{ $fromDate }})</strong></td>
                        <td class="text-end"><strong>{{ number_format($openingBalance, 2) }}</strong></td>
                        <td colspan="3"></td>
                    </tr>
                @endif

                @foreach($grns as $grn)
                    <tr>
                        <td>{{ $grn->bill_no }}</td>
                        <td>{{ $grn->grn_date }}</td>
                        <td>{{ $grn->customer_name }}</td>
                        <td class="text-end">{{ number_format($grn->total_price, 2) }}</td>
                        <td class="text-end">{{ number_format($grn->total_discount, 2) }}</td>
                        <td class="text-end">{{ number_format($grn->tobe_price, 2) }}</td>
                        <td class="text-end">{{ number_format($grn->customer_pay, 2) }}</td>
                        <td class="text-end">{{ number_format($grn->balance, 2) }}</td>
                        <td>{{ $grn->issued_by }}</td>
                        <td>{{ $grn->received_by }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot class="table-secondary">
                <tr>
                    <td colspan="3"><strong>Totals</strong></td>
                    <td class="text-end"><strong>{{ number_format($totalTotal, 2) }}</strong></td>
                    <td class="text-end"><strong>{{ number_format($totalDiscount, 2) }}</strong></td>
                    <td class="text-end"><strong>{{ number_format($totalToBePaid, 2) }}</strong></td>
                    <td class="text-end"><strong>{{ number_format($totalPaid, 2) }}</strong></td>
                    <td class="text-end"><strong>{{ number_format($totalBalance, 2) }}</strong></td>
                    <td colspan="2"></td>
                </tr>

                @if(isset($customerName, $fromDate))
                    <tr class="table-warning">
                        <td colspan="5"><strong>Grand To be Paid (Opening + Range)</strong></td>
                        <td class="text-end">
                            <strong>{{ number_format($openingBalance + $totalToBePaid, 2) }}</strong>
                        </td>
                        <td colspan="4"></td>
                    </tr>
                @endif
            </tfoot>
        </table>
    @else
        <div class="alert alert-warning">No Bills found for the selected filters.</div>
    @endif
</div>

@endsection

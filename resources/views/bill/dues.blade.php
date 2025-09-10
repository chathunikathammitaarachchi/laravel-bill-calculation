@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1550px; margin-top: 40px;">
    <h2 class="mb-4" style="font-weight: 700; color: #ffffffff; border-bottom: 2px solid #3490dc; padding-bottom: 10px;">
        Supplier Dues
    </h2>

    {{-- Filter Form --}}
   <form method="GET" action="{{ route('bill.dues') }}" class="row g-3 mb-4" style="background: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <div class="col-md-3">
        <label for="from_date" class="form-label" style="font-weight: 600;">From Date</label>
        <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" class="form-control" style="border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div class="col-md-3">
        <label for="to_date" class="form-label" style="font-weight: 600;">To Date</label>
        <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" class="form-control" style="border: 1px solid #ccc; border-radius: 4px;">
    </div>
    <div class="col-md-3">
        <label for="supplier_name" class="form-label" style="font-weight: 600;">Supplier Name</label>
        <input list="suppliers" name="supplier_name" id="supplier_name" value="{{ request('supplier_name') }}" class="form-control" placeholder="Type supplier name..." style="border: 1px solid #ccc; border-radius: 4px;">
        <datalist id="suppliers">
            @foreach(\App\Models\SupplierDue::select('supplier_name')->distinct()->orderBy('supplier_name')->get() as $supplier)
                <option value="{{ $supplier->supplier_name }}">
            @endforeach
        </datalist>
    </div>
    <div class="col-md-3 d-flex align-items-end" style="gap: 10px;">
        <button type="submit" class="btn btn-primary" style="flex: 1;">Filter</button>
        <a href="{{ route('bill.dues') }}" class="btn btn-secondary" style="flex: 1;">Reset</a>

        @if(count($dues) > 0)
            <a href="{{ route('bill.dues.export', request()->only(['from_date', 'to_date', 'supplier_name'])) }}" class="btn btn-success" style="flex: 5;">Download PDF</a>
        @endif
    </div>
</form>
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Table Section --}}
@if($dues->isEmpty())
    <p style="font-style: italic; color: #888;">No dues available for selected date range.</p>
@else
    <div class="table-responsive" style="box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;">
        <table class="table table-bordered table-striped mb-0">
            <thead class="table-dark" style="background-color: #2c3e50; color: #fff;">
                <tr>
                    <th>Supplier Name</th>
                    <th>Due Amount</th>
                    <th>Paid</th>
                    <th>To Be Paid</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Cheque Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dues as $due)
                    <tr>
                        <td><strong>{{ $due->supplier_name }}</strong></td>
                        <td class="text-end">{{ number_format($due->total_due, 2) }}</td>
                        <td class="text-end">{{ number_format($due->total_paid, 2) }}</td>
                        <td class="text-end">{{ number_format($due->total_balance, 2) }}</td>
                        <td>{{ $due->last_date }}</td>
                        <td>
                            @if ($due->total_balance > 0)
                                <span class="badge bg-warning text-dark fw-bold">Pending</span>
                            @else
                                <span class="badge bg-success fw-bold">Settled</span>
                            @endif
                        </td>
                        <td>
                            @if ($due->has_cheque_returned)
                                <span class="badge bg-danger fw-bold">Cheque Returned</span>
                            @elseif ($due->total_balance > 0)
                                <span class="badge bg-warning text-dark fw-bold">Pending</span>
                            @else
                                <span class="badge bg-success fw-bold">Settled</span>
                            @endif
                        </td>
                        <td>
                            @if ($due->total_balance > 0)
                                <a href="{{ route('due_payments.form.by.supplier', ['supplier_name' => $due->supplier_name]) }}" class="btn btn-sm btn-primary">
                                    Pay
                                </a>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled>Paid</button>
                            @endif
                        </td>
                    </tr>

                    {{-- Inline Returned Cheques --}}
                    @if(isset($returnedCheques[$due->supplier_name]))
                        @foreach($returnedCheques[$due->supplier_name] as $cheque)
                            <tr style="background-color: #f8d7da;">
                                <td colspan="2"><strong>Returned Cheque</strong></td>
                                <td colspan="2">Cheque No: {{ $cheque->cheque_number }}</td>
                                <td>{{ $cheque->bank_name }} / {{ $cheque->branch_name }}</td>
                                <td colspan="3" class="text-end">Rs. {{ number_format($cheque->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
        <br/>
        <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">Back</a>

    </div>
@endif

<script>
$(document).ready(function() {
    $('#supplier_name').select2({
        placeholder: 'Search supplier name...',
        minimumInputLength: 1,
        ajax: {
            url: '{{ route("suppliers.autocomplete") }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term // search term
                };
            },
            processResults: function(data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.supplier_name,
                            text: item.supplier_name
                        };
                    })
                };
            },
            cache: true
        }
    });
});


</script>

@endsection

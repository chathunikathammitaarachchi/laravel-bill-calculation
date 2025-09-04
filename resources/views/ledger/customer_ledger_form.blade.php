@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 600px; margin-top: 50px; padding: 30px; background: #f8f9fa; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    <h4 style="text-align: center; margin-bottom: 25px; color: #343a40;">Customer Ledger - Search</h4>
    <form method="GET" action="{{ route('customer.ledger') }}">
        <div class="mb-3" style="position: relative;">
            <label for="customer_display" class="form-label">Customer Name or ID</label>
            <input type="text" id="customer_display" class="form-control" placeholder="Type customer ID or name" autocomplete="off"
                value="{{ old('customer_display', isset($selectedCustomer) ? $selectedCustomer->customer_id . ' - ' . $selectedCustomer->customer_name : '') }}">
            <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id', request('customer_id')) }}">
            <div id="customerList" class="list-group mt-1" style="position: absolute; z-index: 9999; width: 100%; max-height: 200px; overflow-y: auto;"></div>
            @error('customer_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
            @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <div class="mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
            @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">View Ledger</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('#customer_display').on('keyup', function () {
        let query = $(this).val().trim();
        $('#customer_id').val('');
        if (query.length >= 1) {
            $.ajax({
                url: "{{ route('customer.search') }}",
                method: "GET",
                data: { query: query },
                success: function (data) {
                    $('#customerList').fadeIn().html('');
                    if (data.length === 0) {
                        $('#customerList').append('<a class="list-group-item disabled">No results found</a>');
                    } else {
                        data.forEach(customer => {
                            $('#customerList').append(`
                                <a href="#" class="list-group-item list-group-item-action"
                                   data-id="${customer.id}"
                                   data-name="${customer.customer_name}"
                                   data-customerid="${customer.customer_id}">
                                   ${customer.customer_id} - ${customer.customer_name}
                                </a>
                            `);
                        });
                    }
                }
            });
        } else {
            $('#customerList').fadeOut();
        }
    });

    $(document).on('click', '#customerList a', function (e) {
        e.preventDefault();
        let selectedId = $(this).data('id');
        let selectedName = $(this).data('name');
        let selectedCustomerId = $(this).data('customerid');
        $('#customer_display').val(`${selectedCustomerId} - ${selectedName}`);
        $('#customer_id').val(selectedId);
        $('#customerList').fadeOut();
    });

    $(document).click(function (e) {
        if (!$(e.target).closest('#customer_display, #customerList').length) {
            $('#customerList').fadeOut();
        }
    });

    $('form').on('submit', function(e) {
        if ($('#customer_id').val() === '') {
            e.preventDefault();
            alert('Please select a valid customer from the list.');
            $('#customer_display').focus();
        }
    });
});
</script>
@endsection

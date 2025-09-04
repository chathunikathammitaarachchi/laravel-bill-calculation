@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 600px; margin-top: 50px; padding: 30px; background: #f8f9fa; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
    <h4 style="text-align: center; margin-bottom: 25px; color: #343a40;">Supplier Ledger - Search</h4>
    <form method="GET" action="{{ route('supplier.ledger') }}">
        <div class="mb-3" style="position: relative;">
            <label for="supplier_display" class="form-label">Supplier Name or ID</label>
            <input type="text" id="supplier_display" class="form-control" placeholder="Type supplier ID or name" autocomplete="off"
                value="{{ old('supplier_display', isset($selectedSupplier) ? $selectedSupplier->id . ' - ' . $selectedSupplier->supplier_name : '') }}">
            <input type="hidden" name="supplier_id" id="supplier_id" value="{{ old('supplier_id', request('supplier_id')) }}">
            <div id="supplierList" class="list-group mt-1" style="position: absolute; z-index: 9999; width: 100%; max-height: 200px; overflow-y: auto;"></div>
            @error('supplier_id') <small class="text-danger">{{ $message }}</small> @enderror
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
    $('#supplier_display').on('keyup', function () {
        let query = $(this).val().trim();
        $('#supplier_id').val('');
        if (query.length >= 1) {
            $.ajax({
                url: "{{ route('supplier.search') }}",
                method: "GET",
                data: { query: query },
                success: function (data) {
                    $('#supplierList').fadeIn().html('');
                    if (data.length === 0) {
                        $('#supplierList').append('<a class="list-group-item disabled">No results found</a>');
                    } else {
                        data.forEach(supplier => {
                            $('#supplierList').append(`
                                <a href="#" class="list-group-item list-group-item-action" 
                                   data-id="${supplier.id}" 
                                   data-name="${supplier.supplier_name}">
                                   ${supplier.id} - ${supplier.supplier_name}
                                </a>
                            `);
                        });
                    }
                }
            });
        } else {
            $('#supplierList').fadeOut();
        }
    });

    $(document).on('click', '#supplierList a', function (e) {
        e.preventDefault();
        let selectedId = $(this).data('id');
        let selectedName = $(this).data('name');
        $('#supplier_display').val(`${selectedId} - ${selectedName}`);
        $('#supplier_id').val(selectedId);
        $('#supplierList').fadeOut();
    });

    $(document).click(function (e) {
        if (!$(e.target).closest('#supplier_display, #supplierList').length) {
            $('#supplierList').fadeOut();
        }
    });

    $('form').on('submit', function(e) {
        if ($('#supplier_id').val() === '') {
            e.preventDefault();
            alert('Please select a valid supplier from the list.');
            $('#supplier_display').focus();
        }
    });
});
</script>
@endsection

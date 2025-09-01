@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold fs-1">📦 Daily Item Summary</h2>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('itemsummary') }}" class="card p-4 mb-5 border-0 bg-light">
        <div class="row g-4 align-items-end">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="form-control" />
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="form-control" />
            </div>
            <div class="col-md-6 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">
                    🔍 Filter
                </button>

                <a href="{{ route('itemsummary') }}" class="btn btn-outline-primary">
                    🔄 Reset
                </a>

           <a href="{{ route('itemsummary.pdf', request()->all()) }}" target="pdfFrame" class="btn btn-success">
    📄 Download & Print PDF
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
    </form>

    {{-- Alerts --}}
    @if($maxItem)
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-arrow-up-circle-fill"></i>
            Highest OUT: <strong>{{ $minItem['item_code'] }}</strong> — {{ $minItem['item_name'] }} ({{ $minItem['quantity_total'] }})
        </div>
    @endif

    @if($minItem)
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-arrow-down-circle-fill"></i>
            Lowest OUT: <strong>{{ $maxItem['item_code'] }}</strong> — {{ $maxItem['item_name'] }} ({{ $maxItem['quantity_total'] }})
        </div>
    @endif

    {{-- Table / No data --}}
    @if(empty($groupedByDate))
        <div class="alert alert-warning text-center">
            🚫 No data found for the selected date range.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>📅 Date</th>
                        <th>🔢 Item Code</th>
                        <th>📦 Item Name</th>
                        <th class="text-end">📤 Quantity OUT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedByDate as $date => $items)
                        @php $itemCount = count($items); @endphp
                        @foreach($items as $code => $data)
                            <tr>
                                @if ($loop->first)
                                    <td rowspan="{{ $itemCount }}" class="align-middle">
                                        {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}
                                    </td>
                                @endif
                                <td>{{ $code }}</td>
                                <td class="text-start">{{ $data['item_name'] }}</td>
                                <td class="text-end text-danger fw-bold">{{ $data['quantity_total'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

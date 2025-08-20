@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold fs-1">📦 Daily Item Summary</h2>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('itemsummary') }}" class="card shadow-lg p-4 mb-5 border-0 bg-white rounded-4">
        <div class="row g-4 align-items-end">
            <div class="col-md-3">
                <label for="start_date" class="form-label fw-semibold text-secondary">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="form-control border-primary shadow-sm" />
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label fw-semibold text-secondary">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="form-control border-primary shadow-sm" />
            </div>
            <div class="col-md-6 d-flex gap-3 justify-content-md-start">
                <button type="submit" class="btn btn-primary px-4 shadow-sm fw-semibold">
                    🔍 Filter
                </button>
<a href="{{ route('itemsummary') }}" class="reset-button">
  🔄 Reset
</a>

                <a href="{{ route('itemsummary.pdf', request()->all()) }}" target="_blank" class="btn btn-success px-4 shadow-sm fw-semibold">
                    📄 Download PDF
                </a>
            </div>
        </div>
    </form>

    @if($maxItem)
        <div class="alert alert-success rounded-4 shadow-sm d-flex align-items-center gap-3 mb-4 fs-5">
            <i class="bi bi-arrow-up-circle-fill"></i>
            Highest OUT: <strong>{{ $maxItem['item_code'] }}</strong> - {{ $maxItem['item_name'] }} ({{ $maxItem['quantity_total'] }})
        </div>
    @endif

    @if($minItem)
        <div class="alert alert-warning rounded-4 shadow-sm d-flex align-items-center gap-3 mb-4 fs-5">
            <i class="bi bi-arrow-down-circle-fill"></i>
            Lowest OUT: <strong>{{ $minItem['item_code'] }}</strong> - {{ $minItem['item_name'] }} ({{ $minItem['quantity_total'] }})
        </div>
    @endif

    {{-- Table / No data --}}
    @if(empty($groupedByDate))
        <div class="alert alert-warning text-center fs-5 py-4 rounded-4 shadow-sm">
            🚫 No data found for the selected date range.
        </div>
    @else
        <div class="table-responsive shadow-lg rounded-4">
            <table class="table table-bordered table-hover align-middle mb-0" style="min-width: 00px;">
                <thead class="table-primary text-center fs-6">
                    <tr>
                        <th class="fw-bold">Date</th>
                        <th class="fw-bold">Item Code</th>
                        <th class="fw-bold">Item Name</th>
                        <th class="fw-bold text-end">Quantity OUT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedByDate as $date => $items)
                        @php $itemCount = count($items); @endphp
                        @foreach($items as $code => $data)
                            <tr>
                                @if ($loop->first)
                                    <td rowspan="{{ $itemCount }}" class="text-center fw-semibold bg-light text-primary" style="vertical-align: middle;">
                                        {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}
                                    </td>
                                @endif
                                <td class="fw-semibold">{{ $code }}</td>
                                <td>{{ $data['item_name'] }}</td>
                                <td class="text-end fw-bold text-danger">{{ $data['quantity_total'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<style>
.reset-button {
  display: inline-block;
  padding: 0.6rem 1.2rem; /* großzügig padding */
  font-size: 1rem;
  color: #007bff; /* Bootstrap primary blue */
  background-color: transparent;
  border: 2px solid #007bff;
  border-radius: 0.375rem; /* 6px — zachte hoek */
  text-decoration: none;
  font-weight: 600;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;
}

.reset-button:hover,
.reset-button:focus {
  background-color: #007bff;
  color: #fff;
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.reset-button:active {
  background-color: #0056b3; /* slightly darker */
  border-color: #0056b3;
}

</style>
@endsection

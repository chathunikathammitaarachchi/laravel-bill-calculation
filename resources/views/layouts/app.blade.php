<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Bill System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
    /* Base Reset */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html, body {
        height: 100%;
        overflow-x: hidden;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f1f3f5;
    }

    body {
        display: flex;
        flex-direction: row;
        min-height: 100vh;
    }

    /* Sidebar Styling */
   .sidebar {
    width: 250px;
    min-height: 100vh; 
    height: 100%; 
    background: linear-gradient(135deg, #0d6efd, #0a58ca);
    padding-top: 1rem;
    color: #fff;
    flex-shrink: 0;
}


    .sidebar a {
        color: #ffffffcc;
        padding: 12px 20px;
        display: block;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease-in-out;
    }

    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
        background-color: rgba(255, 247, 247, 0.1);
        color: #fff !important;
        font-weight: bold;
        border-left: 4px solid #fff;
    }

    .sidebar h4 a {
        color: #fff;
        font-weight: 700;
        text-transform: uppercase;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
    }

    /* Content Area */
    .content {
        flex: 1;
        padding: 2rem;
    }

    /* Scrollbar Styling (applies to whole page) */
    body::-webkit-scrollbar {
        width: 8px;
    }

    body::-webkit-scrollbar-track {
        background: transparent;
    }

    body::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
    }

    /* Responsive Layout */
    @media (max-width: 768px) {
        body {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
        }

        .content {
            padding: 1rem;
        }
    }
</style>

</head>

<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <h4 class="text-center mb-4">
            <a href="{{ route('home') }}">Bill System</a>
        </h4>

       <!-- Sidebar Navigation -->
<ul class="nav flex-column">

    {{-- Supplier --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('supplier.index') ? 'active' : '' }}" href="{{ route('supplier.index') }}">
            <i class="bi bi-person-badge"></i> Supplier
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('bill.create') ? 'active' : '' }}" href="{{ route('bill.create') }}">
            <i class="bi bi-file-earmark-plus"></i> Supplier GRN Order
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('bill.report') ? 'active' : '' }}" href="{{ route('bill.report') }}">
            <i class="bi bi-file-earmark-text"></i> Supplier Report
        </a>
    </li>


    <li class="nav-item">
    <a class="nav-link" href="{{ route('bill.summary') }}">
        <i class="bi bi-list"></i>
        GRN Summary
    </a>
</li>

    {{-- Item --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('items.index') ? 'active' : '' }}" href="{{ route('items.index') }}">
            <i class="bi bi-box-seam"></i> Items
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('item_summaries.index') ? 'active' : '' }}" href="{{ route('item_summaries.index') }}">
            <i class="bi bi-file-earmark-bar-graph"></i> Item Summary
        </a>
    </li>

    {{-- Customer --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.index') ? 'active' : '' }}" href="{{ route('customer.index') }}">
            <i class="bi bi-people"></i> Customer
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/">
            <i class="bi bi-receipt"></i> Customer Bill
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('grn.dues') ? 'active' : '' }}" href="{{ route('grn.dues') }}">
            <i class="bi bi-cash-coin"></i> Customer Dues
        </a>
    </li>

         <li class="nav-item">
<a class="nav-link" href="{{ route('customer.ledger') }}">            
<i class="bi bi-journal-text"></i> Customer Ledger
        </a>
    </li>

    {{-- Reports --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('grn.report') ? 'active' : '' }}" href="{{ route('grn.report') }}">
            <i class="bi bi-bar-chart-steps"></i> Bill Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('grn.summary') ? 'active' : '' }}" href="{{ route('grn.summary') }}">
            <i class="bi bi-clipboard-data"></i> Bill Summary
        </a>
    </li>

    {{-- Stock --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('stock.transactions') ? 'active' : '' }}" href="{{ route('stock.transactions') }}">
            <i class="bi bi-repeat"></i> All Stock Transaction
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('daily.item.summary') }}">
            <i class="bi bi-calendar-week"></i> Daily Item Stock
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('stock_in_hand.index') }}">
            <i class="bi bi-box"></i> Stock In Hand
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('stock.bin_card') }}">
            <i class="bi bi-archive"></i> Stock Bin Card
        </a>
    </li>







    
</ul>

<!-- Main Section Cards -->


    </div>

    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    body {
        background: url('https://img.freepik.com/premium-photo/modern-payment-terminal-counter-dark-cafe-environment_1353959-18917.jpg?semt=ais_hybrid&w=740&q=80') no-repeat center center fixed;
        background-size: cover;
    }

  
    .background-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.05); 
        z-index: -1; 
    }
</style>

</body>


</html>

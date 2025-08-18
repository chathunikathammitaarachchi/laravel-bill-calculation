<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Bill System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #fff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            padding-top: 1rem;
        }

        .sidebar .nav-link {
            color: #333;
            padding: 10px 20px;
            display: block;
            font-weight: 500;
        }

        .sidebar .nav-link.active {
            background-color: #e7f1ff;
            color: #0d6efd !important;
            font-weight: bold;
        }

        .sidebar .nav-link:hover {
            background-color: #f1f1f1;
            color: #0a58ca;
        }

        .content {
            margin-left: 260px;
            padding: 2rem;
        }
    </style>
</head>

<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <h4 class="text-center mb-4">
            <a href="{{ route('home') }}" class="text-decoration-none text-primary fw-bold">Bill System</a>
        </h4>

        <ul class="nav flex-column">

            {{-- Supplier Section --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('supplier.index') ? 'active' : '' }}" href="{{ route('supplier.index') }}">
                    Supplier
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bill.create') ? 'active' : '' }}" href="{{ route('bill.create') }}">
                    Supplier GRN Order
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('bill.report') ? 'active' : '' }}" href="{{ route('bill.report') }}">
                    Supplier Report
                </a>
            </li>

            {{-- Item Section --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('items.index') ? 'active' : '' }}" href="{{ route('items.index') }}">
                    Items
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('stock.index') ? 'active' : '' }}" href="{{ route('stock.index') }}">
                    Item Stock Management
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('item_summaries.index') ? 'active' : '' }}" href="{{ route('item_summaries.index') }}">
                    Item Summary
                </a>
            </li>

               
            {{-- Customer Section --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('customer.index') ? 'active' : '' }}" href="{{ route('customer.index') }}">
                    Customer
                </a>
            </li>


             <li class="nav-item">
            <a class="nav-link" href="/">Customer Bill</a>
            </li>



            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('grn.dues') ? 'active' : '' }}" href="{{ route('grn.dues') }}">
                    Customer Dues
                </a>
            </li>




            {{-- Reports --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('grn.report') ? 'active' : '' }}" href="{{ route('grn.report') }}">
                    Bill Reports
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('grn.summary') ? 'active' : '' }}" href="{{ route('grn.summary') }}">
                    Bill Summary
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('daily.summary') ? 'active' : '' }}" href="{{ route('daily.summary') }}">
                    Stock In Hand
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('stock.transactions') ? 'active' : '' }}" href="{{ route('stock.transactions') }}">
                    All  Stock Transactions
                </a>
            </li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('stock.history') }}">Stock Ledger</a>
</li>




        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Bill System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f1f3f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            padding-top: 1rem;
            color: #fff;
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
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff !important;
            font-weight: bold;
            border-left: 4px solid #fff;
        }

        .sidebar h4 a {
            color: #fff;
            font-weight: 700;
            text-transform: uppercase;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }

        .content {
            margin-left: 260px;
            padding: 2rem;
        }

        .nav-item i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .content {
                margin-left: 0;
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

            {{-- Item --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('items.index') ? 'active' : '' }}" href="{{ route('items.index') }}">
                    <i class="bi bi-box-seam"></i> Items
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('stock.index') ? 'active' : '' }}" href="{{ route('stock.index') }}">
                    <i class="bi bi-gear-wide-connected"></i> Item Stock Management
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

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('daily.summary') ? 'active' : '' }}" href="{{ route('daily.summary') }}">
                    <i class="bi bi-journal-bookmark-fill"></i> Stock IN Hand
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('stock.transactions') ? 'active' : '' }}" href="{{ route('stock.transactions') }}">
                    <i class="bi bi-arrow-left-right"></i> All Stock Transactions
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('stock.history') }}">
                    <i class="bi bi-clock-history"></i> Stock Ledger
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('daily.item.summary') }}">
                    <i class="bi bi-calendar-week"></i> Daily Item Stock
                </a>
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

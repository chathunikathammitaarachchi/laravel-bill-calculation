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

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: #0d6efd !important;
        }

        .container {
            background: #ffffff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
        }

        .nav-link.active {
            font-weight: bold;
            color: #0d6efd !important;
        }

        .nav-link {
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #0a58ca !important;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">Bill System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customer.index') ? 'active' : '' }}" href="{{ route('customer.index') }}">
                            Customer
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('items.index') ? 'active' : '' }}" href="{{ route('items.index') }}">
                            Items
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('grn.report') ? 'active' : '' }}" href="{{ route('grn.report') }}">
                            Bill Reports
                        </a>
                    </li>

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
                        <a class="nav-link {{ request()->routeIs('stock.index') ? 'active' : '' }}" href="{{ route('stock.index') }}">
                            Item Stock Management
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('bill.report') ? 'active' : '' }}" href="{{ route('bill.report') }}">
                            Supplier Report
                        </a>
                    </li>

                   

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('stock.transactions') ? 'active' : '' }}" href="{{ route('stock.transactions') }}">
                             All Transactions
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('item_summaries.index') ? 'active' : '' }}" href="{{ route('item_summaries.index') }}">
                            Item Summary
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('daily.summary') ? 'active' : '' }}" href="{{ route('daily.summary') }}">
                            Daily Summary
                        </a>
                    </li>


<li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('grn.dues') ? 'active' : '' }}" href="{{ route('grn.dues') }}">
                            Customer Dues
                        </a>
                    </li>




<li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('grn.summary') ? 'active' : '' }}" href="{{ route('grn.summary') }}">
                            Bill summary
                        </a>
                    </li>



                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

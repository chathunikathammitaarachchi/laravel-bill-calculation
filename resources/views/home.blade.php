@extends('layouts.app')

@section('content')


    <div class="dashboard-background">
      <div class="container py-4" style="color: white;">
    <h1 class="mb-4">Welcome to the Bill System</h1>
    <p>This is the main Dashboard.</p>


        
    {{-- Supplier Section --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Manage Suppliers</h5>
                    <p class="card-text">Add and manage suppliers.</p>
                    <a href="{{ route('supplier.index') }}" class="btn btn-primary">Go to Suppliers</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Supplier GRN Order</h5>
                    <p class="card-text">Create and manage GRN orders from suppliers.</p>
                    <a href="{{ route('bill.create') }}" class="btn btn-primary">New GRN Order</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Supplier Report</h5>
                    <p class="card-text">Reports for supplier GRNs and purchases.</p>
                    <a href="{{ route('bill.report') }}" class="btn btn-primary">Supplier Report</a>
                </div>
            </div>
        </div>
    </div>



    {{-- Item Section --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Manage Items</h5>
                    <p class="card-text">View and manage stock items.</p>
                    <a href="{{ route('items.index') }}" class="btn btn-primary">Go to Items</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Stock Management</h5>
                    <p class="card-text">Monitor and update stock levels.</p>
                    <a href="{{ route('stock.index') }}" class="btn btn-primary">Manage Stock</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Item Summary</h5>
                    <p class="card-text">See detailed summary of item transactions.</p>
                    <a href="{{ route('item_summaries.index') }}" class="btn btn-primary">View Summary</a>
                </div>
            </div>
        </div>
    </div>


     <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Manage Customers</h5>
                    <p class="card-text">Add, view and manage customer details.</p>
                    <a href="{{ route('customer.index') }}" class="btn btn-primary">Go to Customers</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Customer Bill</h5>
                    <p class="card-text">Create new customer bills and manage them.</p>
                    <a href="/" class="btn btn-primary">Create Customer Bill</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Customer Dues</h5>
                    <p class="card-text">Check and manage customer dues.</p>
                    <a href="{{ route('grn.dues') }}" class="btn btn-primary">Customer Dues</a>
                </div>
            </div>
        </div>
    </div>

    


    {{-- Reports Section --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Billing Reports</h5>
                    <p class="card-text">Generate and view all customer bills.</p>
                    <a href="{{ route('grn.report') }}" class="btn btn-primary">Bill Reports</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Bill Summary</h5>
                    <p class="card-text">View summarized bills by customer or date.</p>
                    <a href="{{ route('grn.summary') }}" class="btn btn-primary">Bill Summary</a>
                </div>
            </div>
        </div>
        
        
    </div>

    {{-- Transaction Section --}}
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">All Stock Transaction</h5>
                    <p class="card-text">View all stock in/out transactions.</p>
                    <a href="{{ route('stock.transactions') }}" class="btn btn-primary">View Transactions</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">stock ledger</h5>
                    <p class="card-text">Check stock ledger.</p>
                    <a href="{{ route('stock.ledger') }}" class="btn btn-primary">stock ledger</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Daily Item Stock</h5>
                    <p class="card-text">Check Daily Item Stock summary.</p>
                    <a href="{{ route('daily.item.summary') }}" class="btn btn-primary">Daily Item Stock</a>
                </div>
            </div>
        </div>
       
    </div>


    
@endsection

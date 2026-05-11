@extends('admin.layouts.app')

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">

    <h1 class="h3 mb-0 text-gray-800">
        Dashboard
    </h1>

</div>

<!-- Content Row -->
<div class="row">

    <!-- Total Produk -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">

            <div class="card-body">

                <div class="row no-gutters align-items-center">

                    <div class="col mr-2">

                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Produk
                        </div>

                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $totalProducts }}
                        </div>

                    </div>

                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Total Order -->
    <div class="col-xl-3 col-md-6 mb-4">

        <div class="card border-left-success shadow h-100 py-2">

            <div class="card-body">

                <div class="row no-gutters align-items-center">

                    <div class="col mr-2">

                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Order
                        </div>

                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $totalOrders }}
                        </div>

                    </div>

                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Customer -->
    <div class="col-xl-3 col-md-6 mb-4">

        <div class="card border-left-info shadow h-100 py-2">

            <div class="card-body">

                <div class="row no-gutters align-items-center">

                    <div class="col mr-2">

                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Customer
                        </div>

                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $totalCustomers }}
                        </div>

                    </div>

                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Pending Order -->
    <div class="col-xl-3 col-md-6 mb-4">

        <div class="card border-left-warning shadow h-100 py-2">

            <div class="card-body">

                <div class="row no-gutters align-items-center">

                    <div class="col mr-2">

                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Order
                        </div>

                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $pendingOrders }}
                        </div>

                    </div>

                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- Revenue Card -->
<div class="row">

    <div class="col-xl-12">

        <div class="card shadow mb-4">

            <div class="card-body">

                <h5 class="font-weight-bold mb-3">
                    Total Revenue
                </h5>

                <h2 class="text-success font-weight-bold">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h2>

            </div>

        </div>

    </div>

</div>

@endsection
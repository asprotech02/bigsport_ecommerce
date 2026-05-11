<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    @php
        $role = auth()->user()->role ?? 'admin';

        $dashboardUrl = route('admin.dashboard');
    @endphp

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center"
       href="{{ $dashboardUrl }}">

        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-store"></i>
        </div>

        <div class="sidebar-brand-text mx-3">
            BigSport <sup>Admin</sup>
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">

        <a class="nav-link"
           href="{{ route('admin.dashboard') }}">

            <i class="fas fa-fw fa-tachometer-alt"></i>

            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- ========================= -->
    <!-- MENU ADMIN -->
    <!-- ========================= -->

    @if($role == 'admin')

        <li class="nav-item {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">

            <a class="nav-link"
            href="{{ route('admin.brands.index') }}">

                <i class="fas fa-fw fa-tags"></i>

                <span>Brand</span>
            </a>

        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.categories.index') }}">
                <i class="fas fa-list"></i>
                <span>Category</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.subcategories.index') }}">
                <i class="fas fa-tags"></i>
                <span>Subcategory</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.products.index') }}">
                <i class="fas fa-box"></i>
                <span>Products</span>
            </a>
        </li>

        <li class="nav-item">

            <a class="nav-link"
               href="#">

                <i class="fas fa-fw fa-users"></i>

                <span>Pelanggan</span>
            </a>
        </li>

        <li class="nav-item">

            <a class="nav-link"
               href="#">

                <i class="fas fa-fw fa-shopping-cart"></i>

                <span>Pesanan</span>
            </a>
        </li>

        <li class="nav-item">

            <a class="nav-link"
               href="#">

                <i class="fas fa-fw fa-tags"></i>

                <span>Promo</span>
            </a>
        </li>

    @endif

    <!-- ========================= -->
    <!-- SALES -->
    <!-- ========================= -->

    @if($role == 'sales')

        <li class="nav-item">

            <a class="nav-link"
               href="#">

                <i class="fas fa-fw fa-shopping-cart"></i>

                <span>Sales Order</span>
            </a>
        </li>

    @endif

    <!-- ========================= -->
    <!-- MANAGER -->
    <!-- ========================= -->

    @if(in_array($role, ['manager', 'admin']))

        <li class="nav-item">

            <a class="nav-link"
               href="#">

                <i class="fas fa-fw fa-chart-bar"></i>

                <span>Laporan</span>
            </a>
        </li>

    @endif

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Logout -->
    <li class="nav-item">

        <form action="{{ route('logout') }}"
              method="POST">

            @csrf

            <button type="submit"
                    class="nav-link border-0 bg-transparent w-100 text-start">

                <i class="fas fa-sign-out-alt"></i>

                <span>Logout</span>
            </button>
        </form>
    </li>

</ul>
<!-- End Sidebar -->

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
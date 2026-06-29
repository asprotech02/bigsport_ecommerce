<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    @php
        $role = auth()->user()->role ?? 'admin';
        $dashboardUrl = route('admin.dashboard');
    @endphp

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center py-4" href="{{ $dashboardUrl }}">
        <img src="{{ asset('assets/logo.png') }}" class="logo-img img-fluid" alt="Bagindo Jaya Logo" style="max-height: 50px;">
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Menu Utama</div>

    <!-- 1. Katalog Produk -->
    @if($role == 'admin')
        @php
            $isKatalogActive = request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.subcategories.*') || request()->routeIs('admin.brands.*');
        @endphp
        <li class="nav-item {{ $isKatalogActive ? 'active' : '' }}">
            <a class="nav-link {{ $isKatalogActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseKatalog"
                aria-expanded="{{ $isKatalogActive ? 'true' : 'false' }}" aria-controls="collapseKatalog">
                <i class="fas fa-fw fa-th-large"></i>
                <span>Katalog Produk</span>
            </a>
            <div id="collapseKatalog" class="collapse {{ $isKatalogActive ? 'show' : '' }}" aria-labelledby="headingKatalog">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">Produk</a>
                    <a class="collapse-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">Kategori</a>
                    <a class="collapse-item {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}" href="{{ route('admin.subcategories.index') }}">Subkategori</a>
                    <a class="collapse-item {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">Brand</a>
                </div>
            </div>
        </li>
    @endif

    <!-- 2. Pesanan & Transaksi -->
    @if(in_array($role, ['admin', 'sales', 'manager']))
        @php
            $isTransaksiActive = request()->routeIs('admin.orders.*') || request()->routeIs('admin.payments.*') || request()->routeIs('admin.shippings.*') || request()->routeIs('admin.pickups.*');
        @endphp
        <li class="nav-item {{ $isTransaksiActive ? 'active' : '' }}">
            <a class="nav-link {{ $isTransaksiActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseTransaksi"
                aria-expanded="{{ $isTransaksiActive ? 'true' : 'false' }}" aria-controls="collapseTransaksi">
                <i class="fas fa-fw fa-shopping-cart"></i>
                <span>Pesanan & Transaksi</span>
            </a>
            <div id="collapseTransaksi" class="collapse {{ $isTransaksiActive ? 'show' : '' }}" aria-labelledby="headingTransaksi">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">Pesanan</a>
                    <a class="collapse-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}">Pembayaran</a>
                    <a class="collapse-item {{ request()->routeIs('admin.shippings.*') ? 'active' : '' }}" href="{{ route('admin.shippings.index') }}">Pengiriman</a>
                    <a class="collapse-item {{ request()->routeIs('admin.pickups.*') ? 'active' : '' }}" href="{{ route('admin.pickups.index') }}">Pick Up (Ambil di Toko)</a>
                </div>
            </div>
        </li>
    @endif

    <!-- 3. Pelanggan & Pemasaran -->
    @if(in_array($role, ['admin', 'manager']))
        @php
            $isPemasaranActive = request()->routeIs('admin.customers.*') || request()->routeIs('admin.promos.*') || request()->routeIs('admin.reviews.*') || request()->routeIs('admin.notifications.*');
        @endphp
        <li class="nav-item {{ $isPemasaranActive ? 'active' : '' }}">
            <a class="nav-link {{ $isPemasaranActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePemasaran"
                aria-expanded="{{ $isPemasaranActive ? 'true' : 'false' }}" aria-controls="collapsePemasaran">
                <i class="fas fa-fw fa-users"></i>
                <span>Pelanggan & Promo</span>
            </a>
            <div id="collapsePemasaran" class="collapse {{ $isPemasaranActive ? 'show' : '' }}" aria-labelledby="headingPemasaran">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">Daftar Pengguna</a>
                    <a class="collapse-item {{ request()->routeIs('admin.promos.*') ? 'active' : '' }}" href="{{ route('admin.promos.index') }}">Promo & Voucher</a>
                    <a class="collapse-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">Ulasan Produk</a>
                    <a class="collapse-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">Notifikasi User</a>
                </div>
            </div>
        </li>
    @endif

    <!-- 4. Website & Laporan -->
    @if(in_array($role, ['admin', 'manager']))
        @php
            $isCMSActive = request()->routeIs('admin.reports.*') || request()->routeIs('admin.banners.*') || request()->routeIs('admin.pages.*');
        @endphp
        <li class="nav-item {{ $isCMSActive ? 'active' : '' }}">
            <a class="nav-link {{ $isCMSActive ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseCMS"
                aria-expanded="{{ $isCMSActive ? 'true' : 'false' }}" aria-controls="collapseCMS">
                <i class="fas fa-fw fa-laptop"></i>
                <span>Website & Laporan</span>
            </a>
            <div id="collapseCMS" class="collapse {{ $isCMSActive ? 'show' : '' }}" aria-labelledby="headingCMS">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">Laporan Penjualan</a>
                    <a class="collapse-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">CMS Banner Depan</a>
                    <a class="collapse-item {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}" href="{{ route('admin.pages.index') }}">CMS Halaman Statis</a>
                </div>
            </div>
        </li>
    @endif

    <hr class="sidebar-divider d-none d-md-block">

    <!-- Logout -->
    <li class="nav-item">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-left">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </li>

</ul>

<!-- Script Collapsible Dropdown Independen & Smooth -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const toggles = document.querySelectorAll('.sidebar .nav-link[data-toggle="collapse"]');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const targetId = this.getAttribute('data-target');
            const targetCollapse = document.querySelector(targetId);
            const isCurrentlyOpen = targetCollapse.classList.contains('show');
            
            // Menutup collapse lainnya (Accordion Mode)
            document.querySelectorAll('.sidebar .collapse').forEach(el => {
                if (el !== targetCollapse) {
                    $(el).collapse('hide');
                    el.previousElementSibling.classList.add('collapsed');
                    el.previousElementSibling.setAttribute('aria-expanded', 'false');
                }
            });
            
            // Toggle dropdown yang diklik (klik buka, klik lagi tutup)
            if (isCurrentlyOpen) {
                $(targetCollapse).collapse('hide');
                this.classList.add('collapsed');
                this.setAttribute('aria-expanded', 'false');
            } else {
                $(targetCollapse).collapse('show');
                this.classList.remove('collapsed');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });
});
</script>
<!-- End Sidebar -->
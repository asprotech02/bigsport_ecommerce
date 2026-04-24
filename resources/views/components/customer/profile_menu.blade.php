<div class="col-12 col-lg-3">
    <div class="profile-sidebar border pb-2">

        <a href="{{ route('profile') }}" 
           class="sidebar-link {{ request()->routeIs('profile') ? 'active' : '' }}">
            Akun Saya
        </a>

        <a href="{{ route('address') }}" 
           class="sidebar-link {{ request()->routeIs('address') ? 'active' : '' }}">
            Alamat
        </a>

        <div class="sidebar-divider"></div>

        <a href="#" 
           class="sidebar-link {{ request()->routeIs('orders') ? 'active' : '' }}">
            Pesanan
        </a>

        <a href="#" 
           class="sidebar-link {{ request()->routeIs('order_status') ? 'active' : '' }}">
            Status Pesanan
        </a>

        <div class="sidebar-divider"></div>

        <a href="#" class="sidebar-link">
            Kontak Kami
        </a>

        <a href="{{ route('store_location') }}" 
           class="sidebar-link {{ request()->routeIs('store_location') ? 'active' : '' }}">
            Lokasi Toko
        </a>

        <a href="#" class="sidebar-link">
            Bantuan
        </a>

    </div>
</div>
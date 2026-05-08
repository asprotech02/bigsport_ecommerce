@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-4 py-lg-5 bg-white" style="min-height: 70vh;">
        <div class="container">
            <div class="row g-4 g-lg-5">
                
                {{-- 1. SIDEBAR NAV TABS --}}
                <div class="col-12 col-lg-3">
                    <div class="profile-sidebar border pb-2 nav flex-column" role="tablist" aria-orientation="vertical">
                        <a class="sidebar-link active" id="tab-akun" data-bs-toggle="tab" href="#content-akun" role="tab" style="cursor: pointer;">Akun Saya</a>
                        <a class="sidebar-link" id="tab-alamat" data-bs-toggle="tab" href="#content-alamat" role="tab" style="cursor: pointer;">Alamat</a>
                        <div class="sidebar-divider"></div>
                        <a class="sidebar-link" id="tab-pesanan" data-bs-toggle="tab" href="#content-pesanan" role="tab" style="cursor: pointer;">Pesanan</a>
                        <a class="sidebar-link" id="tab-status" data-bs-toggle="tab" href="#content-status" role="tab" style="cursor: pointer;">Status Pesanan</a>
                        <div class="sidebar-divider"></div>
                        <a class="sidebar-link" id="tab-kontak" data-bs-toggle="tab" href="#content-kontak" role="tab" style="cursor: pointer;">Kontak Kami</a>
                        <a class="sidebar-link" id="tab-lokasi" data-bs-toggle="tab" href="#content-lokasi" role="tab" style="cursor: pointer;">Lokasi Toko</a>
                    </div>
                </div>

                {{-- 2. KONTEN TAB PANES --}}
                <div class="col-12 col-lg-9 ps-lg-5">
                    <div class="tab-content" id="profileTabContent">
                        
                        {{-- TAB: AKUN SAYA --}}
                        <div class="tab-pane fade show active" id="content-akun" role="tabpanel">
                            <h3 class="fw-bold mb-5 text-uppercase" style="letter-spacing: 0.5px;">INFORMASI PENGGUNA</h3>
                            <div class="mb-5">
                                <h6 class="fw-bold mb-3 text-dark">Detail Pengguna</h6>
                                <div class="profile-info-text mb-2">{{ $user->name ?? 'Belum diatur' }}</div>
                                <div class="profile-info-text mb-2">
                                    {{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->translatedFormat('d F Y') : 'Tanggal lahir belum diatur' }}
                                </div>
                                <div class="profile-info-text mb-4">
                                    {{ $user->gender == 'L' ? 'Laki-laki' : ($user->gender == 'P' ? 'Perempuan' : 'Jenis kelamin belum diatur') }}
                                </div>
                                <a href="{{ route('profile_edit') }}" class="btn btn-dark btn-sm px-4 py-2">Edit</a>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-3 text-dark">Detail Login</h6>
                                
                                {{-- Seksi Email --}}
                                <div class="mb-4">
                                    <div class="profile-info-text mb-2">{{ $user->email }}</div>
                                    <a href="{{ route('email_edit') }}" class="btn btn-dark btn-sm px-4 py-2">Edit Email</a>
                                </div>

                                {{-- Seksi Password --}}
                                <div class="mb-2">
                                    <div class="profile-info-text mb-2">*************</div>
                                    <a href="{{ route('password_edit') }}" class="btn btn-dark btn-sm px-4 py-2">Edit Password</a>
                                </div>
                            </div>
                        </div>

                        {{-- TAB: ALAMAT --}}
                        <div class="tab-pane fade" id="content-alamat" role="tabpanel">
                            <div class="mb-5">
                                <h3 class="fw-bold text-uppercase m-0" style="letter-spacing: 0.5px;">Informasi Alamat</h3>
                            </div>

                            <div id="address-list">
                                @forelse($addresses as $addr)
                                    <div class="address-card border p-4 mb-4 position-relative {{ $addr->is_default ? 'border-dark' : 'border-secondary-subtle' }}" 
                                         id="address-card-{{ $addr->id }}" data-id="{{ $addr->id }}"
                                         style="{{ $addr->is_default ? 'border-width: 2px;' : '' }}">
                                        
                                        <div class="address-content">
                                            @if($addr->is_default)
                                                <span class="badge bg-dark text-white rounded-0 position-absolute top-0 end-0 px-3 py-2 badge-utama" style="font-size: 10px; letter-spacing: 1px;">UTAMA</span>
                                            @endif
                                            <h6 class="fw-bold text-dark mb-2 text-uppercase">{{ $addr->receiver_name }}</h6>
                                            <p class="text-secondary mb-1" style="font-size: 13px;">{{ $addr->receiver_phone }}</p>
                                            <p class="profile-info-text mb-3" style="line-height: 1.6; max-width: 550px; font-size: 14px; color: #444;">
                                                {{ $addr->full_address }}, {{ $addr->village_name }}, {{ $addr->district_name }}, {{ $addr->city_name }}, {{ $addr->province_name }}, {{ $addr->postal_code }}
                                            </p>
                                        </div>

                                        <div class="d-flex gap-3 align-items-center">
                                            {{-- Link di halaman profile.blade.php --}}
<a href="{{ route('address.edit', $addr->id) }}" class="text-dark fw-bold text-decoration-none" style="font-size: 12px; letter-spacing: 0.5px;">
    <i class="bi bi-pencil-square me-1"></i> EDIT
</a>
                                            <div class="action-buttons d-flex gap-3 align-items-center">
                                                @if(!$addr->is_default)
                                                    <div class="vr" style="height: 15px; width: 1px;"></div>
                                                    <button onclick="setMainAddress({{ $addr->id }})" class="bg-transparent border-0 p-0 text-dark fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">SET UTAMA</button>
                                                    <div class="vr" style="height: 15px; width: 1px;"></div>
                                                    <button onclick="deleteAddress({{ $addr->id }})" class="bg-transparent border-0 p-0 text-danger fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">HAPUS</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div id="empty-address" class="text-center py-5 border border-dashed mb-4">
                                        <p class="text-secondary m-0">Anda belum memiliki daftar alamat</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="mt-5">
                                <a href="{{ route('address_form') }}" class="btn btn-dark w-100 rounded-0 py-3 fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 14px;">
                                    Tambah Alamat Baru
                                </a>
                            </div>
                        </div>

                        {{-- TAB: PESANAN --}}
                        <div class="tab-pane fade" id="content-pesanan" role="tabpanel">
                            <div class="mb-4">
                                <h3 class="fw-bold text-uppercase m-0" style="font-size: 24px; letter-spacing: 1px;">Pesanan Saya</h3>
                            </div>

                            <ul class="nav border-bottom border-secondary-subtle mb-4 gap-3 gap-md-4 d-flex flex-nowrap overflow-x-auto thumbnail-scroll" id="orderTabs" role="tablist">
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 active text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#semua" type="button">Semua</button></li>
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#belum-bayar" type="button">Belum Bayar</button></li>
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#diproses" type="button">Diproses</button></li>
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#dikirim-siap" type="button">Dikirim / Diambil</button></li>
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#selesai" type="button">Selesai</button></li>
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#dibatalkan" type="button">Dibatalkan</button></li>
                            </ul>

                            @php
                                $orderList = $orders ?? collect(); 
                                
                                function getCustomerStatus($order) {
                                    if ($order->status == 'cancelled' || in_array($order->payment_status, ['failed', 'expired'])) {
                                        return ['label' => 'Pesanan Dibatalkan', 'class' => 'text-secondary'];
                                    }
                                    if ($order->payment_status == 'unpaid') {
                                        return ['label' => 'Menunggu Pembayaran', 'class' => 'text-danger'];
                                    }
                                    switch($order->status) {
                                        case 'processing': return ['label' => 'Sedang Dikemas', 'class' => 'text-warning text-dark'];
                                        case 'ready_for_pickup': return ['label' => 'Siap Diambil', 'class' => 'text-info text-dark'];
                                        case 'shipped': return ['label' => 'Dalam Pengiriman', 'class' => 'text-primary'];
                                        case 'completed': return ['label' => 'Pesanan Selesai', 'class' => 'text-success'];
                                        default: return ['label' => 'Menunggu Konfirmasi', 'class' => 'text-warning text-dark'];
                                    }
                                }

                                $tabData = [
                                    'semua' => ['data' => $orderList, 'empty' => 'Belum ada pesanan sama sekali.'],
                                    'belum-bayar' => ['data' => $orderList->where('payment_status', 'unpaid')->whereNotIn('status', ['cancelled']), 'empty' => 'Tidak ada pesanan yang menunggu pembayaran.'],
                                    'diproses' => ['data' => $orderList->where('payment_status', 'paid')->whereIn('status', ['pending', 'processing']), 'empty' => 'Tidak ada pesanan yang sedang dikemas/diproses.'],
                                    'dikirim-siap' => ['data' => $orderList->whereIn('status', ['shipped', 'ready_for_pickup']), 'empty' => 'Tidak ada pesanan dalam perjalanan atau siap diambil.'],
                                    'selesai' => ['data' => $orderList->where('status', 'completed'), 'empty' => 'Belum ada riwayat pesanan yang selesai.'],
                                    'dibatalkan' => ['data' => $orderList->filter(function($o) { return $o->status == 'cancelled' || in_array($o->payment_status, ['failed', 'expired']); }), 'empty' => 'Tidak ada pesanan yang dibatalkan.'],
                                ];
                            @endphp

                            <div class="tab-content" id="orderTabsContent">
                                @foreach($tabData as $tabId => $tabInfo)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel">
                                        @forelse($tabInfo['data'] as $order)
                                            @php
                                                $statusData = getCustomerStatus($order);
                                                $isPickup = $order->shippingDetail && $order->shippingDetail->courier_company === 'pickup';
                                            @endphp

                                            <div class="border border-secondary-subtle rounded-0 mb-4 bg-white shadow-sm">
                                                
                                                <div class="d-flex justify-content-between align-items-center p-3 border-bottom border-secondary-subtle bg-light">
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <i class="bi bi-shop fs-5 d-none d-sm-inline"></i>
                                                        <span class="fw-bold text-uppercase" style="font-size: 14px;">Big Sport Tangerang</span>
                                                        
                                                        @if($isPickup)
                                                            <span class="badge bg-dark rounded-0 px-2 py-1 ms-sm-2" style="font-size: 10px; letter-spacing: 0.5px;"><i class="bi bi-shop-window me-1"></i> AMBIL DI TOKO</span>
                                                        @else
                                                            <span class="badge border border-dark text-dark rounded-0 px-2 py-1 ms-sm-2" style="font-size: 10px; letter-spacing: 0.5px;"><i class="bi bi-truck me-1"></i> DIKIRIM</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-end text-uppercase fw-bold" style="font-size: 13px; letter-spacing: 0.5px;">
                                                        <span class="{{ $statusData['class'] }}">{{ $statusData['label'] }}</span>
                                                    </div>
                                                </div>

                                                <div class="p-3">
                                                    @php
                                                        $firstItem = $order->items->first();
                                                        $otherItemsCount = $order->items->count() - 1;
                                                        $imagePath = null;
                                                        if ($firstItem && $firstItem->sku && $firstItem->sku->product && $firstItem->sku->product->images->isNotEmpty()) {
                                                            $primaryImg = $firstItem->sku->product->images->where('is_primary', true)->first() ?? $firstItem->sku->product->images->first();
                                                            if ($primaryImg) $imagePath = 'storage/' . $primaryImg->image_path;
                                                        }
                                                    @endphp
                                                    
                                                    @if($firstItem)
                                                    <div class="d-flex align-items-start py-2">
                                                        <div class="ratio ratio-1x1 border border-secondary-subtle flex-shrink-0 bg-light me-3" style="width: 80px;">
                                                            @if($imagePath) 
                                                                <img src="{{ asset($imagePath) }}" class="object-fit-cover w-100 h-100" alt="{{ $firstItem->product_name }}">
                                                            @else 
                                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary bg-light"><i class="bi bi-image text-muted fs-4"></i></div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <h6 class="fw-bold mb-1 pe-3" style="font-size: 15px; line-height: 1.4;">{{ $firstItem->product_name }}</h6>
                                                                <span class="fw-bold text-nowrap" style="font-size: 14px;">Rp {{ number_format($firstItem->price_at_purchase, 0, ',', '.') }}</span>
                                                            </div>
                                                            <p class="text-secondary mb-1" style="font-size: 13px;">Variasi: {{ $firstItem->product_size ?? '-' }}</p>
                                                            <p class="text-secondary mb-0" style="font-size: 13px;">x{{ $firstItem->quantity }}</p>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if($otherItemsCount > 0)
                                                        <div class="mt-2 pt-2 border-top border-light text-secondary text-center" style="font-size: 12px; font-weight: 600;">
                                                            Tampilkan {{ $otherItemsCount }} produk lainnya <i class="bi bi-chevron-down ms-1"></i>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="bg-light p-3 border-top border-secondary-subtle">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="text-secondary" style="font-size: 12px;">No. Pesanan: <span class="fw-bold text-dark">#{{ $order->invoice_number }}</span></span>
                                                        <div class="text-end">
                                                            <span class="text-secondary me-2" style="font-size: 13px;">Total Pesanan:</span>
                                                            <h4 class="fw-bold text-danger d-inline-block m-0" style="font-size: 20px;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</h4>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex gap-2 justify-content-end flex-wrap mt-2">
                                                        {{-- 🌟 FIX: Pastikan route detail order diarahkan dengan benar jika ada route-nya --}}
                                                        <a href="{{ route('order.detail', $order->id) }}" class="btn btn-outline-dark fw-bold text-uppercase rounded-0" style="font-size: 12px; padding: 8px 20px;">
                                                            Detail Pesanan
                                                        </a>

                                                        @if($order->payment_status == 'unpaid' && $order->status != 'cancelled')
                                                            <button type="button" class="btn btn-danger fw-bold text-uppercase btn-lanjut-bayar rounded-0" style="font-size: 12px; padding: 8px 20px;" data-token="{{ $order->snap_token }}">
                                                                Bayar Sekarang
                                                            </button>
                                                        @endif

                                                        @if($order->status == 'shipped' && !$isPickup)
                                                            {{-- 🌟 FIX: Ganti href jadi onclick agar AJAX Tracking berfungsi! --}}
                                                            <button type="button" onclick="loadTrackingData({{ $order->id }})" class="btn btn-dark fw-bold text-uppercase rounded-0" style="font-size: 12px; padding: 8px 20px;">
                                                                Lacak Pesanan
                                                            </button>
                                                            <button type="button" class="btn btn-success fw-bold text-uppercase rounded-0" style="font-size: 12px; padding: 8px 20px;">
                                                                Pesanan Diterima
                                                            </button>
                                                        @endif

                                                        @if($order->status == 'ready_for_pickup' && $isPickup)
                                                            <button type="button" class="btn btn-dark fw-bold text-uppercase rounded-0" style="font-size: 12px; padding: 8px 20px;">
                                                                <i class="bi bi-qr-code me-1"></i> QR Code Pengambilan
                                                            </button>
                                                        @endif

                                                        @if($order->status == 'completed')
                                                            <button type="button" class="btn btn-dark fw-bold text-uppercase rounded-0" style="font-size: 12px; padding: 8px 20px;">
                                                                Beri Ulasan
                                                            </button>
                                                            <a href="{{ route('product.index') }}" class="btn btn-outline-dark fw-bold text-uppercase rounded-0" style="font-size: 12px; padding: 8px 20px;">
                                                                Beli Lagi
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-5 bg-white border border-secondary-subtle">
                                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                    <i class="bi bi-receipt display-5 text-secondary opacity-50"></i>
                                                </div>
                                                <h6 class="fw-bold text-uppercase mb-2">Tidak Ada Data</h6>
                                                <p class="text-secondary mb-4" style="font-size: 14px;">{{ $tabInfo['empty'] }}</p>
                                                <a href="{{ route('product.index') }}" class="btn btn-dark rounded-0 fw-bold px-4 py-2 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Mulai Belanja</a>
                                            </div>
                                        @endforelse
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- 🌟 FIX: TAB STATUS PESANAN (HTML WADAH YANG HILANG DARI KODINGAN LU) --}}
                        <div class="tab-pane fade" id="content-status" role="tabpanel">
                            <div class="mb-4">
                                <h3 class="fw-bold text-uppercase m-0" style="font-size: 24px; letter-spacing: 1px;">Status Pesanan</h3>
                                <button type="button" class="btn btn-link text-dark p-0 mt-2 text-decoration-none fw-bold" style="font-size: 13px;" onclick="new bootstrap.Tab(document.querySelector('#tab-pesanan')).show();">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Pesanan
                                </button>
                            </div>

                            <div id="tracking-loading" class="text-center py-5 border border-secondary-subtle bg-light" style="display: none;">
                                <div class="spinner-border text-dark mb-3" role="status"></div>
                                <h6 class="fw-bold text-uppercase mb-1" style="font-size: 14px; letter-spacing: 1px;">Mencari Data Resi...</h6>
                                <p class="text-secondary mb-0" style="font-size: 13px;">Tunggu sebentar, kami sedang menghubungi pihak kurir.</p>
                            </div>

                            <div id="tracking-error" class="text-center py-5 border border-secondary-subtle bg-light" style="display: none;">
                                <i class="bi bi-exclamation-triangle text-danger display-4 mb-3 d-block"></i>
                                <h6 class="fw-bold text-uppercase mb-2">Gagal Melacak</h6>
                                <p class="text-secondary mb-0" id="tracking-error-text" style="font-size: 14px;"></p>
                            </div>

                            <div id="tracking-header" class="border border-secondary-subtle p-4 mb-4 bg-light shadow-sm" style="display: none;">
                                <div class="row align-items-center text-center text-md-start">
                                    <div class="col-12 col-md-4 mb-3 mb-md-0 border-end-md border-secondary-subtle">
                                        <p class="text-secondary mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Ekspedisi Kurir</p>
                                        <h5 class="fw-bold m-0 text-dark" id="track-courier-name">-</h5>
                                    </div>
                                    <div class="col-12 col-md-4 mb-3 mb-md-0 border-end-md border-secondary-subtle">
                                        <p class="text-secondary mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Nomor Resi</p>
                                        <h5 class="fw-bold m-0 text-dark" style="letter-spacing: 1px;" id="track-waybill-id">-</h5>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <p class="text-secondary mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Status Saat Ini</p>
                                        <h5 class="fw-bold m-0 text-success text-uppercase" id="track-current-status">-</h5>
                                    </div>
                                </div>
                            </div>

                            <div id="tracking-timeline-container" class="border border-secondary-subtle p-4 p-md-5 bg-white shadow-sm" style="display: none;">
                                <h6 class="fw-bold text-uppercase mb-4 pb-3 border-bottom border-dark" style="font-size: 15px; letter-spacing: 1px;">Riwayat Perjalanan Paket</h6>
                                <div id="tracking-history-list" class="position-relative ms-3 border-start border-2 border-dark pb-2 mt-4">
                                    </div>
                            </div>
                        </div>
                        {{-- END TAB STATUS PESANAN --}}

                        {{-- TAB: KONTAK KAMI --}}
                        <div class="tab-pane fade" id="content-kontak" role="tabpanel">
                            <div class="mb-4"><h3 class="fw-bold text-uppercase m-0" style="font-size: 24px; letter-spacing: 1px;">Hubungi Kami</h3><p class="text-secondary mt-1" style="font-size: 13px;">Kami siap membantu Anda.</p></div>
                            <div class="row g-5">
                                <div class="col-12 col-xl-5">
                                    <div class="bg-light p-4 p-md-5 border border-secondary-subtle rounded-0 h-100">
                                        <h5 class="fw-bold text-uppercase mb-4" style="font-size: 16px;">Informasi Kontak</h5>
                                        <div class="d-flex align-items-start mb-4"><i class="bi bi-geo-alt fs-5 me-3 text-dark"></i><div><h6 class="fw-bold mb-1" style="font-size: 13px;">Alamat Toko Utama</h6><p class="text-secondary mb-0" style="font-size: 12px;">Jl. Jenderal Sudirman No. 45, Tangerang</p></div></div>
                                        <div class="d-flex align-items-start mb-4"><i class="bi bi-envelope fs-5 me-3 text-dark"></i><div><h6 class="fw-bold mb-1" style="font-size: 13px;">Email</h6><p class="text-secondary mb-0" style="font-size: 12px;">support@bigsport.com</p></div></div>
                                        <div class="d-flex align-items-start"><i class="bi bi-telephone fs-5 me-3 text-dark"></i><div><h6 class="fw-bold mb-1" style="font-size: 13px;">WhatsApp</h6><p class="text-secondary mb-0" style="font-size: 12px;">+62 812 3456 7890</p></div></div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-7">
                                    <form action="#"><div class="row g-3">
                                        <div class="col-md-6"><label class="fw-bold mb-2 text-uppercase" style="font-size: 11px;">Nama</label><input type="text" class="form-control rounded-0 border-dark p-3 shadow-none" placeholder="John Doe"></div>
                                        <div class="col-md-6"><label class="fw-bold mb-2 text-uppercase" style="font-size: 11px;">Email</label><input type="email" class="form-control rounded-0 border-dark p-3 shadow-none" placeholder="john@example.com"></div>
                                        <div class="col-12"><label class="fw-bold mb-2 text-uppercase" style="font-size: 11px;">Pesan</label><textarea class="form-control rounded-0 border-dark p-3 shadow-none" rows="5" placeholder="Pesan Anda..."></textarea></div>
                                        <div class="col-12 mt-4"><button type="submit" class="btn btn-dark w-100 rounded-0 py-3 fw-bold text-uppercase">KIRIM PESAN</button></div>
                                    </div></form>
                                </div>
                            </div>
                        </div>

                        {{-- TAB: LOKASI TOKO --}}
                        <div class="tab-pane fade" id="content-lokasi" role="tabpanel">
                            <h3 class="fw-bold mb-5 text-uppercase" style="letter-spacing: 0.5px;">INFORMASI LOKASI TOKO</h3>
                            @php
                                $locations = [
                                    ['title' => 'Bigsport Tangerang Selatan', 'addr' => 'Jl. HOS Cokroaminoto No.52, Larangan', 'map' => 'https://maps.google.com/maps?q=Bigsport+Tangerang+Selatan&t=&z=13&ie=UTF8&iwloc=&output=embed', 'link' => '#'],
                                    ['title' => 'Bigsport Citra Raya', 'addr' => 'QG7F+2JW, Cikupa, Tangerang', 'map' => 'https://maps.google.com/maps?q=Cikupa+Tangerang&t=&z=13&ie=UTF8&iwloc=&output=embed', 'link' => '#'],
                                ];
                            @endphp
                            @foreach($locations as $loc)
                                <div class="row g-4 mb-5 border-bottom pb-4">
                                    <div class="col-12 col-lg-6"><div class="rounded overflow-hidden border border-secondary-subtle"><iframe src="{{ $loc['map'] }}" width="100%" height="250" style="border:0;" loading="lazy"></iframe></div></div>
                                    <div class="col-12 col-lg-6 d-flex flex-column justify-content-center"><h5 class="fw-bold text-dark mb-3">{{ $loc['title'] }}</h5><p class="profile-info-text mb-4">{{ $loc['addr'] }}</p><a href="{{ $loc['link'] }}" target="_blank" class="btn btn-outline-dark rounded-0 px-4 py-3 fw-bold w-100 text-uppercase" style="letter-spacing: 1px;"><i class="bi bi-map me-2"></i>Buka di Google Maps</a></div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    @include('customer.components.footer')
    @include('customer.components.chatbot')

    @push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    
    <style>
        .border-end-md { border-right: 1px solid #dee2e6; }
        @media (max-width: 768px) { .border-end-md { border-right: none; border-bottom: 1px solid #dee2e6; padding-bottom: 15px; } }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');

            if (tabParam === 'orders') {
                const orderTabTrigger = document.getElementById('tab-pesanan');
                if (orderTabTrigger) {
                    const tab = new bootstrap.Tab(orderTabTrigger);
                    tab.show();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-lanjut-bayar').forEach(btn => {
                btn.addEventListener('click', function() {
                    let token = this.getAttribute('data-token');
                    let originalText = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> MEMUAT...';
                    this.disabled = true;
                    
                    window.snap.pay(token, {
                        onSuccess: () => { window.location.reload(); },
                        onPending: () => { btn.innerHTML = originalText; btn.disabled = false; },
                        onError: () => { btn.innerHTML = originalText; btn.disabled = false; },
                        onClose: () => { btn.innerHTML = originalText; btn.disabled = false; }
                    });
                });
            });
        });

        function setMainAddress(id) {
            fetch(`/address/${id}/set-main`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    window.location.reload(); // Paling aman refresh buat sinkronin UI
                }
            });
        }

        function deleteAddress(id) {
            fetch(`/address/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    const card = document.getElementById(`address-card-${id}`);
                    if(card) {
                        card.style.transition = '0.3s'; 
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)'; 
                        setTimeout(() => card.remove(), 300);
                    }
                }
            });
        }

        moment.locale('id');

        function loadTrackingData(orderId) {
            // Pindah ke tab Status Pesanan
            const statusTab = new bootstrap.Tab(document.querySelector('#tab-status'));
            statusTab.show();
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Reset UI
            document.getElementById('tracking-header').style.display = 'none';
            document.getElementById('tracking-timeline-container').style.display = 'none';
            document.getElementById('tracking-error').style.display = 'none';
            document.getElementById('tracking-loading').style.display = 'block';

            // 🌟 FIX: Pastikan URL AJAX sesuai dengan Route web.php lu (api.order.tracking)
            axios.get(`/profile/order/${orderId}/tracking`)
                .then(response => {
                    document.getElementById('tracking-loading').style.display = 'none';

                    if (response.data.success) {
                        const data = response.data.data;
                        
                        document.getElementById('track-courier-name').innerText = data.courier.company || 'Ekspedisi';
                        document.getElementById('track-waybill-id').innerText = data.courier.waybill_id || 'Menunggu Resi';
                        document.getElementById('track-current-status').innerText = data.status || 'Diproses';

                        const historyContainer = document.getElementById('tracking-history-list');
                        historyContainer.innerHTML = '';

                        if (data.history && data.history.length > 0) {
                            data.history.forEach((item, index) => {
                                const isLatest = index === 0; 
                                const timeFormatted = moment(item.updated_at).format('DD MMM YYYY, HH:mm') + ' WIB';

                                const dotStyle = isLatest 
                                    ? 'bg-dark rounded-circle flex-shrink-0 mt-1 border border-white' 
                                    : 'bg-white rounded-circle flex-shrink-0 mt-1 border border-dark';
                                
                                const dotSize = isLatest 
                                    ? 'width: 16px; height: 16px; border-width: 3px !important; box-shadow: 0 0 0 2px #000; left: -9px; position: absolute;'
                                    : 'width: 14px; height: 14px; border-width: 2px !important; left: -8px; position: absolute;';
                                    
                                const textOpacity = isLatest ? '' : 'opacity-75';

                                const htmlRow = `
                                    <div class="position-relative mb-4 ps-4 pb-2" style="z-index: 2;">
                                        <div class="${dotStyle}" style="${dotSize}"></div>
                                        <div class="${textOpacity}">
                                            <h6 class="fw-bold text-uppercase mb-1" style="font-size: 13px;">${item.status}</h6>
                                            <p class="text-secondary mb-1" style="font-size: 13px; line-height: 1.5;">${item.note}</p>
                                            <span class="${isLatest ? 'fw-bold text-dark' : 'text-secondary fw-bold'}" style="font-size: 11px; letter-spacing: 0.5px;">
                                                <i class="bi bi-clock me-1"></i> ${timeFormatted}
                                            </span>
                                        </div>
                                    </div>
                                `;
                                historyContainer.insertAdjacentHTML('beforeend', htmlRow);
                            });
                        } else {
                            historyContainer.innerHTML = '<p class="text-secondary ms-4">Belum ada riwayat perjalanan paket.</p>';
                        }

                        document.getElementById('tracking-header').style.display = 'block';
                        document.getElementById('tracking-timeline-container').style.display = 'block';

                    } else {
                        document.getElementById('tracking-error-text').innerText = response.data.message || "Resi belum bisa dilacak";
                        document.getElementById('tracking-error').style.display = 'block';
                    }
                })
                .catch(error => {
                    document.getElementById('tracking-loading').style.display = 'none';
                    let errorMsg = 'Gagal menghubungi server.';
                    if (error.response) {
                        errorMsg = `Server gagal memuat resi. (Error ${error.response.status})`;
                        console.error("Tracking Error:", error.response.data);
                    }
                    document.getElementById('tracking-error-text').innerText = errorMsg;
                    document.getElementById('tracking-error').style.display = 'block';
                });
        }
    </script>
    @endpush
@endsection
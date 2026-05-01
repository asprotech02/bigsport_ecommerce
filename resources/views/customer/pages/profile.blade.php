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
                                    {{ $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->translatedFormat('d F Y') : 'Tanggal lahir belum diatur' }}
                                </div>
                                <div class="profile-info-text mb-4">{{ $user->gender ?? 'Jenis kelamin belum diatur' }}</div>
                                <a href="{{ route('profile_edit') }}" class="btn btn-black btn-sm px-4 py-2">Edit</a>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-3 text-dark">Detail Login</h6>
                                <div class="profile-info-text mb-2">{{ $user->email }}</div>
                                <div class="profile-info-text mb-4">*************</div>
                                <a href="{{ route('login_edit') }}" class="btn btn-black btn-sm px-4 py-2">Edit</a>
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
                                            <a href="{{ route('address_edit', $addr->id) }}" class="text-dark fw-bold text-decoration-none" style="font-size: 12px; letter-spacing: 0.5px;">
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
                                <a href="{{ route('address_edit') }}" class="btn btn-black w-100 rounded-0 py-3 fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 14px;">
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
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#dikemas" type="button">Dikemas</button></li>
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#dikirim" type="button">Dikirim</button></li>
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#selesai" type="button">Selesai</button></li>
                            </ul>

                            @php
                                $orderList = $orders ?? collect(); 
                                $tabData = [
                                    'semua' => $orderList,
                                    'belum-bayar' => $orderList->where('payment_status', 'unpaid'),
                                    'dikemas' => $orderList->whereIn('status', ['pending', 'processing'])->where('payment_status', 'paid'),
                                    'dikirim' => $orderList->where('status', 'shipped'),
                                    'selesai' => $orderList->where('status', 'completed'),
                                ];
                            @endphp

                            <div class="tab-content" id="orderTabsContent">
                                @foreach($tabData as $tabId => $tabOrders)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel">
                                        @forelse($tabOrders as $order)
                                            <div class="border border-dark rounded-0 p-3 p-md-4 mb-4">
                                                <div class="d-flex flex-column flex-md-row justify-content-between border-bottom border-secondary-subtle pb-3 mb-3 gap-2">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <span class="fw-bold text-uppercase" style="font-size: 13px;">{{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d M Y') }}</span>
                                                        @if($order->payment_status == 'unpaid') <span class="badge bg-danger rounded-0 text-uppercase px-2 py-1" style="letter-spacing: 0.5px; font-weight: 600;">BELUM BAYAR</span>
                                                        @elseif($order->status == 'pending' || $order->status == 'processing') <span class="badge bg-info text-dark rounded-0 text-uppercase px-2 py-1" style="letter-spacing: 0.5px; font-weight: 600;">DIKEMAS</span>
                                                        @elseif($order->status == 'shipped') <span class="badge bg-primary rounded-0 text-uppercase px-2 py-1" style="letter-spacing: 0.5px; font-weight: 600;">DIKIRIM</span>
                                                        @elseif($order->status == 'completed') <span class="badge bg-success rounded-0 text-uppercase px-2 py-1" style="letter-spacing: 0.5px; font-weight: 600;">SELESAI</span>
                                                        @else <span class="badge bg-secondary rounded-0 text-uppercase px-2 py-1">{{ $order->status }}</span>
                                                        @endif
                                                        <span class="text-secondary d-none d-sm-inline" style="font-size: 13px;">#{{ $order->invoice_number }}</span>
                                                    </div>
                                                </div>
                                                
                                                @foreach($order->items as $item)
                                                    @php
                                                        $imagePath = null;
                                                        if ($item->sku && $item->sku->product && $item->sku->product->images->isNotEmpty()) {
                                                            $primaryImg = $item->sku->product->images->where('is_primary', true)->first() ?? $item->sku->product->images->first();
                                                            if ($primaryImg) $imagePath = 'storage/' . $primaryImg->image_path;
                                                        }
                                                    @endphp
                                                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 gap-md-4 mb-3">
                                                        <div class="ratio ratio-1x1 border border-secondary-subtle flex-shrink-0 bg-light" style="width: 80px;">
                                                            @if($imagePath) <img src="{{ asset($imagePath) }}" class="object-fit-cover w-100 h-100" alt="{{ $item->product_name }}">
                                                            @else <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary bg-light" style="font-size: 10px;"><i class="bi bi-image text-muted fs-4"></i></div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="fw-bold text-uppercase mb-1" style="font-size: 15px;">{{ $item->product_name }}</h6>
                                                            <p class="text-secondary mb-0" style="font-size: 13px;">Ukuran: {{ $item->product_size ?? '-' }}</p>
                                                            <p class="text-secondary mt-1 mb-0" style="font-size: 12px;">{{ $item->quantity }} Barang x Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</p>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                {{-- Ganti bagian div footer kartu pesanan Anda dengan ini --}}
                                                <div class="d-flex flex-column flex-sm-row justify-content-between mt-4 pt-3 border-top border-secondary-subtle gap-2 align-items-sm-center">
                                                    <div>
                                                        <p class="text-secondary mb-1" style="font-size: 12px;">Total Belanja</p>
                                                        <h5 class="fw-bold mb-0">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</h5>
                                                    </div>
                                                    
                                                    <div class="d-flex gap-2 justify-content-end">
                                                        {{-- Tombol Lanjut Bayar (Sudah ada di kode Anda) --}}
                                                        @if($order->payment_status == 'unpaid' && $order->snap_token)
                                                            <button type="button" class="btn btn-black fw-bold text-uppercase btn-lanjut-bayar" 
                                                                    style="border-radius: 0; font-size: 12px; padding: 10px 20px;" 
                                                                    data-token="{{ $order->snap_token }}">
                                                                Lanjut Bayar
                                                            </button>
                                                        @endif

                                                        {{-- TOMBAL BARU: Lihat Status Pesanan --}}
                                                        @if($order->payment_status == 'paid')
                                                            <button type="button" onclick="loadTrackingData({{ $order->id }})" 
                                                                    class="btn btn-outline-dark fw-bold text-uppercase rounded-0" 
                                                                    style="font-size: 12px; padding: 10px 20px;">
                                                                Lihat Status Pesanan
                                                            </button>
                                                        @else
                                                            {{-- Tombol Detail opsional jika belum bayar agar UI tetap simetris --}}
                                                            <button type="button" class="btn btn-outline-secondary fw-bold text-uppercase rounded-0" 
                                                                    style="font-size: 12px; padding: 10px 20px; border-color: #ddd;" disabled>
                                                                Menunggu Pembayaran
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-5">
                                                <i class="bi bi-bag-x display-1 text-secondary opacity-50 mb-3 d-block"></i>
                                                <h5 class="fw-bold text-uppercase">Belum Ada Pesanan</h5>
                                                <p class="text-secondary mb-4">Tidak ada pesanan di kategori ini.</p>
                                                <a href="{{ route('product.index') }}" class="btn btn-black rounded-0 fw-bold px-4 py-2">MULAI BELANJA</a>
                                            </div>
                                        @endforelse
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- TAB: STATUS PESANAN --}}
                        <div class="tab-pane fade" id="content-status" role="tabpanel">
                            <div class="mb-4">
                                <h3 class="fw-bold text-uppercase m-0" style="font-size: 24px; letter-spacing: 1px;">Status Pesanan</h3>
                            </div>

                            <!-- Container Header Status -->
                            <div class="border border-dark p-4 mb-5 rounded-0" id="tracking-header" style="display: none;">
                                <div class="row g-3">
                                    <div class="col-6 col-md-3 border-end-md border-secondary-subtle">
                                        <p class="text-secondary mb-1" style="font-size: 12px; text-transform: uppercase;">Kurir</p>
                                        <p class="fw-bold mb-0 text-uppercase" style="font-size: 14px;" id="track-courier-name">-</p>
                                    </div>
                                    <div class="col-6 col-md-4 border-end-md border-secondary-subtle">
                                        <p class="text-secondary mb-1" style="font-size: 12px; text-transform: uppercase;">No. Resi</p>
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="fw-bold mb-0" style="font-size: 14px;" id="track-waybill-id">-</p>
                                            <button class="btn p-0 border-0 text-secondary hover-text-dark" title="Salin Resi"><i class="bi bi-copy"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <p class="text-secondary mb-1" style="font-size: 12px; text-transform: uppercase;">Status Saat Ini</p>
                                        <p class="fw-bold text-success mb-0 text-uppercase" style="font-size: 14px;" id="track-current-status">-</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Area Loading -->
                            <div id="tracking-loading" class="text-center py-5" style="display: none;">
                                <div class="spinner-border text-dark mb-3" role="status"></div>
                                <p class="fw-bold text-uppercase">Mengambil data perjalanan paket...</p>
                            </div>

                            <!-- Pesan Error -->
                            <div id="tracking-error" class="alert alert-danger rounded-0 border border-dark" style="display: none;">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i> <span id="tracking-error-text">Terjadi kesalahan.</span>
                            </div>

                            <!-- Container Timeline -->
                            <div class="position-relative ms-3 ms-md-4" id="tracking-timeline-container" style="display: none;">
                                <!-- Garis Vertikal -->
                                <div class="position-absolute bg-dark" style="left: 7px; top: 10px; bottom: 30px; width: 2px; z-index: 1;"></div>
                                
                                <!-- List History akan di-inject ke sini oleh JavaScript -->
                                <div id="tracking-history-list"></div>
                            </div>
                        </div>

                        {{-- TAB: KONTAK KAMI --}}
                        <div class="tab-pane fade" id="content-kontak" role="tabpanel">
                            <div class="mb-4"><h3 class="fw-bold text-uppercase m-0" style="font-size: 24px; letter-spacing: 1px;">Hubungi Kami</h3><p class="text-secondary mt-1" style="font-size: 13px;">Kami siap membantu Anda.</p></div>
                            <div class="row g-5">
                                <div class="col-12 col-xl-5">
                                    <div class="bg-light-gray p-4 p-md-5 border border-secondary-subtle rounded-0 h-100">
                                        <h5 class="fw-bold text-uppercase mb-4" style="font-size: 16px;">Informasi Kontak</h5>
                                        <div class="d-flex align-items-start mb-4"><i class="bi bi-geo-alt fs-5 me-3 text-dark"></i><div><h6 class="fw-bold mb-1" style="font-size: 13px;">Alamat Toko Utama</h6><p class="text-secondary mb-0" style="font-size: 12px;">Jl. Jenderal Sudirman No. 45, Tangerang</p></div></div>
                                        <div class="d-flex align-items-start mb-4"><i class="bi bi-envelope fs-5 me-3 text-dark"></i><div><h6 class="fw-bold mb-1" style="font-size: 13px;">Email</h6><p class="text-secondary mb-0" style="font-size: 12px;">support@bigsport.com</p></div></div>
                                        <div class="d-flex align-items-start"><i class="bi bi-telephone fs-5 me-3 text-dark"></i><div><h6 class="fw-bold mb-1" style="font-size: 13px;">WhatsApp</h6><p class="text-secondary mb-0" style="font-size: 12px;">+62 812 3456 7890</p></div></div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-7">
                                    <form action="#"><div class="row g-3">
                                        <div class="col-md-6"><label class="fw-bold mb-2 text-uppercase" style="font-size: 11px;">Nama</label><input type="text" class="form-control rounded-0 border-dark p-3" placeholder="John Doe"></div>
                                        <div class="col-md-6"><label class="fw-bold mb-2 text-uppercase" style="font-size: 11px;">Email</label><input type="email" class="form-control rounded-0 border-dark p-3" placeholder="john@example.com"></div>
                                        <div class="col-12"><label class="fw-bold mb-2 text-uppercase" style="font-size: 11px;">Pesan</label><textarea class="form-control rounded-0 border-dark p-3" rows="5" placeholder="Pesan Anda..."></textarea></div>
                                        <div class="col-12 mt-4"><button type="submit" class="btn btn-action-main w-100 m-0">KIRIM PESAN</button></div>
                                    </div></form>
                                </div>
                            </div>
                        </div>

                        {{-- TAB: LOKASI TOKO --}}
                        <div class="tab-pane fade" id="content-lokasi" role="tabpanel">
                            <h3 class="fw-bold mb-5 text-uppercase" style="letter-spacing: 0.5px;">INFORMASI LOKASI TOKO</h3>
                            @php
                                $locations = [
                                    ['title' => 'Bigsport Tangerang Selatan', 'addr' => 'Jl. HOS Cokroaminoto No.52, Larangan', 'map' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2111151819176!2d106.7421777!3d-6.2358797!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f1c3428125af%3A0x948466498a5b6768!2sBigsport%20Tangerang%20Selatan!5e0!3m2!1sen!2sid!4v1776976900153!5m2!1sen!2sid', 'link' => 'https://maps.app.goo.gl/rnMiEKk4Zsj1QvNY7'],
                                    ['title' => 'Bigsport Citra Raya', 'addr' => 'QG7F+2JW, Cikupa, Tangerang', 'map' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.199776241046!2d106.5240772!3d-6.2373785!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e4207ddf6ae715b%3A0x2c489afb42e19571!2sBig%20Sport%20Tangerang!5e0!3m2!1sen!2sid!4v1776977167727!5m2!1sen!2sid', 'link' => 'https://maps.app.goo.gl/5LmwmRAtm3d8YrQdA'],
                                ];
                            @endphp
                            @foreach($locations as $loc)
                                <div class="row g-4 mb-5 border-bottom pb-4">
                                    <div class="col-12 col-lg-6"><div class="rounded overflow-hidden border border-secondary-subtle"><iframe src="{{ $loc['map'] }}" width="100%" height="250" style="border:0;" loading="lazy"></iframe></div></div>
                                    <div class="col-12 col-lg-6 d-flex flex-column justify-content-center"><h5 class="fw-bold text-dark mb-3">{{ $loc['title'] }}</h5><p class="profile-info-text mb-4">{{ $loc['addr'] }}</p><a href="{{ $loc['link'] }}" target="_blank" class="btn btn-black px-4 py-2 fw-bold w-100">Buka di Google Maps</a></div>
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
    <script>

        document.addEventListener('DOMContentLoaded', function() {
    // 1. Ambil parameter dari URL
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');

    // 2. Jika parameter tab adalah 'orders'
    if (tabParam === 'orders') {
        const orderTabTrigger = document.getElementById('tab-pesanan');
        
        if (orderTabTrigger) {
            console.log("Membuka tab pesanan...");
            
            // 🔥 Trik jitu: Pakai constructor Tab Bootstrap agar animasi fade-nya jalan
            const tab = new bootstrap.Tab(orderTabTrigger);
            tab.show();
            
            // Scroll ke atas sedikit biar user langsung liat judul "Pesanan Saya"
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
});

        document.addEventListener('DOMContentLoaded', function() {
            // Logic Midtrans
            document.querySelectorAll('.btn-lanjut-bayar').forEach(btn => {
                btn.addEventListener('click', function() {
                    let token = this.getAttribute('data-token');
                    let originalText = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> MEMUAT...';
                    this.disabled = true;
                    
                    window.snap.pay(token, {
                        onSuccess: () => { window.location.reload(); }, // Langsung reload tanpa alert
                        onPending: () => { btn.innerHTML = originalText; btn.disabled = false; },
                        onError: () => { btn.innerHTML = originalText; btn.disabled = false; },
                        onClose: () => { btn.innerHTML = originalText; btn.disabled = false; }
                    });
                });
            });
        });

        // AJAX Set Utama (Tanpa Alert)
        function setMainAddress(id) {
            fetch(`/address/${id}/set-main`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    const selectedCard = document.getElementById(`address-card-${id}`);
                    const list = document.getElementById('address-list');
                    
                    document.querySelectorAll('.address-card').forEach(card => {
                        card.classList.replace('border-dark', 'border-secondary-subtle');
                        card.style.borderWidth = '1px';
                        const b = card.querySelector('.badge-utama'); if (b) b.remove();
                        const act = card.querySelector('.action-buttons');
                        const cid = card.getAttribute('data-id');
                        if (cid != id && act.innerHTML.trim() == "") {
                            act.innerHTML = `<div class="vr" style="height: 15px; width: 1px;"></div><button onclick="setMainAddress(${cid})" class="bg-transparent border-0 p-0 text-dark fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">SET UTAMA</button><div class="vr" style="height: 15px; width: 1px;"></div><button onclick="deleteAddress(${cid})" class="bg-transparent border-0 p-0 text-danger fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">HAPUS</button>`;
                        }
                    });
                    
                    selectedCard.classList.replace('border-secondary-subtle', 'border-dark');
                    selectedCard.style.borderWidth = '2px';
                    selectedCard.querySelector('.address-content').insertAdjacentHTML('afterbegin', '<span class="badge bg-dark text-white rounded-0 position-absolute top-0 end-0 px-3 py-2 badge-utama" style="font-size: 10px; letter-spacing: 1px;">UTAMA</span>');
                    selectedCard.querySelector('.action-buttons').innerHTML = '';
                    list.prepend(selectedCard);
                }
            });
        }

        // AJAX Hapus Alamat (Tanpa Confirm & Alert)
        function deleteAddress(id) {
            fetch(`/address/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    const card = document.getElementById(`address-card-${id}`);
                    card.style.transition = '0.3s'; 
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)'; // Tambahan efek visual biar smooth
                    setTimeout(() => card.remove(), 300);
                }
            });
        }

        moment.locale('id');

    function loadTrackingData(orderId) {
        // Pindah ke tab Status secara visual
        const statusTab = new bootstrap.Tab(document.querySelector('a[href="#content-status"]'));
        statusTab.show();

        // Siapkan tampilan UI (Tampilkan loading, sembunyikan yang lain)
        document.getElementById('tracking-header').style.display = 'none';
        document.getElementById('tracking-timeline-container').style.display = 'none';
        document.getElementById('tracking-error').style.display = 'none';
        document.getElementById('tracking-loading').style.display = 'block';

        // Panggil API buatan lu (ProfileController -> getTracking)
        axios.get(`/profile/order/${orderId}/tracking`)
            .then(response => {
                document.getElementById('tracking-loading').style.display = 'none';

                if (response.data.success) {
                    const data = response.data.data;
                    
                    // Isi Header Status
                    document.getElementById('track-courier-name').innerText = data.courier.company || 'Kurir Standar';
                    document.getElementById('track-waybill-id').innerText = data.courier.waybill_id || 'Menunggu Resi';
                    document.getElementById('track-current-status').innerText = data.status || 'Diproses';

                    // Isi Timeline History
                    const historyContainer = document.getElementById('tracking-history-list');
                    historyContainer.innerHTML = ''; // Bersihkan dulu

                    if (data.history && data.history.length > 0) {
                        // Looping data history dari yang paling baru
                        data.history.forEach((item, index) => {
                            const isLatest = index === 0; // Item paling atas
                            
                            // Format waktu: "25 Apr 2026, 09:30 WIB"
                            const timeFormatted = moment(item.updated_at).format('DD MMM YYYY, HH:mm') + ' WIB';

                            // Tentukan desain (Titik tebal untuk status terbaru, titik bolong untuk yang lama)
                            const dotStyle = isLatest 
                                ? 'bg-dark rounded-circle flex-shrink-0 mt-1 border border-white' 
                                : 'bg-white rounded-circle flex-shrink-0 mt-1 border border-dark';
                            
                            const dotSize = isLatest 
                                ? 'width: 16px; height: 16px; border-width: 3px !important; box-shadow: 0 0 0 2px #000;'
                                : 'width: 16px; height: 16px; border-width: 2px !important;';
                                
                            const textOpacity = isLatest ? '' : 'opacity-75';

                            const htmlRow = `
                                <div class="d-flex position-relative mb-5" style="z-index: 2;">
                                    <div class="${dotStyle}" style="${dotSize}"></div>
                                    <div class="ms-4 ${textOpacity}">
                                        <h6 class="fw-bold text-uppercase mb-1" style="font-size: 14px;">${item.status}</h6>
                                        <p class="text-secondary mb-1" style="font-size: 13px;">${item.note}</p>
                                        <span class="${isLatest ? 'fw-bold text-dark' : 'text-secondary fw-bold'}" style="font-size: 12px;">
                                            ${timeFormatted}
                                        </span>
                                    </div>
                                </div>
                            `;
                            historyContainer.insertAdjacentHTML('beforeend', htmlRow);
                        });
                    } else {
                        historyContainer.innerHTML = '<p class="text-secondary ms-4">Belum ada riwayat perjalanan paket.</p>';
                    }

                    // Tampilkan element
                    document.getElementById('tracking-header').style.display = 'block';
                    document.getElementById('tracking-timeline-container').style.display = 'block';

                } else {
                    // Muncul jika success = false (contoh: resi belum di-pick up)
                    document.getElementById('tracking-error-text').innerText = response.data.message;
                    document.getElementById('tracking-error').style.display = 'block';
                }
            })
            .catch(error => {
                document.getElementById('tracking-loading').style.display = 'none';
                
                // Menangkap pesan error asli dari server
                let errorMsg = 'Gagal menghubungi server.';
                if (error.response) {
                    // Jika server Laravel membalas dengan error (misal 404 atau 500)
                    errorMsg = `Error ${error.response.status}: Route tidak ditemukan atau ada masalah di Controller.`;
                    console.error("Detail Error:", error.response.data);
                }
                
                document.getElementById('tracking-error-text').innerText = errorMsg;
                document.getElementById('tracking-error').style.display = 'block';
            });
    }
    </script>
    @endpush
@endsection
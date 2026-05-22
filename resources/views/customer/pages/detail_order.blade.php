@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    @php
        $statusLabel = 'Status Tidak Diketahui';
        $statusIcon = 'bi-info-circle';
        
        if ($order->status == 'cancelled' || in_array($order->payment_status, ['failed', 'expired', 'refunded'])) {
            $statusLabel = 'Pesanan Dibatalkan';
            $statusIcon = 'bi-x-circle';
        } elseif (in_array($order->payment_status, ['unpaid', 'pending'])) {
            $statusLabel = 'Menunggu Pembayaran';
            $statusIcon = 'bi-wallet2';
        } else {
            switch ($order->status) {
                case 'pending': 
                    $statusLabel = 'Menunggu Konfirmasi'; 
                    $statusIcon = 'bi-hourglass-split';
                    break;
                case 'confirmed': 
                    $statusLabel = 'Sedang Dikemas'; 
                    $statusIcon = 'bi-box-seam';
                    break;
                case 'processing': 
                    $statusLabel = 'Sedang Dikirim'; 
                    $statusIcon = 'bi-truck';
                    break;
                case 'completed': 
                    $statusLabel = 'Pesanan Selesai'; 
                    $statusIcon = 'bi-check-circle-fill';
                    break;
            }
        }

        $isPickup = $order->shippingDetail && $order->shippingDetail->courier_company === 'pickup';
        $hasTracking = $order->shippingDetail && !empty($order->shippingDetail->tracking_number);
    @endphp

    <section class="py-4 py-lg-5 bg-white text-dark" style="min-height: 70vh;">
        <div class="container" style="max-width: 800px;">
            
            {{-- 1. BANNER STATUS --}}
            <div class="card rounded-0 mb-4 bg-black text-white border-0">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <i class="bi {{ $statusIcon }} fs-2 text-white"></i>
                    <div>
                        <h5 class="fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px;">{{ $statusLabel }}</h5>
                        @if(in_array($order->payment_status, ['unpaid', 'pending']) && $order->status != 'cancelled')
                            <p class="mb-0 text-white" style="font-size: 13px;">Selesaikan pembayaran Anda agar pesanan dapat segera diproses</p>
                        @elseif($order->status == 'confirmed')
                            <p class="mb-0 text-white" style="font-size: 13px;">Penjual sedang menyiapkan dan mengemas produk pesanan Anda</p>
                        @elseif($order->status == 'processing')
                            <p class="mb-0 text-white" style="font-size: 13px;">Pesanan Anda sedang dalam perjalanan kurir ekspedisi</p>
                        @elseif($order->status == 'completed')
                            <p class="mb-0 text-white" style="font-size: 13px;">Terima kasih telah berbelanja di Big Sport Tangerang</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. INFO PENGIRIMAN & KURIR --}}
            <div class="card rounded-0 border-secondary-subtle mb-4 shadow-sm bg-white">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-6 p-4 border-end-md border-secondary-subtle">
                            @if($isPickup)
                                <div class="d-flex flex-column justify-content-center h-100">
                                    <p class="text-dark fw-bold mb-1" style="font-size: 18px;">Ambil di Toko</p>
                                    <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.5;">
                                        <strong class="text-dark">Big Sport Tangerang (Toko Utama)</strong><br>
                                        Jl. HOS Cokroaminoto No.52, Larangan, Kota Tangerang
                                    </p>
                                </div>
                            @else
                                <h6 class="fw-bold text-uppercase mb-3" style="font-size: 12px; letter-spacing: 1px;">
                                    <i class="bi bi-geo-alt me-2 align-middle"></i> Alamat Pengiriman
                                </h6>
                                <div class="ps-1">
                                    @php
                                        $addressParts = explode(' | ', $order->shipping_address);
                                        $contact = $addressParts[0] ?? '-';
                                        $fullAddr = $addressParts[1] ?? $order->shipping_address;
                                    @endphp
                                    <p class="text-dark fw-bold mb-1" style="font-size: 13px;">{{ $contact }}</p>
                                    <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.6;">{{ $fullAddr }}</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-6 p-4">
                            @if($isPickup)
                                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                    <p class="text-dark fw-bold mb-2 text-uppercase text-center" style="font-size: 13px; letter-spacing: 1px;">KODE PENGAMBILAN</p>
                                    @if(in_array($order->status, ['confirmed', 'processing']))
                                        <div class="bg-white p-2 border border-secondary-subtle shadow-sm" id="qr-code-box">
                                            {!! QrCode::size(110)->generate($order->invoice_number) !!}
                                        </div>
                                        <p class="text-secondary mt-2 mb-2 text-center" style="font-size: 11px;">Tunjukkan kode ini ke kasir toko</p>
                                        <button type="button" onclick="downloadQRCode('{{ $order->invoice_number }}')" class="btn btn-outline-dark btn-sm rounded-0 fw-bold" style="font-size: 10px; padding: 5px 12px; letter-spacing: 0.5px;">
                                            <i class="bi bi-download me-1"></i> SIMPAN QR
                                        </button>
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center border border-secondary-subtle" style="width: 110px; height: 110px;">
                                            <i class="bi bi-lock text-secondary fs-1"></i>
                                        </div>
                                        <p class="text-secondary mt-2 mb-0 text-center" style="font-size: 11px;">
                                            {{ $order->payment_status == 'unpaid' ? 'Selesaikan pembayaran dahulu' : 'QR Code kedaluwarsa' }}
                                        </p>
                                    @endif
                                </div>
                            @else
                                <h6 class="fw-bold text-uppercase mb-3" style="font-size: 12px; letter-spacing: 1px;">
                                    <i class="bi bi-truck me-2 align-middle"></i> Informasi Ekspedisi
                                </h6>
                                <div class="ps-1">
                                    <p class="text-dark fw-bold mb-1 text-uppercase" style="font-size: 13px;">
                                        {{ $order->shippingDetail->courier_company ?? 'Kurir' }} - {{ $order->shippingDetail->courier_type ?? 'Reguler' }}
                                    </p>
                                    <p class="text-secondary mb-0" style="font-size: 13px;">No. Resi: <strong class="text-dark">{{ $order->shippingDetail->tracking_number ?? 'Belum tersedia' }}</strong></p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. RINCIAN PRODUK --}}
            <div class="card rounded-0 border-secondary-subtle mb-4 shadow-sm bg-white">
                <div class="card-body p-0">
                    @foreach($order->items as $item)
                        @php
                            $imagePath = null;
                            $brandName = null;
                            $genderName = null;
                            if ($item->sku && $item->sku->product) {
                                $brandName = $item->sku->product->brand->name ?? null;
                                $genderName = $item->sku->product->gender ?? null;
                                if ($item->sku->product->images->isNotEmpty()) {
                                    $primaryImg = $item->sku->product->images->where('is_primary', true)->first() ?? $item->sku->product->images->first();
                                    if ($primaryImg) $imagePath = 'storage/' . $primaryImg->image_path;
                                }
                            }
                        @endphp
                        <div class="d-flex align-items-start p-4 {{ !$loop->last ? 'border-bottom border-secondary-subtle' : '' }}">
                            <div class="ratio ratio-1x1 border border-secondary-subtle flex-shrink-0 bg-light me-3" style="width: 70px;">
                                @if($imagePath) 
                                    <img src="{{ asset($imagePath) }}" class="object-fit-cover w-100 h-100" alt="{{ $item->product_name }}">
                                @else 
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary bg-light"><i class="bi bi-image text-muted"></i></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="pe-3">
                                        @if($brandName || $genderName)
                                            <p class="text-secondary fw-bold mb-0 text-uppercase" style="font-size: 10px; letter-spacing: 0.5px;">
                                                {{ $brandName }} {{ $brandName && $genderName ? '|' : '' }} {{ $genderName }}
                                            </p>
                                        @endif
                                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">{{ $item->product_name }}</h6>
                                    </div>
                                    <span class="fw-bold text-nowrap text-dark" style="font-size: 13px;">Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-secondary mb-1" style="font-size: 12px;">Variasi: {{ $item->product_size ?? '-' }}</p>
                                <p class="text-secondary mb-0" style="font-size: 12px;">x{{ $item->quantity }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 4. RINCIAN PESANAN & PEMBAYARAN --}}
            <div class="card rounded-0 border-secondary-subtle mb-4 shadow-sm bg-white">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-6 p-4 border-end-md border-secondary-subtle">
                            <h6 class="fw-bold text-uppercase mb-3" style="font-size: 12px; letter-spacing: 1px;">Rincian Transaksi</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary" style="font-size: 12px;">No. Pesanan</span>
                                <span class="fw-bold text-dark" style="font-size: 12px;">#{{ $order->invoice_number }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary" style="font-size: 12px;">Waktu Pemesanan</span>
                                <span class="text-dark" style="font-size: 12px;">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, HH:i') }} WIB</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary" style="font-size: 12px;">Metode Pembayaran</span>
                                <span class="text-dark text-uppercase fw-bold" style="font-size: 12px;">{{ $order->payment->payment_type ?? 'Belum Dipilih' }}</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 p-4">
                            <h6 class="fw-bold text-uppercase mb-3" style="font-size: 12px; letter-spacing: 1px;">Rincian Pembayaran</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary" style="font-size: 12px;">Subtotal Produk</span>
                                <span class="text-dark" style="font-size: 12px;">Rp {{ number_format($order->total_product_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary" style="font-size: 12px;">Total Ongkos Kirim</span>
                                <span class="text-dark" style="font-size: 12px;">Rp {{ number_format($order->total_shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary" style="font-size: 12px;">Diskon / Promo</span>
                                <span class="text-dark fw-bold" style="font-size: 12px;">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <hr class="border-secondary-subtle my-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-secondary" style="font-size: 12px;">Total Belanja:</span>
                                <span class="fw-bold text-dark" style="font-size: 13px;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🌟 LIVE PANEL TRACKING (Akan terbuka otomatis di bawah rincian saat di-klik) 🌟 --}}
            <div id="live-tracking-section" class="card rounded-0 border-secondary-subtle mb-4 shadow-sm bg-white d-none">
                <div class="card-header bg-light fw-bold small text-uppercase py-3">
                    <i class="bi bi-clock-history me-1"></i> Riwayat Perjalanan Paket
                </div>
                <div class="card-body p-4 position-relative">
                    <div id="tracking-loading-inline" class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-dark me-2"></div> Memuat perjalanan paket...
                    </div>
                    <div id="tracking-error-inline" class="alert alert-warning rounded-0 small d-none"></div>
                    <div id="tracking-history-list-inline" class="position-relative" style="border-left: 2px solid #dee2e6; margin-left: 10px;">
                        {{-- Data Row dimasukkan via JS --}}
                    </div>
                </div>
            </div>

            {{-- 5. TOMBOL AKSI LENGKAP --}}
            <div class="d-flex flex-column gap-2 w-100">
                @php
                    $cancelDeadline = \Carbon\Carbon::parse($order->created_at)->addHours(1);
                    $canCancel = now()->lessThan($cancelDeadline);
                @endphp

                {{-- KODE BARU YANG LEBIH AMAN --}}
                @php
                            $cancelDeadline = \Carbon\Carbon::parse($order->created_at)->addHours(12);
                            $canCancel = now()->lessThan($cancelDeadline);
                            
                            // Kita buat variabel khusus untuk ngecek apakah resi MASIH KOSONG
                            $belumAdaResi = (!$order->shippingDetail || empty($order->shippingDetail->tracking_number));
                        @endphp

                        {{-- Batalkan Pesanan --}}
                        @if(in_array($order->status, ['pending', 'confirmed']) && $order->status != 'cancelled')
                            
                            {{-- Syarat Baru: Boleh batal JIKA (waktu < 1 jam) DAN (resi belum ada) --}}
                            @if($canCancel && $belumAdaResi)
                                <button type="button" onclick="openCancelModal({{ $order->id }})" class="btn btn-outline-danger fw-bold text-uppercase rounded-0 py-2.5 w-100" style="font-size: 12px; letter-spacing: 0.5px;">Batalkan Pesanan</button>
                            @endif

                        @endif

                {{-- Hubungi Penjual --}}
                @if($order->payment_status == 'paid')
                    <a href="{{ route('chatbot', ['invoice' => $order->invoice_number]) }}" class="btn btn-outline-dark fw-bold text-uppercase rounded-0 py-2.5 text-center w-100" style="font-size: 12px; letter-spacing: 0.5px;">
                        <i class="bi bi-chat-dots me-1"></i> Hubungi Penjual
                    </a>
                @endif

                {{-- Bayar Sekarang --}}
                @if(in_array($order->payment_status, ['unpaid', 'pending']) && $order->status != 'cancelled')
                    <button type="button" class="btn btn-dark fw-bold text-uppercase btn-lanjut-bayar rounded-0 py-2.5 w-100" style="font-size: 12px; letter-spacing: 0.5px;" data-token="{{ $order->snap_token }}">Bayar Sekarang</button>
                @endif
                
                {{-- Lacak Pesanan (Arahkan ke halaman khusus) --}}
                @if(in_array($order->status, ['processing', 'completed']) && !$isPickup && $hasTracking)
                    <a href="{{ route('order.track', $order->id) }}" class="btn btn-outline-dark fw-bold text-uppercase rounded-0 py-2.5 text-center w-100 d-block" style="font-size: 12px; letter-spacing: 0.5px;">
                        Lacak Pesanan
                    </a>
                @endif

                {{-- Pesanan Diterima (Fungsi Aktif) --}}
                @if($order->status == 'processing' && !$isPickup && $hasTracking)
                    <button type="button" onclick="submitOrderReceived({{ $order->id }})" id="btn-complete-order" class="btn btn-dark fw-bold text-uppercase rounded-0 py-2.5 w-100" style="font-size: 12px; letter-spacing: 0.5px;">Pesanan Diterima</button>
                @endif
                
                {{-- Selesai: Beri Ulasan & Beli Lagi (Fungsi Aktif) --}}
                @if($order->status == 'completed')
                    <a href="{{ route('order.buy-again', $order->id) }}" class="btn btn-dark fw-bold text-uppercase rounded-0 py-2.5 text-center w-100" style="font-size: 12px; letter-spacing: 0.5px;">Beli Lagi</a>
                @endif
                
                @if($order->status == 'cancelled' || in_array($order->payment_status, ['failed', 'expired']))
                    <a href="{{ route('order.buy-again', $order->id) }}" class="btn btn-outline-dark fw-bold text-uppercase rounded-0 py-2.5 text-center w-100" style="font-size: 12px; letter-spacing: 0.5px;">Beli Lagi</a>
                @endif
            </div>
        </div>

        {{-- POP-UP MODAL ALASAN PEMBATALAN PESANAN --}}
        <div class="modal fade" id="cancelOrderModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered rounded-0">
                <div class="modal-content rounded-0 border-dark">
                    <div class="modal-header bg-light border-bottom border-secondary-subtle py-3">
                        <h6 class="modal-title fw-bold text-uppercase m-0" style="letter-spacing: 0.5px; font-size: 13px;">Alasan Pembatalan Pesanan</h6>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-secondary mb-3" style="font-size: 13px;">Silakan pilih alasan pembatalan Anda. Aksi ini akan membatalkan pesanan dan mengembalikan stok produk otomatis.</p>
                        <input type="hidden" id="modalCancelOrderId">
                        
                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-center gap-2 p-2 border border-secondary-subtle style-reason" style="cursor: pointer; font-size: 13px;">
                                <input type="radio" name="cancel_reason" value="Ingin mengubah rincian pesanan (ukuran, warna, alamat)" checked class="form-check-input border-dark shadow-none">
                                <span>Ingin mengubah rincian pesanan (ukuran, warna, alamat)</span>
                            </label>
                            
                            <label class="d-flex align-items-center gap-2 p-2 border border-secondary-subtle style-reason" style="cursor: pointer; font-size: 13px;">
                                <input type="radio" name="cancel_reason" value="Penjual tidak membalas chat / Kurang responsif" class="form-check-input border-dark shadow-none">
                                <span>Penjual tidak membalas chat / Kurang responsif</span>
                            </label>
                            
                            <label class="d-flex align-items-center gap-2 p-2 border border-secondary-subtle style-reason" style="cursor: pointer; font-size: 13px;">
                                <input type="radio" name="cancel_reason" value="Menemukan harga yang lebih murah di toko lain" class="form-check-input border-dark shadow-none">
                                <span>Menemukan harga yang lebih murah di toko lain</span>
                            </label>
                            
                            <label class="d-flex align-items-center gap-2 p-2 border border-secondary-subtle style-reason" style="cursor: pointer; font-size: 13px;">
                                <input type="radio" name="cancel_reason" value="Waktu pengiriman terlalu lama" class="form-check-input border-dark shadow-none">
                                <span>Waktu pengiriman terlalu lama</span>
                            </label>
                            
                            <label class="d-flex align-items-center gap-2 p-2 border border-secondary-subtle style-reason" style="cursor: pointer; font-size: 13px;">
                                <input type="radio" name="cancel_reason" value="Berubah pikiran / Tidak jadi membeli" class="form-check-input border-dark shadow-none">
                                <span>Berubah pikiran / Tidak jadi membeli</span>
                            </label>

                            <label class="d-flex align-items-center gap-2 p-2 border border-secondary-subtle style-reason" style="cursor: pointer; font-size: 13px;">
                                <input type="radio" name="cancel_reason" value="Lainnya" class="form-check-input border-dark shadow-none">
                                <span>Lainnya</span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top border-light p-3">
                        <button type="button" class="btn btn-outline-dark fw-bold text-uppercase rounded-0" style="font-size: 11px; padding: 10px 20px;" data-bs-dismiss="modal">Kembali</button>
                        <button type="button" id="btnConfirmCancel" onclick="submitCancelOrder()" class="btn btn-dark fw-bold text-uppercase rounded-0" style="font-size: 11px; padding: 10px 20px;">Konfirmasi Batalkan</button>
                    </div>
                </div>
            </div>
        </div>

        

    </section>

    @include('customer.components.footer')
    @include('customer.components.chatbot')

    @push('styles')
    <style>
        .border-end-md { border-right: 1px solid #dee2e6; }
        @media (max-width: 768px) { .border-end-md { border-right: none; border-bottom: 1px solid #dee2e6; } }
    </style>
    @endpush

    @push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    
    <script>
        moment.locale('id');

        // ==========================================
        // 1. LIVE INLINE TRACKING LOGIC (API)
        // ==========================================
        function toggleInlineTracking(orderId) {
            const trackBox = document.getElementById('live-tracking-section');
            if (!trackBox.classList.contains('d-none')) {
                trackBox.classList.add('d-none');
                return;
            }
            
            trackBox.classList.remove('d-none');
            document.getElementById('tracking-error-inline').classList.add('d-none');
            document.getElementById('tracking-history-list-inline').innerHTML = '';
            document.getElementById('tracking-loading-inline').style.display = 'block';

            axios.get(`/profile/order/${orderId}/tracking`)
                .then(response => {
                    document.getElementById('tracking-loading-inline').style.display = 'none';
                    if (response.data.success) {
                        const data = response.data.data;
                        const listContainer = document.getElementById('tracking-history-list-inline');

                        if (data.history && data.history.length > 0) {
                            data.history.forEach((item, index) => {
                                const isLatest = index === 0;
                                const timeFormatted = moment(item.updated_at).format('DD MMM YYYY, HH:mm') + ' WIB';
                                
                                const htmlRow = `
                                    <div class="position-relative mb-4 ps-4 pb-1">
                                        <div class="rounded-circle bg-white border border-dark" style="width:12px; height:12px; left:-7px; top:4px; position:absolute; border-width: 2px !important; ${isLatest ? 'background-color:#000 !important;' : ''}"></div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-uppercase small" style="font-size:12px;">${item.status}</h6>
                                            <p class="text-secondary mb-1 small" style="font-size:12px;">${item.note}</p>
                                            <span class="text-muted small" style="font-size:10px;"><i class="bi bi-clock me-1"></i>${timeFormatted}</span>
                                        </div>
                                    </div>`;
                                listContainer.insertAdjacentHTML('beforeend', htmlRow);
                            });
                        } else {
                            listContainer.innerHTML = '<p class="text-secondary small p-3 m-0">Belum ada riwayat pengiriman.</p>';
                        }
                    } else {
                        document.getElementById('tracking-error-inline').innerText = response.data.message;
                        document.getElementById('tracking-error-inline').classList.remove('d-none');
                    }
                }).catch(() => {
                    document.getElementById('tracking-loading-inline').style.display = 'none';
                    document.getElementById('tracking-error-inline').innerText = 'Gagal memuat status pelacakan paket.';
                    document.getElementById('tracking-error-inline').classList.remove('d-none');
                });
        }

        // ==========================================
        // 2. PESANAN DITERIMA LOGIC
        // ==========================================
        function submitOrderReceived(orderId) {
            if (confirm('Apakah Anda yakin paket telah diterima dengan aman dan ingin menyelesaikan pesanan ini?')) {
                const btn = document.getElementById('btn-complete-order');
                btn.disabled = true;
                btn.innerText = 'MEMPROSES...';

                axios.post(`/profile/order/${orderId}/complete`, {
                    _token: '{{ csrf_token() }}'
                })
                .then(res => {
                    if (res.data.success) {
                        window.location.reload();
                    } else {
                        alert(res.data.message);
                        btn.disabled = false;
                        btn.innerText = 'Pesanan Diterima';
                    }
                }).catch(() => {
                    alert('Terjadi kesalahan koneksi server.');
                    btn.disabled = false;
                    btn.innerText = 'Pesanan Diterima';
                });
            }
        }

        // ==========================================
        // 3. MODAL REVIEW & ULASAN LOGIC
        // ==========================================
        function openReviewModal() {
            const myModal = new bootstrap.Modal(document.getElementById('reviewOrderModal'));
            myModal.show();
        }

        function submitReviewProduk(e) {
            e.preventDefault();
            const form = document.getElementById('form-review-produk');
            const submitBtn = document.getElementById('btnSubmitReview');
            
            submitBtn.disabled = true;
            submitBtn.innerText = 'MENGIRIM...';

            const payload = {
                product_sku_id: document.getElementById('review-product-sku').value,
                rating: form.rating.value,
                comment: form.comment.value,
                _token: '{{ csrf_token() }}'
            };

            axios.post("{{ route('order.review', $order->id) }}", payload)
                .then(res => {
                    if (res.data.success) {
                        alert(res.data.message);
                        window.location.reload();
                    }
                }).catch(() => {
                    alert('Gagal mengirim ulasan.');
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Kirim Ulasan';
                });
        }

        // ==========================================
        // 4. KODE SIMPAN QR DOWNLOAD KE PNG (TETAP AKTIF)
        // ==========================================
        function downloadQRCode(invoiceNumber) {
            const svgElement = document.querySelector('#qr-code-box svg');
            if (!svgElement) return;

            const serializer = new XMLSerializer();
            let svgString = serializer.serializeToString(svgElement);
            const svgBlob = new Blob([svgString], { type: "image/svg+xml;charset=utf-8" });
            const URL = window.URL || window.webkitURL || window;
            const blobURL = URL.createObjectURL(svgBlob);

            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const padding = 40;
                canvas.width = img.width + (padding * 2);
                canvas.height = img.height + (padding * 2);
                const ctx = canvas.getContext('2d');

                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, padding, padding);

                const pngUrl = canvas.toDataURL("image/png");
                const downloadLink = document.createElement("a");
                downloadLink.href = pngUrl;
                downloadLink.download = "QR-" + invoiceNumber + ".png";
                
                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
                URL.revokeObjectURL(blobURL);
            };
            img.src = blobURL;
        }

        // MIDTRANS & CANCEL ORDER LOGIC (Bawaan lama Anda tetap aman utuh)
        function openCancelModal(orderId) {
            document.getElementById('modalCancelOrderId').value = orderId;
            new bootstrap.Modal(document.getElementById('cancelOrderModal')).show();
        }


        function submitCancelOrder() {
            const orderId = document.getElementById('modalCancelOrderId').value;
            
            // Mengambil nilai alasan pembatalan yang dipilih
            const selectedReason = document.querySelector('input[name="cancel_reason"]:checked').value;
            
            const confirmBtn = document.getElementById('btnConfirmCancel');
            let originalText = 'Konfirmasi Batalkan';
            
            if (confirmBtn) {
                originalText = confirmBtn.innerHTML;
                confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> MEMBATALKAN...';
                confirmBtn.disabled = true;
            }

            axios.post(`/profile/order/${orderId}/cancel-manual`, {
                _token: '{{ csrf_token() }}',
                reason: selectedReason // Mengirimkan alasan ke backend
            }, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.data.success) {
                    // Jika sukses, arahkan kembali ke tab pesanan
                    window.location.href = "{{ route('profile') }}?tab=orders";
                } else {
                    alert(response.data.message || 'Gagal membatalkan pesanan.');
                    if (confirmBtn) {
                        confirmBtn.innerHTML = originalText;
                        confirmBtn.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error("Cancel Error:", error);
                alert('Terjadi kesalahan koneksi server.');
                if (confirmBtn) {
                    confirmBtn.innerHTML = originalText;
                    confirmBtn.disabled = false;
                }
            });
        }
    </script>
    @endpush
@endsection
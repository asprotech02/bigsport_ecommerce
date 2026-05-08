@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    @php
        // LOGIKA STATUS (Sama dengan di halaman Profile agar konsisten)
        $statusLabel = 'Status Tidak Diketahui';
        $statusClass = 'bg-light text-dark border';
        $statusIcon = 'bi-info-circle';
        
        if ($order->status == 'cancelled' || in_array($order->payment_status, ['failed', 'expired', 'refunded'])) {
            $statusLabel = 'Pesanan Dibatalkan';
            $statusClass = 'bg-secondary text-white';
            $statusIcon = 'bi-x-circle';
        } elseif (in_array($order->payment_status, ['unpaid', 'pending'])) {
            $statusLabel = 'Menunggu Pembayaran';
            $statusClass = 'bg-danger text-white';
            $statusIcon = 'bi-wallet2';
        } else {
            switch ($order->status) {
                case 'pending': 
                    $statusLabel = 'Menunggu Konfirmasi'; 
                    $statusClass = 'bg-warning text-dark'; 
                    $statusIcon = 'bi-hourglass-split';
                    break;
                case 'confirmed': 
                    $statusLabel = 'Pesanan Dikonfirmasi'; 
                    $statusClass = 'bg-info text-dark'; 
                    $statusIcon = 'bi-check-circle';
                    break;
                case 'processing': 
                    $statusLabel = 'Sedang Diproses / Dikirim'; 
                    $statusClass = 'bg-primary text-white'; 
                    $statusIcon = 'bi-truck';
                    break;
                case 'completed': 
                    $statusLabel = 'Pesanan Selesai'; 
                    $statusClass = 'bg-success text-white'; 
                    $statusIcon = 'bi-box-seam';
                    break;
            }
        }

        $isPickup = $order->shippingDetail && $order->shippingDetail->courier_company === 'pickup';
        $hasTracking = $order->shippingDetail && !empty($order->shippingDetail->tracking_number);
    @endphp

    <section class="py-4 py-lg-5 bg-light" style="min-height: 70vh;">
        <div class="container" style="max-width: 800px;">
            
            {{-- Tombol Kembali --}}
            <div class="mb-4">
                <a href="{{ route('profile', ['tab' => 'orders']) }}" class="text-dark text-decoration-none fw-bold" style="font-size: 13px;">
                    <i class="bi bi-arrow-left me-1"></i> KEMBALI KE DAFTAR PESANAN
                </a>
            </div>

            {{-- 1. BANNER STATUS --}}
            <div class="card rounded-0 border-0 mb-3 shadow-sm {{ $statusClass }}">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <i class="bi {{ $statusIcon }} display-5 opacity-75"></i>
                    <div>
                        <h4 class="fw-bold mb-1 text-uppercase">{{ $statusLabel }}</h4>
                        @if(in_array($order->payment_status, ['unpaid', 'pending']) && $order->status != 'cancelled')
                            <p class="mb-0" style="font-size: 13px;">Selesaikan pembayaran Anda agar pesanan dapat segera diproses.</p>
                        @elseif($order->status == 'processing')
                            <p class="mb-0" style="font-size: 13px;">Pesanan Anda sedang dalam proses pengiriman atau siap diambil.</p>
                        @elseif($order->status == 'completed')
                            <p class="mb-0" style="font-size: 13px;">Terima kasih telah berbelanja di Big Sport Tangerang!</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. INFO PENGIRIMAN & KURIR --}}
            <div class="card rounded-0 border-secondary-subtle mb-3 shadow-sm">
                <div class="card-body p-0">
                    <div class="row g-0">
                        {{-- Alamat --}}
                        <div class="col-md-6 p-4 border-end-md border-secondary-subtle">
                            <h6 class="fw-bold text-uppercase mb-3" style="font-size: 13px; letter-spacing: 0.5px;">
                                <i class="bi bi-geo-alt me-2 fs-5 align-middle"></i> Alamat Pengiriman
                            </h6>
                            @if($isPickup)
                                <p class="text-dark fw-bold mb-1" style="font-size: 14px;">Ambil di Toko (Self Pickup)</p>
                                <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.5;">
                                    Silakan ambil pesanan Anda di cabang: <br>
                                    <strong class="text-dark">{{ strtoupper($order->shippingDetail->courier_type ?? 'Pusat') }}</strong>
                                </p>
                            @else
                                <div class="ps-1">
                                    @php
                                        // Memecah snapshot alamat: "Nama (HP) | Alamat Lengkap"
                                        $addressParts = explode(' | ', $order->shipping_address);
                                        $contact = $addressParts[0] ?? '-';
                                        $fullAddr = $addressParts[1] ?? $order->shipping_address;
                                    @endphp
                                    <p class="text-dark fw-bold mb-1" style="font-size: 14px;">{{ $contact }}</p>
                                    <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.5;">{{ $fullAddr }}</p>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Kurir --}}
                        <div class="col-md-6 p-4">
                            <h6 class="fw-bold text-uppercase mb-3" style="font-size: 13px; letter-spacing: 0.5px;">
                                <i class="bi bi-truck me-2 fs-5 align-middle"></i> Informasi Ekspedisi
                            </h6>
                            <div class="ps-1">
                                @if($isPickup)
                                    <p class="text-dark fw-bold mb-1 text-uppercase" style="font-size: 14px;">PENGAMBILAN SENDIRI</p>
                                    <p class="text-secondary mb-0" style="font-size: 13px;">Tunjukkan Invoice / QR Code saat di kasir.</p>
                                @else
                                    <p class="text-dark fw-bold mb-1 text-uppercase" style="font-size: 14px;">
                                        {{ $order->shippingDetail->courier_company ?? 'Kurir' }} - {{ $order->shippingDetail->courier_type ?? 'Reguler' }}
                                    </p>
                                    <p class="text-secondary mb-1" style="font-size: 13px;">No. Resi: <strong class="text-dark">{{ $order->shippingDetail->tracking_number ?? 'Belum tersedia' }}</strong></p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. RINCIAN PRODUK --}}
            <div class="card rounded-0 border-secondary-subtle mb-3 shadow-sm">
                <div class="card-header bg-white border-bottom border-secondary-subtle py-3 px-4 d-flex align-items-center gap-2">
                    <i class="bi bi-shop fs-5 text-dark"></i>
                    <span class="fw-bold text-uppercase" style="font-size: 14px; letter-spacing: 0.5px;">Big Sport Tangerang</span>
                </div>
                <div class="card-body p-0">
                    @foreach($order->items as $item)
                        @php
                            $imagePath = null;
                            if ($item->sku && $item->sku->product && $item->sku->product->images->isNotEmpty()) {
                                $primaryImg = $item->sku->product->images->where('is_primary', true)->first() ?? $item->sku->product->images->first();
                                if ($primaryImg) $imagePath = 'storage/' . $primaryImg->image_path;
                            }
                        @endphp
                        <div class="d-flex align-items-start p-4 {{ !$loop->last ? 'border-bottom border-secondary-subtle' : '' }}">
                            <div class="ratio ratio-1x1 border border-secondary-subtle flex-shrink-0 bg-light me-3" style="width: 80px;">
                                @if($imagePath) 
                                    <img src="{{ asset($imagePath) }}" class="object-fit-cover w-100 h-100" alt="{{ $item->product_name }}">
                                @else 
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary"><i class="bi bi-image fs-4"></i></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="fw-bold mb-1 pe-3" style="font-size: 15px;">{{ $item->product_name }}</h6>
                                    <span class="fw-bold text-nowrap" style="font-size: 14px;">Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</span>
                                </div>
                                <p class="text-secondary mb-1" style="font-size: 13px;">Variasi: {{ $item->product_size ?? '-' }}</p>
                                <p class="text-secondary mb-0" style="font-size: 13px;">x{{ $item->quantity }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 4. RINCIAN PESANAN & PEMBAYARAN --}}
            <div class="card rounded-0 border-secondary-subtle mb-4 shadow-sm">
                <div class="card-body p-0">
                    <div class="row g-0">
                        {{-- Info Order --}}
                        <div class="col-md-6 p-4 border-end-md border-secondary-subtle">
                            <h6 class="fw-bold text-uppercase mb-3" style="font-size: 13px; letter-spacing: 0.5px;">Rincian Pesanan</h6>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary" style="font-size: 13px;">No. Pesanan</span>
                                <span class="fw-bold text-dark" style="font-size: 13px;">#{{ $order->invoice_number }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary" style="font-size: 13px;">Waktu Pemesanan</span>
                                <span class="text-dark" style="font-size: 13px;">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, HH:i') }} WIB</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary" style="font-size: 13px;">Metode Pembayaran</span>
                                <span class="text-dark text-uppercase fw-bold" style="font-size: 13px;">
                                    {{ $order->payment->payment_type ?? 'Belum Dipilih' }}
                                </span>
                            </div>
                            @if(in_array($order->payment_status, ['paid', 'settlement']))
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary" style="font-size: 13px;">Waktu Pembayaran</span>
                                <span class="text-dark" style="font-size: 13px;">{{ \Carbon\Carbon::parse($order->payment->updated_at)->format('d M Y, HH:i') }} WIB</span>
                            </div>
                            @endif
                        </div>
                        
                        {{-- Kalkulasi Harga --}}
                        <div class="col-md-6 p-4 bg-light">
                            <h6 class="fw-bold text-uppercase mb-3" style="font-size: 13px; letter-spacing: 0.5px;">Rincian Pembayaran</h6>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary" style="font-size: 13px;">Subtotal Produk</span>
                                <span class="text-dark" style="font-size: 13px;">Rp {{ number_format($order->total_product_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary" style="font-size: 13px;">Total Ongkos Kirim</span>
                                <span class="text-dark" style="font-size: 13px;">Rp {{ number_format($order->total_shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary" style="font-size: 13px;">Diskon / Promo</span>
                                <span class="text-danger" style="font-size: 13px;">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            
                            <hr class="border-secondary-subtle my-3">
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark text-uppercase" style="font-size: 14px;">Total Belanja</span>
                                <span class="fw-bold text-danger fs-4">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. TOMBOL AKSI (Sama seperti di tab profile) --}}
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                @php
                    $cancelDeadline = \Carbon\Carbon::parse($order->created_at)->addHours(1);
                    $canCancel = now()->lessThan($cancelDeadline);
                @endphp

                @if(in_array($order->status, ['pending', 'confirmed']) && $order->status != 'cancelled')
                    @if($canCancel)
                        <button type="button" class="btn btn-outline-danger fw-bold text-uppercase rounded-0 px-4 py-2" style="font-size: 12px; letter-spacing: 0.5px;">Batalkan Pesanan</button>
                    @endif
                @endif

                @if(in_array($order->payment_status, ['unpaid', 'pending']) && $order->status != 'cancelled')
                    <button type="button" class="btn btn-danger fw-bold text-uppercase btn-lanjut-bayar rounded-0 px-4 py-2" style="font-size: 12px; letter-spacing: 0.5px;" data-token="{{ $order->snap_token }}">Bayar Sekarang</button>
                @endif

                @if($order->status == 'processing' && !$isPickup && $hasTracking)
                    <a href="{{ route('profile', ['tab' => 'orders']) }}" class="btn btn-dark fw-bold text-uppercase rounded-0 px-4 py-2" style="font-size: 12px; letter-spacing: 0.5px;">Lacak di Profil</a>
                    <button type="button" class="btn btn-success fw-bold text-uppercase rounded-0 px-4 py-2" style="font-size: 12px; letter-spacing: 0.5px;">Pesanan Diterima</button>
                @endif

                @if($order->status == 'completed')
                    <a href="{{ route('order.invoice', $order->id) }}" class="btn btn-outline-dark fw-bold text-uppercase rounded-0 px-4 py-2" style="font-size: 12px; letter-spacing: 0.5px;"><i class="bi bi-file-earmark-pdf me-1"></i> Cetak Invoice</a>
                    <a href="{{ route('product.index') }}" class="btn btn-dark fw-bold text-uppercase rounded-0 px-4 py-2" style="font-size: 12px; letter-spacing: 0.5px;">Beli Lagi</a>
                @endif
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
    <script>
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
    </script>
    @endpush
@endsection
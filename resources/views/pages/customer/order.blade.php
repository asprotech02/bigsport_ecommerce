@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        <x-customer.topbar />
        <x-customer.navbar />
    </header>

    <section class="py-4 py-lg-5 bg-white" style="min-height: 70vh;">
        <div class="container">
            <div class="row g-5 g-lg-5">
                
                <x-customer.profile_menu />

                <div class="col-12 col-lg-9 ps-lg-5">
                    <div class="mb-4">
                        <h3 class="fw-bold text-uppercase m-0" style="font-size: 24px; letter-spacing: 1px;">Pesanan Saya</h3>
                    </div>

                    {{-- TAB NAVIGASI --}}
                    <ul class="nav border-bottom border-secondary-subtle mb-4 gap-3 gap-md-4 d-flex flex-nowrap overflow-x-auto thumbnail-scroll" id="orderTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 active text-dark opacity-75 hover-opacity-100 text-nowrap" id="semua-tab" data-bs-toggle="tab" data-bs-target="#semua" type="button" role="tab">Semua</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" id="belum-bayar-tab" data-bs-toggle="tab" data-bs-target="#belum-bayar" type="button" role="tab">Belum Bayar</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" id="dikemas-tab" data-bs-toggle="tab" data-bs-target="#dikemas" type="button" role="tab">Dikemas</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" id="dikirim-tab" data-bs-toggle="tab" data-bs-target="#dikirim" type="button" role="tab">Dikirim</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" id="selesai-tab" data-bs-toggle="tab" data-bs-target="#selesai" type="button" role="tab">Selesai</button>
                        </li>
                    </ul>

                    {{-- LOGIKA FILTER TAB SECARA DINAMIS --}}
                    @php
                        $tabData = [
                            'semua' => $orders,
                            'belum-bayar' => $orders->where('payment_status', 'unpaid'),
                            'dikemas' => $orders->whereIn('status', ['pending', 'processing'])->where('payment_status', 'paid'),
                            'dikirim' => $orders->where('status', 'shipped'),
                            'selesai' => $orders->where('status', 'completed'),
                        ];
                    @endphp

                    <div class="tab-content" id="orderTabsContent">
                        @foreach($tabData as $tabId => $tabOrders)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel">
                                
                                @forelse($tabOrders as $order)
                                    <div class="border border-dark rounded-0 p-3 p-md-4 mb-4">
                                        {{-- HEADER CARD --}}
                                        <div class="d-flex flex-column flex-md-row justify-content-between border-bottom border-secondary-subtle pb-3 mb-3 gap-2">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="fw-bold text-uppercase" style="font-size: 13px;">{{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d M Y') }}</span>
                                                
                                                {{-- BADGE STATUS --}}
                                                @if($order->payment_status == 'unpaid')
                                                    <span class="badge bg-danger rounded-0 text-uppercase px-2 py-1" style="letter-spacing: 0.5px; font-weight: 600;">BELUM BAYAR</span>
                                                @elseif($order->status == 'pending' || $order->status == 'processing')
                                                    <span class="badge bg-info text-dark rounded-0 text-uppercase px-2 py-1" style="letter-spacing: 0.5px; font-weight: 600;">DIKEMAS</span>
                                                @elseif($order->status == 'shipped')
                                                    <span class="badge bg-primary rounded-0 text-uppercase px-2 py-1" style="letter-spacing: 0.5px; font-weight: 600;">DIKIRIM</span>
                                                @elseif($order->status == 'completed')
                                                    <span class="badge bg-success rounded-0 text-uppercase px-2 py-1" style="letter-spacing: 0.5px; font-weight: 600;">SELESAI</span>
                                                @else
                                                    <span class="badge bg-secondary rounded-0 text-uppercase px-2 py-1">{{ $order->status }}</span>
                                                @endif
                                                
                                                <span class="text-secondary d-none d-sm-inline" style="font-size: 13px;">#{{ $order->invoice_number }}</span>
                                            </div>
                                        </div>
                                        
                                        {{-- DAFTAR ITEM --}}
                                        @foreach($order->items as $item)
                                            @php
                                                // Logika Tarik Gambar Asli (Sudah di-fix berdasarkan relasi SKU)
                                                $imagePath = null;
                                                if ($item->sku && $item->sku->product && $item->sku->product->images->isNotEmpty()) {
                                                    $primaryImg = $item->sku->product->images->where('is_primary', true)->first() ?? $item->sku->product->images->first();
                                                    if ($primaryImg) {
                                                        $imagePath = 'storage/' . $primaryImg->image_path;
                                                    }
                                                }
                                            @endphp

                                            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 gap-md-4 mb-3">
                                                <div class="ratio ratio-1x1 border border-secondary-subtle flex-shrink-0 bg-light" style="width: 80px;">
                                                    @if($imagePath)
                                                        <img src="{{ asset($imagePath) }}" class="object-fit-cover w-100 h-100" alt="{{ $item->product_name }}">
                                                    @else
                                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary bg-light" style="font-size: 10px;">
                                                            <i class="bi bi-image text-muted fs-4"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="fw-bold text-uppercase mb-1" style="font-size: 15px;">{{ $item->product_name }}</h6>
                                                    <p class="text-secondary mb-0" style="font-size: 13px;">Ukuran: {{ $item->product_size ?? '-' }}</p>
                                                    <p class="text-secondary mt-1 mb-0" style="font-size: 12px;">{{ $item->quantity }} Barang x Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        @endforeach

                                        {{-- FOOTER CARD (TOTAL & TOMBOL) --}}
                                        <div class="d-flex flex-column flex-sm-row justify-content-between mt-4 pt-3 border-top border-secondary-subtle gap-2 align-items-sm-center">
                                            <div>
                                                <p class="text-secondary mb-1" style="font-size: 12px;">Total Belanja</p>
                                                <h5 class="fw-bold mb-0">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</h5>
                                            </div>
                                            
                                            <div class="d-flex gap-2 justify-content-end">
                                                @if($order->payment_status == 'unpaid' && $order->snap_token)
                                                    <button type="button" class="btn btn-black fw-bold text-uppercase btn-lanjut-bayar" style="border-radius: 6px; font-size: 12px; padding: 12px 20px;" data-token="{{ $order->snap_token }}">
                                                        Lanjut Bayar
                                                    </button>
                                                @endif

                                                @if($order->status == 'shipped')
                                                    <a href="{{ route('order_track', $order->id) }}" class="btn btn-outline-dark fw-bold text-uppercase" style="border-radius: 6px; font-size: 12px; padding: 12px 20px;">Lacak Pesanan</a>
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

            </div>
        </div>
    </section>

    <x-customer.footer />
    <x-customer.chatbot />

    {{-- SCRIPT MIDTRANS SNAP --}}
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
                        onSuccess: function(result){
                            alert("Pembayaran Berhasil!");
                            window.location.reload(); 
                        },
                        onPending: function(result){
                            alert("Status menunggu pembayaran. Silakan selesaikan tagihan Anda.");
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        },
                        onError: function(result){
                            alert("Pembayaran Gagal. Silakan coba metode lain.");
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        },
                        onClose: function(){
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        }
                    });
                });
            });
        });
    </script>
@endsection
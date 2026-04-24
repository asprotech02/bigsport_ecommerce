@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom">
        <x-customer.topbar />
        <x-customer.navbar />
    </header>
    
    <!-- BANNER HERO -->
    <x-customer.banner_hero />
    <!-- BANNER HERO -->

    <!-- PROMO -->
    <x-customer.promo />
    <!-- PROMO -->

    <!-- SECTION SALE -->
    <section class="py-5 bg-white">
        <div class="container">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0 text-sale text-uppercase section-title">LAGI DISKON 🔥</h4>
                <a href="#" class="link-lihat-semua">CEK SEMUA</a>
            </div>
            
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                @foreach($products as $product)
                <div class="col d-flex">
                    <x-customer.product_card :product="$product" />
                </div>
                @endforeach
            </div>
            
        </div>
    </section>
    <!-- SECTION SALE -->
    
    <!-- ALBUM -->
    <x-customer.album />
    <!-- ALBUM -->
    
    <!-- SECTION EKSLUSIVE -->
    <section class="py-5 bg-white mt-5">
        <div class="container">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0 text-ekslusife text-uppercase">PRODUK PILIHAN ⚡</h4>
                <a href="#" class="link-lihat-semua">CEK SEMUA</a>
            </div>
            
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
                @foreach($products as $product)
                <div class="col d-flex">
                    <x-customer.product_card :product="$product" />
                </div>
                @endforeach
            </div>
            
        </div>
    </section>
    <!-- SECTION EKSLUSIVE -->
    
    <!-- ICON BRAND -->
    <x-customer.brand />
    <!-- ICON BRAND -->
     
    <!-- FOOTER -->
    <x-customer.footer />
    <!-- FOOTER -->

    <!-- ICON CHATBOT -->
    <x-customer.chatbot />
    <!-- ICON CHATBOT -->


    <!-- ========================================== -->
    <div class="modal fade" id="promoModalModern" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                
                <div class="promo-img-wrapper">
                    <button type="button" class="btn-close btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
                    <img src="https://images.unsplash.com/photo-1608231387042-66d1773070a5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="" class="promo-img">
                </div>

                <div class="modal-body p-4 p-md-5 text-center bg-white">
                    
                    <p class="text-uppercase fw-bold mb-2" style="font-size: 11px; letter-spacing: 2px; color: #eb5e55;">
                        PENAWARAN TERBATAS
                    </p>
                    
                    <h3 class="fw-black text-uppercase mb-3" style="font-size: 28px; font-weight: 900; letter-spacing: 0.5px;">
                        DISKON SAMPAI 50%
                    </h3>
                    
                    <p class="text-secondary mb-4" style="font-size: 14px; line-height: 1.6;">
                        Tingkatkan performa Anda dengan koleksi terbaru. Gunakan kode eksklusif ini saat melakukan pembayaran.
                    </p>
                    
                    <div class="promo-code-box p-3 mb-4 d-flex justify-content-center align-items-center" title="Klik untuk salin">
                        <span class="fw-bold fs-5 text-dark">SAMBA50</span>
                    </div>

                    <a href="#" class="btn btn-black-modern w-100 py-3 mb-3" data-bs-dismiss="modal">BELANJA SEKARANG</a>
                    
                    <!-- <a href="#" class="text-muted text-decoration-none" style="font-size: 12px; font-weight: 500;" data-bs-dismiss="modal">
                        Nanti saja, lanjutkan keliling toko
                    </a> -->

                </div>
            </div>
        </div>
    </div>
    <!-- ========================================== -->

    <!-- ========================================== -->
    <script>
        const lastShown = localStorage.getItem('promoShownTime');
        const now = new Date().getTime();

        if (!lastShown || now - lastShown > 86400000) { // 24 jam
            var promoModal = new bootstrap.Modal(document.getElementById('promoModalModern'));
            
            setTimeout(() => promoModal.show(), 1000);

            document.getElementById('promoModalModern').addEventListener('hidden.bs.modal', function () {
                localStorage.setItem('promoShownTime', now);
            });
        }
    </script>


    <!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // CEK apakah modal sudah pernah ditampilkan
        if (!sessionStorage.getItem('promoShownModern')) {
            
            var promoModal = new bootstrap.Modal(document.getElementById('promoModalModern'));
            
            setTimeout(function() {
                promoModal.show();
            }, 1000);

            // SET setelah modal ditutup
            document.getElementById('promoModalModern').addEventListener('hidden.bs.modal', function () {
                sessionStorage.setItem('promoShownModern', 'true');
            });
        }

    });
    </script> -->
    <!-- ========================================== -->

@endsection
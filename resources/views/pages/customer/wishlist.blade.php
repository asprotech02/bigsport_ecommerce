@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom">
        <x-customer.topbar />
        <x-customer.navbar />
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh;">
        <div class="container">
            
            <div class="text-center mb-5">
                <h3 class="fw-bold text-uppercase m-0" style="letter-spacing: 1.5px;">Wishlist Saya</h3>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    
                    <div class="d-none d-md-flex fw-bold border-bottom border-dark pb-3 mb-4 text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
                        <div class="col-6">Item</div>
                        <div class="col-3 text-center">Harga</div>
                        <div class="col-3 text-end">Aksi</div>
                    </div>

                    <div class="row align-items-center border-bottom pb-4 mb-4">
                        
                        <div class="col-12 col-md-6 d-flex mb-4 mb-md-0">
                            <div class="cart-img-wrapper me-3 me-md-4">
                                <img src="{{ asset('assets/images/product/samba1.svg') }}" alt="Product" style="width: 100%; height: 100%; object-fit: cover; display: block; background-color: #f8f9fa;">
                            </div>
                            <div>
                                <div class="fw-bold text-uppercase mb-1" style="font-size: 15px;">Adidas</div>
                                <div class="text-secondary mb-2" style="font-size: 13px;">Sepatu Samba Seris Denim V1</div>
                                <div class="fw-bold mb-3 text-dark" style="font-size: 12px;"><i class="bi bi-check-circle-fill me-1"></i> Tersedia</div>
                                <a href="#" class="text-danger fw-bold text-decoration-none text-uppercase" style="font-size: 11px; letter-spacing: 1px;">Hapus</a>
                            </div>
                        </div>

                        <div class="col-12 col-md-3 mb-3 mb-md-0 d-flex justify-content-between justify-content-md-center align-items-center">
                            <span class="d-md-none fw-bold text-secondary text-uppercase" style="font-size: 12px;">Harga</span>
                            <span class="fw-bold" style="font-size: 15px;">Rp 1.000.000</span>
                        </div>

                        <div class="col-12 col-md-3 d-flex justify-content-end align-items-center">
                            <button type="sumbit" class="btn btn-black rounded-0 px-3 py-1 w-100 fw-bold text-uppercase d-flex justify-content-center align-items-center gap-2" style="font-size: 12px; letter-spacing: 1px;">
                                <i class="bi bi-cart-plus fs-5"></i> + Keranjang
                            </button>
                        </div>
                        
                    </div>

                    <div class="row align-items-center border-bottom pb-4 mb-4">
                        
                        <div class="col-12 col-md-6 d-flex mb-4 mb-md-0">
                            <div class="cart-img-wrapper me-3 me-md-4">
                                <img src="{{ asset('assets/images/sepatu-samba-2.jpg') }}" alt="Product" style="width: 100%; height: 100%; object-fit: cover; display: block; background-color: #f8f9fa;">
                            </div>
                            <div>
                                <div class="fw-bold text-uppercase mb-1" style="font-size: 15px;">Adidas</div>
                                <div class="text-secondary mb-2" style="font-size: 13px;">Adistar Control 5 Unisex</div>
                                <div class="fw-bold mb-3 text-danger" style="font-size: 12px;"><i class="bi bi-exclamation-circle-fill me-1"></i> Stok Terbatas</div>
                                <a href="#" class="text-danger fw-bold text-decoration-none text-uppercase" style="font-size: 11px; letter-spacing: 1px;">Hapus</a>
                            </div>
                        </div>

                        <div class="col-12 col-md-3 mb-3 mb-md-0 d-flex justify-content-between justify-content-md-center align-items-center">
                            <span class="d-md-none fw-bold text-secondary text-uppercase" style="font-size: 12px;">Harga</span>
                            <span class="fw-bold" style="font-size: 15px;">Rp 1.900.000</span>
                        </div>

                        <div class="col-12 col-md-3 d-flex justify-content-end align-items-center">
                            <button type="sumbit" class="btn btn-black rounded-0 px-3 py-1 w-100 fw-bold text-uppercase d-flex justify-content-center align-items-center gap-2" style="font-size: 12px; letter-spacing: 1px;">
                                <i class="bi bi-cart-plus fs-5"></i> + Keranjang
                            </button>
                        </div>
                        
                    </div>

                </div>
            </div>
            
        </div>
    </section>

    <x-customer.footer />
    <x-customer.chatbot />
@endsection
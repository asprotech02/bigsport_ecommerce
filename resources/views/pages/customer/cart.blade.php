@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom">
        <x-customer.topbar />
        <x-customer.navbar />
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh;">
        <div class="container">
            
            <div class="text-center mb-5">
                <h3 class="fw-bold text-uppercase m-0" style="letter-spacing: 1.5px;">Keranjang Belanja</h3>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    
                    <div class="d-none d-md-flex fw-bold border-bottom border-dark pb-3 mb-4 text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
                        <div class="col-5">Item</div>
                        <div class="col-2 text-center">Harga</div>
                        <div class="col-2 text-center">Jumlah</div>
                        <div class="col-3 text-end">Subtotal</div>
                    </div>

                    <div class="row align-items-center border-bottom pb-4 mb-4">
                        
                        <div class="col-12 col-md-5 d-flex mb-4 mb-md-0">
                            <div class="cart-img-wrapper me-3 me-md-4">
                                <img src="{{ asset('assets/images/product/samba1.svg') }}" alt="Product" style="width: 100%; height: 100%; object-fit: cover; display: block; background-color: #f8f9fa;">
                            </div>
                            <div>
                                <div class="fw-bold text-uppercase mb-1" style="font-size: 15px;">Adidas</div>
                                <div class="text-secondary mb-2" style="font-size: 13px;">Sepatu Samba Seris Denim V1</div>
                                <div class="fw-bold mb-2" style="font-size: 12px;">Ukuran : 40</div>
                                <a href="#" class="text-danger fw-bold text-decoration-none text-uppercase" style="font-size: 11px; letter-spacing: 1px;">Hapus</a>
                            </div>
                        </div>

                        <div class="col-12 col-md-2 mb-3 mb-md-0 d-flex justify-content-between justify-content-md-center align-items-center">
                            <span class="d-md-none fw-bold text-secondary text-uppercase" style="font-size: 12px;">Harga</span>
                            <span class="fw-bold" style="font-size: 15px;">Rp 1.000.000</span>
                        </div>

                        <div class="col-12 col-md-2 mb-3 mb-md-0 d-flex justify-content-between justify-content-md-center align-items-center">
                            <span class="d-md-none fw-bold text-secondary text-uppercase" style="font-size: 12px;">Jumlah</span>
                            <div class="border border-dark d-flex align-items-center" style="height: 35px;">
                                <button type="button" class="btn border-0 rounded-0 fw-bold px-3 py-0 shadow-none text-dark">-</button>
                                <input type="text" value="2" class="border-0 text-center fw-bold text-dark p-0" style="width: 30px; outline: none; background: transparent;" readonly>
                                <button type="button" class="btn border-0 rounded-0 fw-bold px-3 py-0 shadow-none text-dark">+</button>
                            </div>
                        </div>

                        <div class="col-12 col-md-3 d-flex justify-content-between justify-content-md-end align-items-center">
                            <span class="d-md-none fw-bold text-secondary text-uppercase" style="font-size: 12px;">Subtotal</span>
                            <span class="fw-bold fs-6">Rp 2.000.000</span>
                        </div>

                    </div>

                    <div class="row align-items-center border-bottom pb-4 mb-4">
                        
                        <div class="col-12 col-md-5 d-flex mb-4 mb-md-0">
                            <div class="cart-img-wrapper me-3 me-md-4">
                                <img src="{{ asset('assets/images/sepatu-samba-2.jpg') }}" alt="Product" style="width: 100%; height: 100%; object-fit: cover; display: block; background-color: #f8f9fa;">
                            </div>
                            <div>
                                <div class="fw-bold text-uppercase mb-1" style="font-size: 15px;">Adidas</div>
                                <div class="text-secondary mb-2" style="font-size: 13px;">Sepatu Samba Seris Denim V1</div>
                                <div class="fw-bold mb-2" style="font-size: 12px;">Ukuran : 41</div>
                                <a href="#" class="text-danger fw-bold text-decoration-none text-uppercase" style="font-size: 11px; letter-spacing: 1px;">Hapus</a>
                            </div>
                        </div>

                        <div class="col-12 col-md-2 mb-3 mb-md-0 d-flex justify-content-between justify-content-md-center align-items-center">
                            <span class="d-md-none fw-bold text-secondary text-uppercase" style="font-size: 12px;">Harga</span>
                            <span class="fw-bold" style="font-size: 15px;">Rp 1.000.000</span>
                        </div>

                        <div class="col-12 col-md-2 mb-3 mb-md-0 d-flex justify-content-between justify-content-md-center align-items-center">
                            <span class="d-md-none fw-bold text-secondary text-uppercase" style="font-size: 12px;">Jumlah</span>
                            <div class="border border-dark d-flex align-items-center" style="height: 35px;">
                                <button type="button" class="btn border-0 rounded-0 fw-bold px-3 py-0 shadow-none text-dark">-</button>
                                <input type="text" value="2" class="border-0 text-center fw-bold text-dark p-0" style="width: 30px; outline: none; background: transparent;" readonly>
                                <button type="button" class="btn border-0 rounded-0 fw-bold px-3 py-0 shadow-none text-dark">+</button>
                            </div>
                        </div>

                        <div class="col-12 col-md-3 d-flex justify-content-between justify-content-md-end align-items-center">
                            <span class="d-md-none fw-bold text-secondary text-uppercase" style="font-size: 12px;">Subtotal</span>
                            <span class="fw-bold fs-6">Rp 2.000.000</span>
                        </div>

                    </div>

                    <div class="row justify-content-end mt-5">
                        <div class="col-12 col-md-6 col-lg-5">
                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-dark">
                                <span class="fw-bold text-uppercase" style="font-size: 14px; letter-spacing: 1px;">Total Belanja</span>
                                <span class="fw-bold fs-4">Rp 5.600.000</span>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('checkout') }}" class="btn btn-black px-4 py-2 fw-bold text-uppercase d-flex align-items-center justify-content-center gap-2" style="letter-spacing: 1px; font-size: 13px;">
                                    LANJUT CHECKOUT <i class="bi bi-arrow-right-short fs-5"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <x-customer.footer />
    <x-customer.chatbot />
@endsection
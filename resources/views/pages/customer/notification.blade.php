@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom">
        <x-customer.topbar />
        <x-customer.navbar />
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh;">
        <div class="container">
            
            <!-- <div class="text-center mb-5">
                <h3 class="fw-bold text-uppercase m-0" style="letter-spacing: 1.5px;">Notifikasi</h3>
            </div> -->

            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center border-bottom border-dark pb-3 mb-4">
                        <div class="d-flex gap-4 mb-3 mb-sm-0 fw-bold text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
                            <a href="#" class="text-dark text-decoration-none border-bottom border-dark border-2 pb-1">Semua</a>
                            <a href="#" class="text-secondary text-decoration-none hover-text-dark pb-1">Transaksi</a>
                            <a href="#" class="text-secondary text-decoration-none hover-text-dark pb-1">Promo</a>
                        </div>
                        <a href="#" class="text-secondary text-decoration-none text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px; border-bottom: 1px dashed #ccc;">
                            Tandai Semua Dibaca
                        </a>
                    </div>

                    <a href="#" class="text-decoration-none text-dark d-block p-4 border border-dark mb-3 notification-item bg-light-gray position-relative">
                        <div class="position-absolute bg-dark" style="top: 15px; right: 15px; width: 10px; height: 10px;"></div>
                        
                        <div class="d-flex align-items-start">
                            <div class="bg-black text-white d-flex justify-content-center align-items-center me-3 me-md-4 flex-shrink-0" style="width: 55px; height: 55px;">
                                <i class="bi bi-box-seam fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mb-2 pe-3">
                                    <h6 class="fw-bold text-uppercase mb-1 mb-sm-0" style="font-size: 14px; letter-spacing: 0.5px;">Pesanan Sedang Dikirim</h6>
                                    <span class="text-secondary fw-bold" style="font-size: 11px;">HARI INI, 10:30</span>
                                </div>
                                <p class="mb-0 text-dark" style="font-size: 13px; line-height: 1.6;">
                                    Pesanan <span class="fw-bold">#INV-987654</span> sedang dalam perjalanan menuju alamat tujuan oleh kurir JNE. Lacak pesanan Anda sekarang.
                                </p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="text-decoration-none text-dark d-block p-4 border border-dark mb-3 notification-item bg-light-gray position-relative">
                        <div class="position-absolute bg-dark" style="top: 15px; right: 15px; width: 10px; height: 10px;"></div>
                        
                        <div class="d-flex align-items-start">
                            <div class="bg-black text-white d-flex justify-content-center align-items-center me-3 me-md-4 flex-shrink-0" style="width: 55px; height: 55px;">
                                <i class="bi bi-tag-fill fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mb-2 pe-3">
                                    <h6 class="fw-bold text-uppercase mb-1 mb-sm-0 text-sale" style="font-size: 14px; letter-spacing: 0.5px;">Spesial 3.3 Deals!</h6>
                                    <span class="text-secondary fw-bold" style="font-size: 11px;">KEMARIN, 14:00</span>
                                </div>
                                <p class="mb-0 text-dark" style="font-size: 13px; line-height: 1.6;">
                                    Diskon hingga 50% untuk seri sepatu Samba dan Adistar. Gunakan kode <span class="fw-bold">SPORT33</span> saat checkout. Jangan sampai kehabisan!
                                </p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="text-decoration-none text-dark d-block p-4 border-bottom border-secondary-subtle mb-3 notification-item bg-white">
                        <div class="d-flex align-items-start opacity-75">
                            <div class="border border-dark text-dark d-flex justify-content-center align-items-center me-3 me-md-4 flex-shrink-0" style="width: 55px; height: 55px;">
                                <i class="bi bi-check2-square fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mb-2">
                                    <h6 class="fw-bold text-uppercase mb-1 mb-sm-0" style="font-size: 14px; letter-spacing: 0.5px;">Pesanan Telah Tiba</h6>
                                    <span class="text-secondary fw-bold" style="font-size: 11px;">20 APR 2026, 16:45</span>
                                </div>
                                <p class="mb-0 text-secondary" style="font-size: 13px; line-height: 1.6;">
                                    Pesanan <span class="fw-bold">#INV-123456</span> telah berhasil dikirim dan diterima oleh John Doe. Terima kasih telah berbelanja!
                                </p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="text-decoration-none text-dark d-block p-4 border-bottom border-secondary-subtle mb-3 notification-item bg-white">
                        <div class="d-flex align-items-start opacity-75">
                            <div class="border border-dark text-dark d-flex justify-content-center align-items-center me-3 me-md-4 flex-shrink-0" style="width: 55px; height: 55px;">
                                <i class="bi bi-person-fill-gear fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mb-2">
                                    <h6 class="fw-bold text-uppercase mb-1 mb-sm-0" style="font-size: 14px; letter-spacing: 0.5px;">Pembaruan Kata Sandi</h6>
                                    <span class="text-secondary fw-bold" style="font-size: 11px;">18 APR 2026, 09:15</span>
                                </div>
                                <p class="mb-0 text-secondary" style="font-size: 13px; line-height: 1.6;">
                                    Kata sandi akun Anda berhasil diperbarui. Jika Anda tidak melakukan perubahan ini, segera hubungi layanan pelanggan kami.
                                </p>
                            </div>
                        </div>
                    </a>

                    <div class="text-center mt-5">
                        <button class="btn btn-outline-dark rounded-0 px-5 py-2 fw-bold text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
                            Muat Lebih Banyak
                        </button>
                    </div>

                </div>
            </div>
            
        </div>
    </section>

    <x-customer.footer />
    <x-customer.chatbot />
@endsection
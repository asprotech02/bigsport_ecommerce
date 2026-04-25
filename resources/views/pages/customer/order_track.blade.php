@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        <x-customer.topbar />
        <x-customer.navbar />
    </header>

    <section class="py-4 py-lg-5 bg-white" style="min-height: 70vh;">
        <div class="container">
            
            <div class="row g-4 g-lg-5">
                
                <x-customer.profile_menu />


                <div class="col-12 col-lg-9 ps-lg-5">
                    
                    <div class="mb-4">
                        <h3 class="fw-bold text-uppercase m-0" style="font-size: 24px; letter-spacing: 1px;">Status Pesanan</h3>
                    </div>

                    <div class="border border-dark p-4 mb-5 rounded-0">
                        <div class="row g-3">
                            <div class="col-6 col-md-3 border-end-md border-secondary-subtle">
                                <p class="text-secondary mb-1" style="font-size: 12px; text-transform: uppercase;">Kurir</p>
                                <p class="fw-bold mb-0" style="font-size: 14px;">JNE Reguler</p>
                            </div>
                            <div class="col-6 col-md-4 border-end-md border-secondary-subtle">
                                <p class="text-secondary mb-1" style="font-size: 12px; text-transform: uppercase;">No. Pesanan</p>
                                <div class="d-flex align-items-center gap-2">
                                    <p class="fw-bold mb-0" style="font-size: 14px;">CGK1234567890</p>
                                    <button class="btn p-0 border-0 text-secondary hover-text-dark" title="Salin Pesanan"><i class="bi bi-copy"></i></button>
                                </div>
                            </div>
                            <div class="col-12 col-md-5">
                                <p class="text-secondary mb-1" style="font-size: 12px; text-transform: uppercase;">Estimasi Tiba</p>
                                <p class="fw-bold text-success mb-0" style="font-size: 14px;">27 April 2026</p>
                            </div>
                        </div>
                    </div>

                    <div class="position-relative ms-3 ms-md-4">
                        <div class="position-absolute bg-dark" style="left: 7px; top: 10px; bottom: 30px; width: 2px; z-index: 1;"></div>

                        <div class="d-flex position-relative mb-5" style="z-index: 2;">
                            <div class="bg-dark rounded-circle flex-shrink-0 mt-1" style="width: 16px; height: 16px; border: 3px solid #fff; box-shadow: 0 0 0 2px #000;"></div>
                            <div class="ms-4">
                                <h6 class="fw-bold text-uppercase mb-1" style="font-size: 14px;">Pesanan Sedang Diantar Kurir</h6>
                                <p class="text-secondary mb-1" style="font-size: 13px;">Kurir JNE (Bpk. Budi) sedang membawa paket menuju alamat tujuan.</p>
                                <span class="fw-bold text-dark" style="font-size: 12px;">25 Apr 2026, 09:30 WIB</span>
                            </div>
                        </div>

                        <div class="d-flex position-relative mb-5" style="z-index: 2;">
                            <div class="bg-white rounded-circle flex-shrink-0 mt-1 border border-dark" style="width: 16px; height: 16px; border-width: 2px !important;"></div>
                            <div class="ms-4 opacity-75">
                                <h6 class="fw-bold text-uppercase mb-1" style="font-size: 14px;">Tiba di Fasilitas Hub Transit</h6>
                                <p class="text-secondary mb-1" style="font-size: 13px;">Paket telah tiba di fasilitas penyortiran Jakarta Selatan.</p>
                                <span class="text-secondary fw-bold" style="font-size: 12px;">24 Apr 2026, 21:15 WIB</span>
                            </div>
                        </div>

                        <div class="d-flex position-relative mb-5" style="z-index: 2;">
                            <div class="bg-white rounded-circle flex-shrink-0 mt-1 border border-dark" style="width: 16px; height: 16px; border-width: 2px !important;"></div>
                            <div class="ms-4 opacity-75">
                                <h6 class="fw-bold text-uppercase mb-1" style="font-size: 14px;">Paket Diserahkan ke Kurir</h6>
                                <p class="text-secondary mb-1" style="font-size: 13px;">Penjual telah menyerahkan paket pesanan ke pihak JNE.</p>
                                <span class="text-secondary fw-bold" style="font-size: 12px;">24 Apr 2026, 15:00 WIB</span>
                            </div>
                        </div>

                        <div class="d-flex position-relative" style="z-index: 2;">
                            <div class="bg-white rounded-circle flex-shrink-0 mt-1 border border-dark" style="width: 16px; height: 16px; border-width: 2px !important;"></div>
                            <div class="ms-4 opacity-75">
                                <h6 class="fw-bold text-uppercase mb-1" style="font-size: 14px;">Pesanan Dibuat</h6>
                                <p class="text-secondary mb-1" style="font-size: 13px;">Pembayaran berhasil diverifikasi.</p>
                                <span class="text-secondary fw-bold" style="font-size: 12px;">23 Apr 2026, 10:20 WIB</span>
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
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

                    <ul class="nav border-bottom border-secondary-subtle mb-4 gap-3 gap-md-4 d-flex flex-nowrap overflow-x-auto thumbnail-scroll" id="orderTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 active text-dark opacity-75 hover-opacity-100 text-nowrap" id="semua-tab" data-bs-toggle="tab" data-bs-target="#semua" type="button" role="tab" aria-selected="true">Semua</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" id="dikemas-tab" data-bs-toggle="tab" data-bs-target="#dikemas" type="button" role="tab" aria-selected="false">Dikemas</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" id="dikirim-tab" data-bs-toggle="tab" data-bs-target="#dikirim" type="button" role="tab" aria-selected="false">Dikirim</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" id="selesai-tab" data-bs-toggle="tab" data-bs-target="#selesai" type="button" role="tab" aria-selected="false">Selesai</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="orderTabsContent">
                        
                        <div class="tab-pane fade show active" id="semua" role="tabpanel">
                            
                            <div class="border border-dark rounded-0 p-3 p-md-4 mb-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between border-bottom border-secondary-subtle pb-3 mb-3 gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="fw-bold text-uppercase" style="font-size: 13px;">25 Apr 2026</span>
                                        <span class="badge bg-dark rounded-0 text-uppercase px-2 py-1" style="letter-spacing: 0.5px; font-weight: 600;">DIKIRIM</span>
                                        <span class="text-secondary d-none d-sm-inline" style="font-size: 13px;">#INV-20260425-001</span>
                                    </div>
                                </div>
                                
                                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 gap-md-4">
                                    <div class="ratio ratio-1x1 border border-secondary-subtle flex-shrink-0" style="width: 80px;">
                                        <img src="{{ asset('assets/images/product/samba1.svg') }}" class="object-fit-cover w-100 h-100">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-uppercase mb-1" style="font-size: 15px;">Sepatu Samba Seris Denim V1</h6>
                                        <p class="text-secondary mb-0" style="font-size: 13px;">Warna: Hitam | Ukuran: 40</p>
                                        <p class="text-secondary mt-1 mb-0" style="font-size: 12px;">1 Barang x Rp 5.600.000</p>
                                    </div>
                                    <div class="text-md-end border-start-md border-secondary-subtle ps-md-4 pt-3 pt-md-0 w-100 w-md-auto">
                                        <p class="text-secondary mb-1" style="font-size: 12px;">Total Belanja</p>
                                        <h5 class="fw-bold mb-0">Rp 5.625.000</h5>
                                    </div>
                                </div>

                                <div class="d-flex flex-column flex-sm-row justify-content-end mt-4 pt-3 border-top border-secondary-subtle gap-2">
                                    <a href="{{ route('order_track') }}" class="btn btn-outline-dark fw-bold text-uppercase" style="border-radius: 6px; font-size: 12px; padding: 12px 20px;">Lacak Pesanan</a>
                                </div>
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
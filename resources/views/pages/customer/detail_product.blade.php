@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        <x-customer.topbar />
        <x-customer.navbar />
    </header>

    <section class="py-4 py-lg-5 bg-white">
        <div class="container" style="max-width: 1200px;">
            
            <nav aria-label="breadcrumb" class="mb-4 d-none d-md-block">
                <ol class="breadcrumb text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 1px;">
                    <li class="breadcrumb-item"><a href="#" class="text-dark text-decoration-none">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-dark text-decoration-none">Wanita</a></li>
                    <li class="breadcrumb-item active text-secondary" aria-current="page">Sepatu Samba Seris Denim V1</li>
                </ol>
            </nav>

            <div class="bg-light-gray p-4 p-lg-5 mb-5 rounded-0">
                <div class="row g-5">
                    
                    <div class="col-12 col-lg-7 d-flex flex-column-reverse flex-md-row gap-3">
                        
                        <div class="d-flex flex-row flex-md-column gap-3 overflow-x-auto thumbnail-scroll" style="width: 100%; max-width: 100px;">
                            <div class="ratio ratio-1x1 border border-dark bg-white flex-shrink-0 cursor-pointer" style="width: 80px;">
                                <img src="{{ asset('assets/images/product/samba1.svg') }}" alt="Thumb 1" class="w-100 h-100 object-fit-cover p-2">
                            </div>
                            <div class="ratio ratio-1x1 border border-secondary-subtle bg-white flex-shrink-0 cursor-pointer opacity-50 hover-opacity-100" style="width: 80px;">
                                <img src="{{ asset('assets/images/sepatu-samba-2.jpg') }}" alt="Thumb 2" class="w-100 h-100 object-fit-cover p-2">
                            </div>
                            <div class="ratio ratio-1x1 border border-secondary-subtle bg-white flex-shrink-0 cursor-pointer opacity-50 hover-opacity-100" style="width: 80px;">
                                <img src="{{ asset('assets/images/sepatu-samba-3.jpg') }}" alt="Thumb 3" class="w-100 h-100 object-fit-cover p-2">
                            </div>
                            <div class="ratio ratio-1x1 border border-secondary-subtle bg-white flex-shrink-0 cursor-pointer opacity-50 hover-opacity-100" style="width: 80px;">
                                <img src="{{ asset('assets/images/product/samba1.svg') }}" alt="Thumb 4" class="w-100 h-100 object-fit-cover p-2">
                            </div>
                        </div>

                        <div class="flex-grow-1 bg-white border border-secondary-subtle d-flex align-items-center justify-content-center">
                            <div class="ratio ratio-1x1 w-100">
                                <img src="{{ asset('assets/images/product/samba1.svg') }}" alt="Main Product" class="w-100 h-100 object-fit-cover p-4">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5">
                        
                        <p class="text-danger fw-bold mb-1 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Adidas</p>
                        
                        <h2 class="fw-black text-uppercase mb-1" style="font-size: 28px; line-height: 1.2;">
                            Sepatu Samba Seris <br> Denim V1 
                            <span class="fw-normal text-secondary" style="font-size: 16px;">/ Hitam</span>
                        </h2>
                        
                        <p class="text-dark fw-bold mb-2" style="font-size: 14px;">Wanita</p>
                        
                        <h3 class="fw-bolder mb-1" style="font-size: 24px;">Rp 5.600.000</h3>
                        
                        <div class="mb-4">
                            <span class="text-secondary" style="font-size: 12px;">40 Sold</span>
                            <div class="d-flex align-items-center gap-1 mt-1" style="font-size: 12px;">
                                <div class="text-dark">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                </div>
                                <span class="text-secondary ms-1">4.0 /</span>
                                <a href="#ulasan" class="text-danger text-decoration-none fw-bold">Ulasan (58)</a>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <span class="fw-bold" style="font-size: 14px;">Ukuran</span>
                                <a href="#" class="text-dark fw-bold text-decoration-none" style="font-size: 12px; border-bottom: 1px solid #000;">Panduan Size</a>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-outline-dark rounded-0 px-2 py-1 fw-bold size-btn active" style="font-size: 12px; min-width: 35px;">35</button>
                                <button class="btn btn-outline-dark rounded-0 px-2 py-1 fw-bold size-btn" style="font-size: 12px; min-width: 35px;">36</button>
                                <button class="btn btn-outline-dark rounded-0 px-2 py-1 fw-bold size-btn" style="font-size: 12px; min-width: 35px;">37</button>
                                <button class="btn btn-outline-dark rounded-0 px-2 py-1 fw-bold size-btn" style="font-size: 12px; min-width: 35px;">38</button>
                                <button class="btn btn-outline-dark rounded-0 px-2 py-1 fw-bold size-btn" style="font-size: 12px; min-width: 35px;">39</button>
                                <button class="btn btn-outline-secondary bg-light text-muted rounded-0 px-2 py-1 fw-bold size-btn disabled" style="font-size: 12px; min-width: 35px; border-style: dashed;">40</button>
                                <button class="btn btn-outline-secondary bg-light text-muted rounded-0 px-2 py-1 fw-bold size-btn disabled" style="font-size: 12px; min-width: 35px; border-style: dashed;">41</button>
                                <button class="btn btn-outline-dark rounded-0 px-2 py-1 fw-bold size-btn" style="font-size: 12px; min-width: 35px;">42</button>
                            </div>
                        </div>

                        <div class="border border-dark p-3 mb-4 bg-white d-flex align-items-center gap-3 rounded-0">
                            <i class="bi bi-truck fs-3"></i>
                            <div>
                                <div class="fw-bold" style="font-size: 14px;">Gratis Pengiriman</div>
                                <div class="text-secondary" style="font-size: 12px;">Buat pesanan sekarang!</div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold d-block mb-2" style="font-size: 14px;">Kuantitas</label>
                            <select class="form-select rounded-0 border-dark fw-bold" style="width: 80px; font-size: 14px; cursor: pointer;">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3" selected>3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>

                        <div class="row g-2 mt-2">
                            <div class="col-12 col-sm-6">
                                <button type="button" class="btn btn-outline-dark w-100 rounded-0 fw-bold text-uppercase d-flex justify-content-center align-items-center" style="padding: 14px 20px; font-size: 12px; letter-spacing: 1px; border-width: 2px;">
                                    BELI SEKARANG
                                </button>
                            </div>
                            <div class="col-12 col-sm-6">
                                <button type="button" class="btn btn-action-main m-0 h-100" style="font-size: 12px;">
                                    TAMBAH KE KERANJANG
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="border border-secondary-subtle rounded-0 mb-5">
                
                <div class="d-flex border-bottom border-secondary-subtle px-4 pt-4 gap-4">
                    <div class="fw-bold fs-5 pb-3 cursor-pointer text-dark border-bottom border-danger" style="border-bottom-width: 3px !important;">
                        Detail
                    </div>
                    <div class="fw-bold fs-5 pb-3 cursor-pointer text-dark opacity-75 hover-opacity-100">
                        Ulasan 
                        <span class="text-warning ms-1" style="font-size: 14px;">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </span> 
                        <span style="font-size: 14px;">(58)</span>
                    </div>
                </div>

                <div class="p-4 p-md-5 bg-white">
                    <h6 class="fw-bold text-uppercase mb-4">SEPATU SAMBA SERIS DENIM V1</h6>
                    
                    <p class="text-secondary mb-5" style="font-size: 14px; line-height: 1.8;">
                        Lahir di lapangan sepak bola, Samba adalah ikon street style abadi. Siluet ini tetap setia pada warisannya dengan desain klasik low-profile, upper berbahan denim premium, lapisan suede, dan sol karet gum. Pembaruan seri ini memberikan sentuhan modern pada desain retro yang sangat digemari.
                    </p>
                    
                    <h6 class="fw-bold text-uppercase mb-3">Detail Ukuran</h6>
                    <ul class="text-secondary mb-5" style="font-size: 14px; line-height: 1.8;">
                        <li>Tersedia dari ukuran 35 hingga 42 (Standar Eropa)</li>
                        <li>Regular fit; Direkomendasikan memilih ukuran asli Anda (True to size).</li>
                    </ul>

                    <h6 class="fw-bold text-uppercase mb-3">Detail Warna</h6>
                    <ul class="text-secondary mb-0" style="font-size: 14px; line-height: 1.8;">
                        <li>Core Black / Cloud White / Gum</li>
                        <li>Material utama: Denim dan Suede overlay</li>
                    </ul>
                </div>
            </div>

        </div>
    </section>

    <x-customer.footer />
    <x-customer.chatbot />
@endsection
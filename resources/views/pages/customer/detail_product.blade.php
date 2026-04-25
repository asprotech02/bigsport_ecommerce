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
                        <div class="d-flex flex-row flex-md-column gap-2 overflow-x-auto thumbnail-scroll" style="width: 100%; max-width: 85px;">
                            <div class="ratio ratio-1x1 border border-dark bg-white flex-shrink-0 cursor-pointer" style="border-width: 2px !important; width: 85px;">
                                <img src="{{ asset('assets/images/product/samba1.svg') }}" alt="Thumb 1" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="ratio ratio-1x1 border border-secondary-subtle bg-white flex-shrink-0 cursor-pointer opacity-50 hover-opacity-100" style="width: 85px;">
                                <img src="{{ asset('assets/images/sepatu-samba-2.jpg') }}" alt="Thumb 2" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="ratio ratio-1x1 border border-secondary-subtle bg-white flex-shrink-0 cursor-pointer opacity-50 hover-opacity-100" style="width: 85px;">
                                <img src="{{ asset('assets/images/sepatu-samba-3.jpg') }}" alt="Thumb 3" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="ratio ratio-1x1 border border-secondary-subtle bg-white flex-shrink-0 cursor-pointer opacity-50 hover-opacity-100" style="width: 85px;">
                                <img src="{{ asset('assets/images/product/samba1.svg') }}" alt="Thumb 4" class="w-100 h-100 object-fit-cover">
                            </div>
                        </div>

                        <div class="flex-grow-1 position-relative bg-white border border-secondary-subtle d-flex align-items-center justify-content-center">
                            <button class="btn rounded-0 position-absolute start-0 top-50 translate-middle-y ms-2 ms-md-3 border-0 d-flex justify-content-center align-items-center gallery-nav-btn" style="width: 40px; height: 40px; z-index: 2;">
                                <i class="bi bi-chevron-left text-white fs-5"></i>
                            </button>
                            <div class="ratio ratio-1x1 w-100">
                                <img src="{{ asset('assets/images/product/samba1.svg') }}" alt="Main Product" class="w-100 h-100 object-fit-cover">
                            </div>
                            <button class="btn rounded-0 position-absolute end-0 top-50 translate-middle-y me-2 me-md-3 border-0 d-flex justify-content-center align-items-center gallery-nav-btn" style="width: 40px; height: 40px; z-index: 2;">
                                <i class="bi bi-chevron-right text-white fs-5"></i>
                            </button>
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
                                <a href="#" onclick="document.getElementById('ulasan-tab').click(); return false;" class="text-danger text-decoration-none fw-bold">Ulasan (58)</a>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="mb-2">
                                <span class="fw-bold d-block" style="font-size: 14px;">Ukuran</span>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#sizeGuideModal" class="text-dark fw-bold text-decoration-none mt-1 d-inline-block" style="font-size: 11px; border-bottom: 1px solid #000;">Panduan Size</a>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
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

                        <div class="mb-4">
                            <label class="fw-bold d-block mb-2" style="font-size: 14px;">Kuantitas</label>
                            <div class="border border-dark d-inline-flex align-items-center bg-white" style="height: 38px;">
                                <button type="button" class="btn border-0 rounded-0 fw-bold px-3 py-0 shadow-none text-dark hover-opacity fs-5">-</button>
                                <input type="text" value="3" class="border-0 text-center fw-bold text-dark p-0" style="width: 40px; outline: none; background: transparent; font-size: 14px;" readonly>
                                <button type="button" class="btn border-0 rounded-0 fw-bold px-3 py-0 shadow-none text-dark hover-opacity fs-5">+</button>
                            </div>
                        </div>

                        <div class="row g-2 mt-4">
                            <div class="col-12 col-sm-6">
                                <button type="button" class="btn btn-outline-dark w-100 rounded-0 fw-bold text-uppercase d-flex justify-content-center align-items-center" style="height: 48px; font-size: 12px; letter-spacing: 1px; border-width: 2px;">
                                    BELI SEKARANG
                                </button>
                            </div>
                            <div class="col-12 col-sm-6">
                                <button type="button" class="btn btn-action-main m-0 w-100 d-flex justify-content-center align-items-center" style="height: 48px; font-size: 12px; padding: 0;">
                                    TAMBAH KE KERANJANG
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 pt-3">
                            <span class="text-secondary d-block mb-2" style="font-size: 12px;">Available Payment Methods:</span>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="bg-white border border-light-subtle d-flex align-items-center justify-content-center p-1" style="width: 55px; height: 32px;"><img src="{{ asset('assets/images/payment-bca.png') }}" alt="BCA" class="w-100 h-100" style="object-fit: contain;"></div>
                                <div class="bg-white border border-light-subtle d-flex align-items-center justify-content-center p-1" style="width: 55px; height: 32px;"><img src="{{ asset('assets/images/payment-qris.png') }}" alt="QRIS" class="w-100 h-100" style="object-fit: contain;"></div>
                                <div class="bg-white border border-light-subtle d-flex align-items-center justify-content-center p-1" style="width: 55px; height: 32px;"><img src="{{ asset('assets/images/payment-kredivo.png') }}" alt="Kredivo" class="w-100 h-100" style="object-fit: contain;"></div>
                                <div class="bg-white border border-light-subtle d-flex align-items-center justify-content-center p-1" style="width: 55px; height: 32px;"><img src="{{ asset('assets/images/payment-mastercard.png') }}" alt="Mastercard" class="w-100 h-100" style="object-fit: contain;"></div>
                                <div class="bg-white border border-light-subtle d-flex align-items-center justify-content-center p-1" style="width: 55px; height: 32px;"><img src="{{ asset('assets/images/payment-visa.png') }}" alt="Visa" class="w-100 h-100" style="object-fit: contain;"></div>
                                <div class="bg-white border border-light-subtle d-flex align-items-center justify-content-center p-1" style="width: 55px; height: 32px;"><img src="{{ asset('assets/images/payment-jcb.png') }}" alt="JCB" class="w-100 h-100" style="object-fit: contain;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border border-secondary-subtle rounded-0 mb-5">
                
                <ul class="nav border-bottom border-secondary-subtle px-4 pt-4 gap-4" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link nav-tab-custom fw-bold fs-5 pb-3 px-1 active text-dark opacity-75 hover-opacity-100" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail-content" type="button" role="tab" aria-controls="detail-content" aria-selected="true">
                            Detail
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link nav-tab-custom fw-bold fs-5 pb-3 px-1 text-dark opacity-75 hover-opacity-100" id="ulasan-tab" data-bs-toggle="tab" data-bs-target="#ulasan-content" type="button" role="tab" aria-controls="ulasan-content" aria-selected="false">
                            Ulasan 
                            <span class="text-warning ms-1" style="font-size: 14px;">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                            </span> 
                            <span style="font-size: 14px;">(58)</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="productTabsContent">
                    
                    <div class="tab-pane fade show active p-4 p-md-5 bg-white" id="detail-content" role="tabpanel" aria-labelledby="detail-tab">
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

                    <div class="tab-pane fade p-4 p-md-5 bg-white" id="ulasan-content" role="tabpanel" aria-labelledby="ulasan-tab">
                        <div class="row g-5">
                            <div class="col-12 col-md-4 col-lg-3">
                                <h6 class="fw-bold text-uppercase mb-4">Ringkasan Ulasan</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <h1 class="fw-black m-0 me-3" style="font-size: 48px;">4.0</h1>
                                    <div>
                                        <div class="text-warning mb-1 fs-5">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star text-secondary"></i>
                                        </div>
                                        <span class="text-secondary" style="font-size: 13px;">Berdasarkan 58 Ulasan</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12 col-md-8 col-lg-9">
                                <div class="border-bottom border-secondary-subtle pb-4 mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="fw-bold fs-6">Ahmad F. <i class="bi bi-patch-check-fill text-success ms-1" style="font-size: 12px;"></i></div>
                                        <div class="text-secondary" style="font-size: 12px;">12 April 2026</div>
                                    </div>
                                    <div class="text-warning mb-3" style="font-size: 12px;">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                    </div>
                                    <p class="text-secondary mb-0" style="font-size: 14px; line-height: 1.6;">
                                        Sepatunya mantap banget! Original, ukurannya pas sesuai dengan panduan size. Bahan denimnya juga ngasih kesan beda dari sepatu Samba biasa.
                                    </p>
                                </div>

                                <div class="border-bottom border-secondary-subtle pb-4 mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="fw-bold fs-6">Rina Melati <i class="bi bi-patch-check-fill text-success ms-1" style="font-size: 12px;"></i></div>
                                        <div class="text-secondary" style="font-size: 12px;">28 Maret 2026</div>
                                    </div>
                                    <div class="text-warning mb-3" style="font-size: 12px;">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star text-secondary"></i>
                                    </div>
                                    <p class="text-secondary mb-0" style="font-size: 14px; line-height: 1.6;">
                                        Pengiriman cepat, packing sangat aman pakai double box. Sepatunya nyaman dipakai jalan jauh, cuma warna aslinya sedikit lebih gelap dari difoto.
                                    </p>
                                </div>

                                <button type="button" class="btn btn-outline-dark rounded-0 fw-bold text-uppercase px-4 py-2 mt-2" style="font-size: 12px; letter-spacing: 1px;">
                                    Lihat Lebih Banyak
                                </button>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>

        </div>
    </section>

    <x-customer.footer />
    <x-customer.chatbot />
    

    <div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-labelledby="sizeGuideModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-0 border-dark" style="border-width: 2px;">
                
                <div class="modal-header border-bottom border-dark rounded-0 px-4 py-3">
                    <h5 class="modal-title fw-bold text-uppercase" id="sizeGuideModalLabel" style="font-size: 15px; letter-spacing: 1px;">
                        Panduan Ukuran Sepatu Adidas
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4 p-md-5">
                    
                    <p class="text-secondary mb-4" style="font-size: 14px; line-height: 1.6;">
                        Gunakan tabel di bawah ini untuk menentukan ukuran yang paling tepat untuk Anda berdasarkan panjang kaki (dalam centimeter). Jika ukuran Anda berada di antara dua size, kami menyarankan untuk memilih ukuran yang lebih besar.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered border-dark text-center align-middle mb-0" style="font-size: 13px;">
                            <thead class="bg-light-gray fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                                <tr>
                                    <th scope="col" class="py-3">EU (Eropa)</th>
                                    <th scope="col" class="py-3">UK</th>
                                    <th scope="col" class="py-3">US (Wanita)</th>
                                    <th scope="col" class="py-3 bg-dark text-white">Panjang Kaki (CM)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold">35 1/3</td>
                                    <td>3</td>
                                    <td>4.5</td>
                                    <td class="fw-bold bg-light">22.1 cm</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">36</td>
                                    <td>3.5</td>
                                    <td>5</td>
                                    <td class="fw-bold bg-light">22.5 cm</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">36 2/3</td>
                                    <td>4</td>
                                    <td>5.5</td>
                                    <td class="fw-bold bg-light">22.9 cm</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">37 1/3</td>
                                    <td>4.5</td>
                                    <td>6</td>
                                    <td class="fw-bold bg-light">23.3 cm</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">38</td>
                                    <td>5</td>
                                    <td>6.5</td>
                                    <td class="fw-bold bg-light">23.8 cm</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">38 2/3</td>
                                    <td>5.5</td>
                                    <td>7</td>
                                    <td class="fw-bold bg-light">24.2 cm</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">39 1/3</td>
                                    <td>6</td>
                                    <td>7.5</td>
                                    <td class="fw-bold bg-light">24.6 cm</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">40</td>
                                    <td>6.5</td>
                                    <td>8</td>
                                    <td class="fw-bold bg-light">25.0 cm</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">40 2/3</td>
                                    <td>7</td>
                                    <td>8.5</td>
                                    <td class="fw-bold bg-light">25.5 cm</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">41 1/3</td>
                                    <td>7.5</td>
                                    <td>9</td>
                                    <td class="fw-bold bg-light">25.9 cm</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">42</td>
                                    <td>8</td>
                                    <td>9.5</td>
                                    <td class="fw-bold bg-light">26.3 cm</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5 border border-secondary-subtle p-4 bg-light-gray rounded-0">
                        <h6 class="fw-bold text-uppercase mb-3" style="font-size: 13px; letter-spacing: 0.5px;">Cara Mengukur Kaki Anda:</h6>
                        <ol class="text-secondary mb-0 ps-3" style="font-size: 13px; line-height: 1.8;">
                            <li class="mb-2">Berdirilah di atas selembar kertas dengan tumit menyentuh dinding.</li>
                            <li class="mb-2">Tandai bagian ujung jari kaki terpanjang Anda di atas kertas.</li>
                            <li>Ukur jarak dari tepi kertas (bagian tumit) ke tanda yang telah Anda buat dalam satuan centimeter (CM).</li>
                        </ol>
                    </div>
                    
                </div>
                
                <div class="modal-footer border-top border-secondary-subtle rounded-0 px-4 py-3 bg-white">
                    <button type="button" class="btn btn-outline-dark rounded-0 fw-bold text-uppercase px-4 py-2" data-bs-dismiss="modal" style="font-size: 12px; letter-spacing: 1px;">Tutup</button>
                </div>
                
            </div>
        </div>
    </div>
@endsection
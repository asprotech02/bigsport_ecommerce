@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        <x-customer.topbar />
        <x-customer.navbar />
    </header>

    <section class="py-4 py-lg-5 bg-white">
        <div class="container">
            
            <div class="fw-bold text-uppercase mb-4 d-none d-md-block" style="font-size: 13px; letter-spacing: 1px;">
                <a href="{{ route('home') }}" class="text-dark text-decoration-none">BERANDA</a> 
                <span class="text-secondary mx-1">/</span> 
                <a href="{{ route('product.index', ['gender' => $product->gender]) }}" class="text-dark text-decoration-none hover-link">KATEGORI {{ $product->gender }}</a>
                <span class="text-secondary mx-1">/</span> 
                <a href="{{ route('product.index', ['gender' => $product->gender, 'category' => $product->category->name]) }}" class="text-dark text-decoration-none hover-link">{{ $product->category->name }}</a>
                <span class="text-secondary mx-1">/</span> 
                <span class="text-secondary">{{ $product->name }}</span>
            </div>

            <div class="bg-light-gray p-4 p-lg-5 mb-5 rounded-0">
                <div class="row g-5">
                    
                    <div class="col-12 col-lg-7 d-flex flex-column-reverse flex-md-row gap-3">
                        
                        <div class="d-flex flex-row flex-md-column gap-2 overflow-x-auto thumbnail-scroll" style="width: 100%; max-width: 85px;">
                            @foreach($product->images as $index => $image)
                                <div class="ratio ratio-1x1 border {{ $image->is_primary ? 'border-dark' : 'border-secondary-subtle opacity-50' }} bg-white flex-shrink-0 cursor-pointer thumb-item" 
                                     style="width: 85px; transition: all 0.2s;"
                                     data-full-src="{{ asset('storage/' . $image->image_path) }}">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" class="w-100 h-100 object-fit-cover">
                                </div>
                            @endforeach
                        </div>

                        <div class="flex-grow-1 position-relative bg-white border border-secondary-subtle d-flex align-items-center justify-content-center overflow-hidden">
                            <button class="btn btn-light rounded-circle position-absolute top-0 end-0 m-3 shadow-sm border-0 d-flex align-items-center justify-content-center" 
                                    id="btn-zoom" style="width: 40px; height: 40px; z-index: 10; opacity: 0.8; transition: all 0.3s;">
                                <i class="bi bi-search fs-5"></i>
                            </button>

                            <button class="btn rounded-0 position-absolute start-0 top-50 translate-middle-y ms-2 ms-md-3 border-0 d-flex justify-content-center align-items-center gallery-nav-btn" 
                                    id="prev-img" style="width: 40px; height: 40px; z-index: 2;">
                                <i class="bi bi-chevron-left text-white fs-5"></i>
                            </button>

                            <div class="ratio ratio-1x1 w-100" id="main-img-wrapper" style="overflow: hidden;">
                                <img src="{{ asset('storage/' . ($primaryImage ? $primaryImage->image_path : 'default.jpg')) }}" 
                                     id="main-product-img" 
                                     class="w-100 h-100 object-fit-cover" 
                                     style="transition: transform 0.1s ease-out, opacity 0.3s ease-in-out;">
                            </div>

                            <button class="btn rounded-0 position-absolute end-0 top-50 translate-middle-y me-2 me-md-3 border-0 d-flex justify-content-center align-items-center gallery-nav-btn" 
                                    id="next-img" style="width: 40px; height: 40px; z-index: 2;">
                                <i class="bi bi-chevron-right text-white fs-5"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5">
                        <p class="text-danger fw-bold mb-1 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">{{ $product->brand->name }}</p>
                        <h2 class="fw-black text-uppercase mb-1" style="font-size: 28px; line-height: 1.2;">
                            {{ $product->name }}
                        </h2>
                        <p class="text-dark fw-bold mb-2" style="font-size: 14px;">{{ $product->gender }}</p>
                        
                        <div class="mb-3">
                            @if($product->discount_price && $product->discount_price < $product->base_price)
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h3 class="fw-bolder mb-0 text-danger" style="font-size: 26px;">
                                        Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                                    </h3>
                                    <span class="text-secondary text-decoration-line-through" style="font-size: 16px;">
                                        Rp {{ number_format($product->base_price, 0, ',', '.') }}
                                    </span>
                                    @php
                                        $percent = round((($product->base_price - $product->discount_price) / $product->base_price) * 100);
                                    @endphp
                                    <span class="badge bg-danger rounded-0 px-2 py-1" style="font-size: 12px; letter-spacing: 0.5px;">
                                        {{ $percent }}% OFF
                                    </span>
                                </div>
                            @else
                                <h3 class="fw-bolder mb-0" style="font-size: 26px;">
                                    Rp {{ number_format($product->base_price, 0, ',', '.') }}
                                </h3>
                            @endif
                        </div>
                        
                        <div class="mb-4">
                            <span class="text-secondary" style="font-size: 12px;">{{ $totalSold ?? 0 }} Sold</span>
                            <div class="d-flex align-items-center gap-1 mt-1" style="font-size: 12px;">
                                <div class="text-warning">
                                    @php $avgRating = $product->reviews_avg_rating ?? 0; @endphp
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($avgRating >= $i)
                                            <i class="bi bi-star-fill"></i>
                                        @elseif($avgRating >= $i - 0.5)
                                            <i class="bi bi-star-half"></i>
                                        @else
                                            <i class="bi bi-star text-secondary"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-secondary ms-1">{{ number_format($avgRating, 1) }} /</span>
                                <span class="text-secondary fw-bold">Ulasan ({{ $product->reviews_count ?? 0 }})</span>
                            </div>
                        </div>

                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <div class="mb-2">
                                    <span class="fw-bold d-block" style="font-size: 14px;">Ukuran</span>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#sizeGuideModal" class="text-dark fw-bold text-decoration-none mt-1 d-inline-block" style="font-size: 11px; border-bottom: 1px solid #000;">Panduan Size</a>
                                </div>

                                <input type="hidden" name="sku_id" id="selected-sku-id" value="">

                                @php
                                    $standardSizes = [];
                                    if ($product->category->name == 'Sepatu') {
                                        $standardSizes = ['38', '39', '40', '41', '42', '43', '44'];
                                    } elseif ($product->category->name == 'Pakaian') {
                                        $standardSizes = ['S', 'M', 'L', 'XL', 'XXL'];
                                    } else {
                                        $standardSizes = ['All Size'];
                                    }

                                    $skuLookup = [];
                                    foreach($product->skus as $sku) {
                                        $skuLookup[$sku->size] = [
                                            'id' => $sku->id,
                                            'stock' => $sku->stock
                                        ];
                                    }
                                @endphp

                                <div class="d-flex flex-wrap gap-2 mt-2" id="size-button-container">
                                    @foreach($standardSizes as $size)
                                        @php
                                            $skuData = $skuLookup[$size] ?? null;
                                            $hasStock = $skuData && $skuData['stock'] > 0;
                                        @endphp

                                        @if($hasStock)
                                            <button type="button" 
                                                    class="btn btn-outline-dark rounded-0 px-2 py-1 fw-bold size-btn" 
                                                    style="font-size: 12px; min-width: 35px;"
                                                    data-sku-id="{{ $skuData['id'] }}"
                                                    data-stock="{{ $skuData['stock'] }}">
                                                {{ $size }}
                                            </button>
                                        @else
                                            <button type="button" 
                                                    class="btn btn-outline-secondary bg-light text-muted rounded-0 px-2 py-1 fw-bold size-btn disabled" 
                                                    style="font-size: 12px; min-width: 35px; border-style: dashed;" 
                                                    disabled>
                                                {{ $size }}
                                            </button>
                                        @endif
                                    @endforeach
                                </div>

                                <div id="stock-status-container" class="mt-2 pt-1" style="display: none;">
                                    <span class="text-secondary me-1" style="font-size: 12px;">Sisa stok:</span>
                                    <span id="stock-display" class="fw-bold text-dark" style="font-size: 13px;">-</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold d-block mb-2" style="font-size: 14px;">Kuantitas</label>
                                <div class="border border-dark d-inline-flex align-items-center bg-white" style="height: 38px;">
                                    <button type="button" id="btn-qty-minus" class="btn border-0 rounded-0 fw-bold px-3 py-0 shadow-none text-dark hover-opacity fs-5">-</button>
                                    
                                    <input type="text" name="quantity" id="qty-input" value="1" class="border-0 text-center fw-bold text-dark p-0" style="width: 40px; outline: none; background: transparent; font-size: 14px;" readonly>
                                    
                                    <button type="button" id="btn-qty-plus" class="btn border-0 rounded-0 fw-bold px-3 py-0 shadow-none text-dark hover-opacity fs-5">+</button>
                                </div>
                                <span id="qty-warning" class="text-danger d-block mt-1 fw-bold" style="font-size: 11px; display: none !important;"></span>
                            </div>

                            <div class="row g-2 mt-4">
                                <div class="col-12 col-sm-6">
                                    <button type="submit" name="action" value="buy_now" class="btn btn-outline-dark w-100 rounded-0 fw-bold text-uppercase d-flex justify-content-center align-items-center" style="height: 48px; font-size: 12px; letter-spacing: 1px; border-width: 2px;">
                                        BELI SEKARANG
                                    </button>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <button type="submit" name="action" value="add_cart" class="btn btn-action-main m-0 w-100 d-flex justify-content-center align-items-center" style="height: 48px; font-size: 12px; padding: 0;">
                                        TAMBAH KE KERANJANG
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="mt-4 pt-3">
                            <span class="text-secondary d-block mb-2" style="font-size: 12px;">Sedia Metode Pembayaran:</span>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="bg-white border border-light-subtle d-flex align-items-center justify-content-center p-1" style="width: 55px; height: 32px;">
                                    <img src="{{ asset('assets/customer/icon/Icon-bca.png') }}" alt="BCA" class="w-100 h-100" style="object-fit: contain;">
                                </div>
                                <div class="bg-white border border-light-subtle d-flex align-items-center justify-content-center p-1" style="width: 55px; height: 32px;">
                                    <img src="{{ asset('assets/customer/icon/Icon-qris.png') }}" alt="QRIS" class="w-100 h-100" style="object-fit: contain;">
                                </div>
                                <div class="bg-white border border-light-subtle d-flex align-items-center justify-content-center p-1" style="width: 55px; height: 32px;">
                                    <img src="{{ asset('assets/customer/icon/Icon-mastercard.png') }}" alt="Mastercard" class="w-100 h-100" style="object-fit: contain;">
                                </div>
                                <div class="bg-white border border-light-subtle d-flex align-items-center justify-content-center p-1" style="width: 55px; height: 32px;">
                                    <img src="{{ asset('assets/customer/icon/Icon-visa.svg') }}" alt="Visa" class="w-100 h-100" style="object-fit: contain;">
                                </div>
                                <div class="bg-white border border-light-subtle d-flex align-items-center justify-content-center p-1" style="width: 55px; height: 32px;">
                                    <img src="{{ asset('assets/customer/icon/Icon-Jcb.png') }}" alt="JCB" class="w-100 h-100" style="object-fit: contain;">
                                </div>
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
                                @php $avgRating = $product->reviews_avg_rating ?? 0; @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($avgRating >= $i)
                                        <i class="bi bi-star-fill"></i>
                                    @elseif($avgRating >= $i - 0.5)
                                        <i class="bi bi-star-half"></i>
                                    @else
                                        <i class="bi bi-star text-secondary"></i>
                                    @endif
                                @endfor
                            </span> 
                            <span style="font-size: 14px;">({{ $product->reviews_count ?? 0 }})</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="productTabsContent">
                    <div class="tab-pane fade show active p-4 p-md-5 bg-white" id="detail-content" role="tabpanel" aria-labelledby="detail-tab">
                        <h6 class="fw-bold text-uppercase mb-4">{{ $product->name }}</h6>
                        <p class="text-secondary mb-5" style="font-size: 14px; line-height: 1.8;">
                            {!! nl2br(e($product->description)) !!}
                        </p>
                        <h6 class="fw-bold text-uppercase mb-3">Detail Kategori</h6>
                        <ul class="text-secondary mb-5" style="font-size: 14px; line-height: 1.8;">
                            <li>Kategori Utama: {{ $product->category->name }}</li>
                            <li>Subkategori: {{ $product->subcategory->name }}</li>
                            <li>Gender: {{ $product->gender }}</li>
                            <li>Berat: {{ $product->weight_gram }} Gram</li>
                        </ul>
                    </div>

                    <div class="tab-pane fade p-4 p-md-5 bg-white" id="ulasan-content" role="tabpanel" aria-labelledby="ulasan-tab">
                        <div class="row g-5">
                            <div class="col-12 col-md-4 col-lg-3">
                                <h6 class="fw-bold text-uppercase mb-4">Ringkasan Ulasan</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <h1 class="fw-black m-0 me-3" style="font-size: 48px;">{{ number_format($avgRating, 1) }}</h1>
                                    <div>
                                        <div class="text-warning mb-1 fs-5">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($avgRating >= $i)
                                                    <i class="bi bi-star-fill"></i>
                                                @elseif($avgRating >= $i - 0.5)
                                                    <i class="bi bi-star-half"></i>
                                                @else
                                                    <i class="bi bi-star text-secondary"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="text-secondary" style="font-size: 13px;">Berdasarkan {{ $product->reviews_count ?? 0 }} Ulasan</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12 col-md-8 col-lg-9">
                                @forelse($product->reviews as $review)
                                    <div class="border-bottom border-secondary-subtle pb-4 mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="fw-bold fs-6">
                                                {{ $review->user->name }} 
                                                <i class="bi bi-patch-check-fill text-success ms-1" style="font-size: 12px;" title="Pembeli Terverifikasi"></i>
                                            </div>
                                            <div class="text-secondary" style="font-size: 12px;">
                                                {{ \Carbon\Carbon::parse($review->created_at)->translatedFormat('d F Y') }}
                                            </div>
                                        </div>
                                        
                                        <div class="text-warning mb-3" style="font-size: 12px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="bi bi-star-fill"></i>
                                                @else
                                                    <i class="bi bi-star text-secondary"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        
                                        <p class="text-secondary mb-0" style="font-size: 14px; line-height: 1.6;">
                                            {{ $review->comment }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="bi bi-chat-square-text text-secondary mb-3 d-block" style="font-size: 32px;"></i>
                                        <h6 class="fw-bold text-uppercase mb-2">Belum Ada Ulasan</h6>
                                        <p class="text-secondary mb-0" style="font-size: 14px;">Jadilah yang pertama memberikan ulasan untuk produk ini setelah membeli!</p>
                                    </div>
                                @endforelse

                                @if($product->reviews->count() > 3)
                                    <button type="button" class="btn btn-outline-dark rounded-0 fw-bold text-uppercase px-4 py-2 mt-2" style="font-size: 12px; letter-spacing: 1px;">
                                        Lihat Lebih Banyak
                                    </button>
                                @endif
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
                        Panduan Ukuran {{ $product->category->name }} {{ $product->brand->name }} ({{ $product->gender }})
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4 p-md-5">
                    @if($product->category->name == 'Sepatu')
                        <p class="text-secondary mb-4" style="font-size: 14px; line-height: 1.6;">
                            Gunakan tabel di bawah ini untuk menentukan ukuran yang paling tepat berdasarkan panjang kaki (dalam centimeter). Jika ukuran Anda berada di antara dua size, kami menyarankan memilih ukuran yang lebih besar.
                        </p>
                        <div class="table-responsive">
                            <table class="table table-bordered border-dark text-center align-middle mb-0" style="font-size: 13px;">
                                <thead class="bg-light-gray fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                                    <tr>
                                        <th scope="col" class="py-3">EU (Eropa)</th>
                                        <th scope="col" class="py-3">UK</th>
                                        <th scope="col" class="py-3">US</th>
                                        <th scope="col" class="py-3 bg-dark text-white">Panjang Kaki (CM)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($product->brand->name == 'Adidas')
                                        <tr><td class="fw-bold">39 1/3</td><td>6</td><td>6.5</td><td class="fw-bold bg-light">24.5 cm</td></tr>
                                        <tr><td class="fw-bold">40</td><td>6.5</td><td>7</td><td class="fw-bold bg-light">25.0 cm</td></tr>
                                        <tr><td class="fw-bold">41 1/3</td><td>7.5</td><td>8</td><td class="fw-bold bg-light">26.0 cm</td></tr>
                                        <tr><td class="fw-bold">42</td><td>8</td><td>8.5</td><td class="fw-bold bg-light">26.5 cm</td></tr>
                                    @elseif($product->brand->name == 'Nike')
                                        <tr><td class="fw-bold">39</td><td>6</td><td>6.5</td><td class="fw-bold bg-light">24.5 cm</td></tr>
                                        <tr><td class="fw-bold">40</td><td>6</td><td>7</td><td class="fw-bold bg-light">25.0 cm</td></tr>
                                        <tr><td class="fw-bold">41</td><td>7</td><td>8</td><td class="fw-bold bg-light">26.0 cm</td></tr>
                                        <tr><td class="fw-bold">42</td><td>7.5</td><td>8.5</td><td class="fw-bold bg-light">26.5 cm</td></tr>
                                    @else
                                        <tr><td class="fw-bold">39</td><td>6</td><td>7</td><td class="fw-bold bg-light">25.0 cm</td></tr>
                                        <tr><td class="fw-bold">40</td><td>6.5</td><td>7.5</td><td class="fw-bold bg-light">25.5 cm</td></tr>
                                        <tr><td class="fw-bold">41</td><td>7</td><td>8</td><td class="fw-bold bg-light">26.5 cm</td></tr>
                                        <tr><td class="fw-bold">42</td><td>7.5</td><td>8.5</td><td class="fw-bold bg-light">27.0 cm</td></tr>
                                    @endif
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

                    @elseif($product->category->name == 'Pakaian')
                        <p class="text-secondary mb-4" style="font-size: 14px; line-height: 1.6;">
                            Gunakan panduan di bawah ini untuk mengukur tubuh Anda dan menentukan ukuran pakaian yang sesuai. Ukuran dapat memiliki toleransi perbedaan 1-2 cm.
                        </p>
                        @if($product->subcategory->name == 'Celana')
                            <div class="table-responsive">
                                <table class="table table-bordered border-dark text-center align-middle mb-0" style="font-size: 13px;">
                                    <thead class="bg-light-gray fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                                        <tr>
                                            <th scope="col" class="py-3 bg-dark text-white">Size</th>
                                            <th scope="col" class="py-3">Lingkar Pinggang (CM)</th>
                                            <th scope="col" class="py-3">Lingkar Paha (CM)</th>
                                            <th scope="col" class="py-3">Panjang (CM)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td class="fw-bold bg-light">S (28-29)</td><td>72 - 76 cm</td><td>54 cm</td><td>98 cm</td></tr>
                                        <tr><td class="fw-bold bg-light">M (30-31)</td><td>77 - 81 cm</td><td>56 cm</td><td>100 cm</td></tr>
                                        <tr><td class="fw-bold bg-light">L (32-33)</td><td>82 - 86 cm</td><td>58 cm</td><td>102 cm</td></tr>
                                        <tr><td class="fw-bold bg-light">XL (34-35)</td><td>87 - 91 cm</td><td>60 cm</td><td>104 cm</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered border-dark text-center align-middle mb-0" style="font-size: 13px;">
                                    <thead class="bg-light-gray fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                                        <tr>
                                            <th scope="col" class="py-3 bg-dark text-white">Size</th>
                                            <th scope="col" class="py-3">Lebar Dada (CM)</th>
                                            <th scope="col" class="py-3">Panjang Baju (CM)</th>
                                            @if($product->subcategory->name == 'Hoodie')
                                                <th scope="col" class="py-3">Panjang Lengan (CM)</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($product->brand->name == 'Adidas' || $product->brand->name == 'Nike')
                                            <tr><td class="fw-bold bg-light">S</td><td>48 - 50 cm</td><td>68 - 70 cm</td> @if($product->subcategory->name == 'Hoodie') <td>62 cm</td> @endif </tr>
                                            <tr><td class="fw-bold bg-light">M</td><td>51 - 53 cm</td><td>71 - 73 cm</td> @if($product->subcategory->name == 'Hoodie') <td>63 cm</td> @endif </tr>
                                            <tr><td class="fw-bold bg-light">L</td><td>54 - 56 cm</td><td>74 - 76 cm</td> @if($product->subcategory->name == 'Hoodie') <td>64 cm</td> @endif </tr>
                                            <tr><td class="fw-bold bg-light">XL</td><td>57 - 60 cm</td><td>77 - 79 cm</td> @if($product->subcategory->name == 'Hoodie') <td>65 cm</td> @endif </tr>
                                        @else
                                            <tr><td class="fw-bold bg-light">S</td><td>46 cm</td><td>66 cm</td> @if($product->subcategory->name == 'Hoodie') <td>60 cm</td> @endif </tr>
                                            <tr><td class="fw-bold bg-light">M</td><td>48 cm</td><td>68 cm</td> @if($product->subcategory->name == 'Hoodie') <td>61 cm</td> @endif </tr>
                                            <tr><td class="fw-bold bg-light">L</td><td>50 cm</td><td>70 cm</td> @if($product->subcategory->name == 'Hoodie') <td>62 cm</td> @endif </tr>
                                            <tr><td class="fw-bold bg-light">XL</td><td>52 cm</td><td>72 cm</td> @if($product->subcategory->name == 'Hoodie') <td>63 cm</td> @endif </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-info-circle text-secondary mb-3 d-block" style="font-size: 32px;"></i>
                            <h6 class="fw-bold text-uppercase">ALL SIZE (SATU UKURAN)</h6>
                            <p class="text-secondary" style="font-size: 14px;">Produk aksesoris ini dirancang untuk ukuran yang fleksibel (All Size / Adjustable) sehingga muat untuk berbagai bentuk dan ukuran tubuh.</p>
                        </div>
                    @endif
                </div>
                
                <div class="modal-footer border-top border-secondary-subtle rounded-0 px-4 py-3 bg-white">
                    <button type="button" class="btn btn-outline-dark rounded-0 fw-bold text-uppercase px-4 py-2" data-bs-dismiss="modal" style="font-size: 12px; letter-spacing: 1px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // ==========================================
            // 1. LOGIKA STOK & KUANTITAS
            // ==========================================
            const sizeButtons = document.querySelectorAll('.size-btn:not(.disabled)');
            const stockContainer = document.getElementById('stock-status-container');
            const stockDisplay = document.getElementById('stock-display');
            const skuInput = document.getElementById('selected-sku-id');
            const qtyInput = document.getElementById('qty-input');
            const btnMinus = document.getElementById('btn-qty-minus');
            const btnPlus = document.getElementById('btn-qty-plus');
            const qtyWarning = document.getElementById('qty-warning');

            let currentMaxStock = 0;

            function selectSize(button) {
                sizeButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                currentMaxStock = parseInt(button.getAttribute('data-stock'));
                skuInput.value = button.getAttribute('data-sku-id');

                stockContainer.style.display = 'block';
                stockDisplay.innerText = currentMaxStock + ' Pcs';

                if (currentMaxStock <= 5) {
                    stockDisplay.classList.replace('text-dark', 'text-danger');
                } else {
                    stockDisplay.classList.replace('text-danger', 'text-dark');
                }

                qtyInput.value = 1;
                qtyWarning.style.setProperty('display', 'none', 'important');
            }

            sizeButtons.forEach(button => {
                button.addEventListener('click', function() { selectSize(this); });
            });

            if (sizeButtons.length > 0) { selectSize(sizeButtons[0]); }

            btnPlus.addEventListener('click', function() {
                let currentQty = parseInt(qtyInput.value);
                if (currentQty < currentMaxStock) {
                    qtyInput.value = currentQty + 1;
                    qtyWarning.style.setProperty('display', 'none', 'important');
                } else {
                    qtyWarning.innerText = "Maksimal pembelian untuk ukuran ini adalah " + currentMaxStock + " pcs.";
                    qtyWarning.style.setProperty('display', 'block', 'important');
                }
            });

            btnMinus.addEventListener('click', function() {
                let currentQty = parseInt(qtyInput.value);
                if (currentQty > 1) {
                    qtyInput.value = currentQty - 1;
                    qtyWarning.style.setProperty('display', 'none', 'important');
                }
            });

            // ==========================================
            // 2. LOGIKA GALERI & INTERACTIVE ZOOM
            // ==========================================
            const mainImg = document.getElementById('main-product-img');
            const imgWrapper = document.getElementById('main-img-wrapper');
            const thumbs = document.querySelectorAll('.thumb-item');
            const btnNext = document.getElementById('next-img');
            const btnPrev = document.getElementById('prev-img');
            const btnZoom = document.getElementById('btn-zoom');
            
            let currentIndex = 0;
            let isZoomed = false;
            const images = Array.from(thumbs).map(t => t.getAttribute('data-full-src'));

            // Fungsi Toggle Zoom
            function toggleZoom() {
                isZoomed = !isZoomed;
                if (isZoomed) {
                    mainImg.style.transform = 'scale(2.5)';
                    mainImg.style.cursor = 'zoom-out';
                    btnZoom.innerHTML = '<i class="bi bi-zoom-out fs-5"></i>';
                    btnZoom.classList.replace('btn-light', 'btn-dark');
                } else {
                    mainImg.style.transform = 'scale(1)';
                    mainImg.style.cursor = 'default';
                    mainImg.style.transformOrigin = 'center center';
                    btnZoom.innerHTML = '<i class="bi bi-search fs-5"></i>';
                    btnZoom.classList.replace('btn-dark', 'btn-light');
                }
            }

            function resetZoom() {
                if (isZoomed) toggleZoom();
            }

            // Fungsi Ganti Gambar
            function updateMainImage(index) {
                resetZoom(); // Pastikan zoom ter-reset saat ganti gambar
                mainImg.style.opacity = '0';
                setTimeout(() => {
                    mainImg.src = images[index];
                    mainImg.style.opacity = '1';
                    
                    thumbs.forEach((t, i) => {
                        if(i === index) {
                            t.classList.add('border-dark');
                            t.classList.remove('opacity-50');
                            t.style.borderWidth = "2px";
                        } else {
                            t.classList.remove('border-dark');
                            t.classList.add('opacity-50');
                            t.style.borderWidth = "1px";
                        }
                    });
                }, 200);
                currentIndex = index;
            }

            // Event Listeners Galeri
            thumbs.forEach((thumb, index) => {
                thumb.addEventListener('click', () => updateMainImage(index));
            });

            btnNext.addEventListener('click', () => {
                updateMainImage((currentIndex + 1) % images.length);
            });

            btnPrev.addEventListener('click', () => {
                updateMainImage((currentIndex - 1 + images.length) % images.length);
            });

            // Event Listeners Zoom & Pan
            btnZoom.addEventListener('click', toggleZoom);
            
            mainImg.addEventListener('click', function() {
                if (isZoomed) toggleZoom();
            });

            imgWrapper.addEventListener('mousemove', function(e) {
                if (!isZoomed) return;
                const rect = imgWrapper.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const xPercent = (x / rect.width) * 100;
                const yPercent = (y / rect.height) * 100;
                mainImg.style.transformOrigin = `${xPercent}% ${yPercent}%`;
            });

            imgWrapper.addEventListener('mouseleave', function() {
                if (isZoomed) toggleZoom();
            });

        });
    </script>
@endsection
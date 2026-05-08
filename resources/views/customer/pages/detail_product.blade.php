@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    @php
        // 🌟 FIX LOGIKA STOK, HARGA & MATRIX WARNA:
        $standardSizes = [];
        $isKids = str_contains(strtolower($product->gender), 'anak');

        if ($product->category->name == 'Sepatu') {
            $standardSizes = $isKids ? ['19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30'] : ['31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44'];
        } elseif ($product->category->name == 'Pakaian') {
            $standardSizes = $isKids ? ['S', 'M', 'L', 'XL'] : ['S', 'M', 'L', 'XL', 'XXL'];
        } else {
            $standardSizes = ['All Size'];
        }

        $skus = $product->skus;
        
        // Ekstrak Warna dan Ukuran yang tersedia
        $availableColors = $skus->pluck('color')->filter()->unique()->values();
        $hasColors = $availableColors->count() > 0;
        
        // Saring ukuran agar berurutan sesuai $standardSizes lu
        $availableSizes = collect($standardSizes)->filter(function($size) use ($skus) {
            return $skus->where('size', $size)->count() > 0;
        })->values();

        $totalValidStock = 0; 
        
        // 🌟 AUTO-SELECT: Cari SKU pertama yang ada stok tersedia buat patokan
        $defaultSku = $skus->where('available_stock', '>', 0)->first();
        
        $defaultBasePrice = $defaultSku ? $defaultSku->base_price : ($skus->first()->base_price ?? 0);
        $defaultDiscountPrice = $defaultSku ? $defaultSku->discount_price : ($skus->first()->discount_price ?? null);
        
        // Variabel untuk dilempar ke JS agar otomatis terpilih
        $defaultColor = $defaultSku ? $defaultSku->color : null;
        $defaultSize = $defaultSku ? $defaultSku->size : null;

        // Hitung total stok valid
        foreach($skus as $sku) {
            if(in_array($sku->size, $standardSizes) && $sku->available_stock > 0) {
                $totalValidStock += $sku->available_stock;
            }
        }
    @endphp

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

                            <div class="ratio ratio-1x1 w-100 position-relative" id="main-img-wrapper" style="overflow: hidden;">
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
                        
                        <div class="mb-3" id="price-container">
                            @if($defaultDiscountPrice)
                                <div class="d-flex align-items-center gap-2 flex-wrap" id="discount-view">
                                    <h3 class="fw-bolder mb-0 text-danger" id="current-price" style="font-size: 26px;">
                                        Rp {{ number_format($defaultDiscountPrice, 0, ',', '.') }}
                                    </h3>
                                    <span class="text-secondary text-decoration-line-through" id="original-price" style="font-size: 16px;">
                                        Rp {{ number_format($defaultBasePrice, 0, ',', '.') }}
                                    </span>
                                    @php
                                        $percent = round((($defaultBasePrice - $defaultDiscountPrice) / $defaultBasePrice) * 100);
                                    @endphp
                                    <span class="badge bg-danger rounded-0 px-2 py-1" id="discount-badge" style="font-size: 12px; letter-spacing: 0.5px;">
                                        {{ $percent }}% OFF
                                    </span>
                                </div>
                                <div id="normal-view" style="display: none;">
                                    <h3 class="fw-bolder mb-0" id="normal-price" style="font-size: 26px;"></h3>
                                </div>
                            @else
                                <div class="d-flex align-items-center gap-2 flex-wrap" id="discount-view" style="display: none !important;">
                                    <h3 class="fw-bolder mb-0 text-danger" id="current-price" style="font-size: 26px;"></h3>
                                    <span class="text-secondary text-decoration-line-through" id="original-price" style="font-size: 16px;"></span>
                                    <span class="badge bg-danger rounded-0 px-2 py-1" id="discount-badge" style="font-size: 12px; letter-spacing: 0.5px;"></span>
                                </div>
                                <div id="normal-view">
                                    <h3 class="fw-bolder mb-0" id="normal-price" style="font-size: 26px;">
                                        Rp {{ number_format($defaultBasePrice, 0, ',', '.') }}
                                    </h3>
                                </div>
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

                        <form action="{{ route('cart.add') }}" method="POST" id="add-to-cart-form">
                            @csrf
                            <input type="hidden" name="sku_id" id="selected-sku-id" value="">

                            @if($hasColors)
                            <div class="mb-4">
                                <span class="fw-bold d-block mb-2" style="font-size: 14px;">Warna</span>
                                <div class="d-flex flex-wrap gap-2" id="color-button-container">
                                    @foreach($availableColors as $color)
                                        <button type="button" class="btn btn-outline-dark rounded-0 px-3 py-1 fw-bold color-btn" 
                                                data-color="{{ $color }}" style="font-size: 12px;">
                                            {{ $color }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <div class="mb-4">
                                <div class="mb-2">
                                    <span class="fw-bold d-block" style="font-size: 14px;">Ukuran</span>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#sizeGuideModal" class="text-dark fw-bold text-decoration-none mt-1 d-inline-block" style="font-size: 11px; border-bottom: 1px solid #000;">Panduan Size</a>
                                </div>

                                <div class="d-flex flex-wrap gap-2 mt-2" id="size-button-container">
                                    @foreach($availableSizes as $size)
                                        <button type="button" 
                                                class="btn btn-outline-dark rounded-0 px-2 py-1 fw-bold size-btn" 
                                                style="font-size: 12px; min-width: 35px;"
                                                data-size="{{ $size }}">
                                            {{ $size }}
                                        </button>
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
                                @if($totalValidStock > 0)
                                    <div class="col-12 col-sm-6">
                                        <button type="submit" name="action" value="buy_now" id="btn-buy-now" class="btn btn-outline-dark w-100 rounded-0 fw-bold text-uppercase d-flex justify-content-center align-items-center" style="height: 48px; font-size: 12px; letter-spacing: 1px; border-width: 2px;">
                                            PILIH VARIAN
                                        </button>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <button type="submit" name="action" value="add_cart" id="btn-add-cart" class="btn btn-action-main m-0 w-100 d-flex justify-content-center align-items-center" style="height: 48px; font-size: 12px; padding: 0;">
                                            PILIH VARIAN
                                        </button>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <button type="button" class="btn btn-secondary w-100 rounded-0 fw-bold text-uppercase d-flex justify-content-center align-items-center" style="height: 48px; font-size: 12px; letter-spacing: 1px; opacity: 0.7; cursor: not-allowed;" disabled>
                                            MAAF, STOK HABIS
                                        </button>
                                    </div>
                                @endif
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
                                                {{ $review->user?->name ?? 'Pengguna Anonim' }} 
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

    @if(isset($recommendedProducts) && $recommendedProducts->count() > 0)
    <section class="py-5 bg-white border-secondary-subtle">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 0.5px;">PRODUK REKOMENDASI</h4>
                <a href="{{ route('product.index', ['category' => $product->category->name]) }}" class="link-lihat-semua">CEK SEMUA</a>
            </div>
            
            <div class="d-flex flex-nowrap overflow-x-auto gap-4 pb-4 align-items-stretch" style="scrollbar-width: thin; -webkit-overflow-scrolling: touch;">
                @foreach($recommendedProducts as $recommendation)
                <div class="flex-shrink-0 d-flex" style="width: 280px;">
                    <div class="w-100 h-100 product-card-stretcher">
                        @include('customer.components.product_card', ['product' => $recommendation])
                    </div>
                </div>
                @endforeach
            </div>
            
        </div>
    </section>
    @endif
    @include('customer.components.footer')
    @include('customer.components.chatbot')
    
    <div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-labelledby="sizeGuideModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-0 border-dark" style="border-width: 2px;">
                <div class="modal-header border-bottom border-dark rounded-0 px-4 py-3">
                    <h5 class="modal-title fw-bold text-uppercase" id="sizeGuideModalLabel" style="font-size: 15px; letter-spacing: 1px;">
                        Panduan Ukuran {{ $product->category->name }} ({{ $product->gender }})
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4 p-md-5">
                    @php
                        $isKids = str_contains(strtolower($product->gender), 'anak');
                    @endphp

                    @if($product->category->name == 'Sepatu')
                        <p class="text-secondary mb-4" style="font-size: 14px; line-height: 1.6;">
                            Gunakan tabel panduan di bawah ini untuk menentukan ukuran sepatu yang tepat berdasarkan panjang kaki (dalam centimeter).
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
                                    @if($isKids)
                                        <tr><td class="fw-bold">19</td><td>3K</td><td>4C</td><td class="fw-bold bg-light">11.5 cm</td></tr>
                                        <tr><td class="fw-bold">20</td><td>4K</td><td>5C</td><td class="fw-bold bg-light">12.0 cm</td></tr>
                                        <tr><td class="fw-bold">21</td><td>5K</td><td>5.5C</td><td class="fw-bold bg-light">12.5 cm</td></tr>
                                        <tr><td class="fw-bold">22</td><td>5.5K</td><td>6C</td><td class="fw-bold bg-light">13.0 cm</td></tr>
                                        <tr><td class="fw-bold">23</td><td>6K</td><td>6.5C</td><td class="fw-bold bg-light">14.0 cm</td></tr>
                                        <tr><td class="fw-bold">24</td><td>7K</td><td>7.5C</td><td class="fw-bold bg-light">14.5 cm</td></tr>
                                        <tr><td class="fw-bold">25</td><td>7.5K</td><td>8C</td><td class="fw-bold bg-light">15.0 cm</td></tr>
                                        <tr><td class="fw-bold">26</td><td>8.5K</td><td>9C</td><td class="fw-bold bg-light">16.0 cm</td></tr>
                                        <tr><td class="fw-bold">27</td><td>9K</td><td>10C</td><td class="fw-bold bg-light">16.5 cm</td></tr>
                                        <tr><td class="fw-bold">28</td><td>10K</td><td>10.5C</td><td class="fw-bold bg-light">17.0 cm</td></tr>
                                        <tr><td class="fw-bold">29</td><td>11K</td><td>11.5C</td><td class="fw-bold bg-light">18.0 cm</td></tr>
                                        <tr><td class="fw-bold">30</td><td>11.5K</td><td>12C</td><td class="fw-bold bg-light">18.5 cm</td></tr>
                                    @else
                                        <tr><td class="fw-bold">31</td><td>12.5K</td><td>13C</td><td class="fw-bold bg-light">19.0 cm</td></tr>
                                        <tr><td class="fw-bold">32</td><td>13K</td><td>1Y</td><td class="fw-bold bg-light">19.5 cm</td></tr>
                                        <tr><td class="fw-bold">33</td><td>1</td><td>1.5Y</td><td class="fw-bold bg-light">20.0 cm</td></tr>
                                        <tr><td class="fw-bold">34</td><td>2</td><td>2.5Y</td><td class="fw-bold bg-light">21.0 cm</td></tr>
                                        <tr><td class="fw-bold">35</td><td>2.5</td><td>3</td><td class="fw-bold bg-light">22.0 cm</td></tr>
                                        <tr><td class="fw-bold">36</td><td>3.5</td><td>4</td><td class="fw-bold bg-light">22.5 cm</td></tr>
                                        <tr><td class="fw-bold">37</td><td>4.5</td><td>5</td><td class="fw-bold bg-light">23.5 cm</td></tr>
                                        <tr><td class="fw-bold">38</td><td>5.5</td><td>6</td><td class="fw-bold bg-light">24.0 cm</td></tr>
                                        <tr><td class="fw-bold">39</td><td>6</td><td>6.5</td><td class="fw-bold bg-light">24.5 cm</td></tr>
                                        <tr><td class="fw-bold">40</td><td>6.5</td><td>7</td><td class="fw-bold bg-light">25.0 cm</td></tr>
                                        <tr><td class="fw-bold">41</td><td>7.5</td><td>8</td><td class="fw-bold bg-light">26.0 cm</td></tr>
                                        <tr><td class="fw-bold">42</td><td>8</td><td>8.5</td><td class="fw-bold bg-light">26.5 cm</td></tr>
                                        <tr><td class="fw-bold">43</td><td>9</td><td>9.5</td><td class="fw-bold bg-light">27.5 cm</td></tr>
                                        <tr><td class="fw-bold">44</td><td>9.5</td><td>10</td><td class="fw-bold bg-light">28.0 cm</td></tr>
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
                            Gunakan panduan ukuran di bawah ini untuk menentukan pakaian yang sesuai. Ukuran memiliki toleransi perbedaan 1-2 cm dari ukuran aslinya.
                        </p>
                        
                        @if($product->subcategory->name == 'Celana')
                            <div class="table-responsive">
                                <table class="table table-bordered border-dark text-center align-middle mb-0" style="font-size: 13px;">
                                    <thead class="bg-light-gray fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                                        <tr>
                                            <th scope="col" class="py-3 bg-dark text-white">Size</th>
                                            @if($isKids)
                                                <th scope="col" class="py-3">Perkiraan Umur</th>
                                            @endif
                                            <th scope="col" class="py-3">Lingkar Pinggang (CM)</th>
                                            <th scope="col" class="py-3">Panjang (CM)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($isKids)
                                            <tr><td class="fw-bold bg-light">S</td><td>4 - 5 Tahun</td><td>50 - 58 cm</td><td>65 cm</td></tr>
                                            <tr><td class="fw-bold bg-light">M</td><td>6 - 7 Tahun</td><td>54 - 62 cm</td><td>70 cm</td></tr>
                                            <tr><td class="fw-bold bg-light">L</td><td>8 - 9 Tahun</td><td>58 - 66 cm</td><td>75 cm</td></tr>
                                            <tr><td class="fw-bold bg-light">XL</td><td>10 - 11 Tahun</td><td>62 - 70 cm</td><td>80 cm</td></tr>
                                        @else
                                            <tr><td class="fw-bold bg-light">S (28-29)</td><td>72 - 76 cm</td><td>98 cm</td></tr>
                                            <tr><td class="fw-bold bg-light">M (30-31)</td><td>77 - 81 cm</td><td>100 cm</td></tr>
                                            <tr><td class="fw-bold bg-light">L (32-33)</td><td>82 - 86 cm</td><td>102 cm</td></tr>
                                            <tr><td class="fw-bold bg-light">XL (34-35)</td><td>87 - 91 cm</td><td>104 cm</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered border-dark text-center align-middle mb-0" style="font-size: 13px;">
                                    <thead class="bg-light-gray fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                                        <tr>
                                            <th scope="col" class="py-3 bg-dark text-white">Size</th>
                                            @if($isKids)
                                                <th scope="col" class="py-3">Perkiraan Umur</th>
                                            @endif
                                            <th scope="col" class="py-3">Lebar Dada (CM)</th>
                                            <th scope="col" class="py-3">Panjang Baju (CM)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($isKids)
                                            <tr><td class="fw-bold bg-light">S</td><td>4 - 5 Tahun</td><td>36 cm</td><td>48 cm</td></tr>
                                            <tr><td class="fw-bold bg-light">M</td><td>6 - 7 Tahun</td><td>38 cm</td><td>51 cm</td></tr>
                                            <tr><td class="fw-bold bg-light">L</td><td>8 - 9 Tahun</td><td>40 cm</td><td>54 cm</td></tr>
                                            <tr><td class="fw-bold bg-light">XL</td><td>10 - 11 Tahun</td><td>42 cm</td><td>57 cm</td></tr>
                                        @else
                                            <tr><td class="fw-bold bg-light">S</td><td>48 cm</td><td>68 cm</td></tr>
                                            <tr><td class="fw-bold bg-light">M</td><td>50 cm</td><td>70 cm</td></tr>
                                            <tr><td class="fw-bold bg-light">L</td><td>52 cm</td><td>72 cm</td></tr>
                                            <tr><td class="fw-bold bg-light">XL</td><td>55 cm</td><td>75 cm</td></tr>
                                            <tr><td class="fw-bold bg-light">XXL</td><td>58 cm</td><td>78 cm</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @endif

                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-info-circle text-secondary mb-3 d-block" style="font-size: 32px;"></i>
                            <h6 class="fw-bold text-uppercase">ALL SIZE (SATU UKURAN)</h6>
                            <p class="text-secondary" style="font-size: 14px;">
                                Produk aksesoris ini dirancang untuk ukuran yang fleksibel (All Size / Adjustable) sehingga muat untuk berbagai bentuk dan ukuran.
                            </p>
                        </div>
                    @endif
                </div>
                
                <div class="modal-footer border-top border-secondary-subtle rounded-0 px-4 py-3 bg-white">
                    <button type="button" class="btn btn-outline-dark rounded-0 fw-bold text-uppercase px-4 py-2" data-bs-dismiss="modal" style="font-size: 12px; letter-spacing: 1px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .product-card-stretcher > div, 
        .product-card-stretcher > a, 
        .product-card-stretcher > article {
            height: 100% !important;
            display: flex;
            flex-direction: column;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // ==========================================
            // 🌟 LOGIKA MATRIX FILTER SHOPEE (AUTO-SELECT)
            // ==========================================
            const skus = @json($product->skus);
            const hasColors = {{ $hasColors ? 'true' : 'false' }};
            const defaultBasePrice = {{ $defaultBasePrice }};
            const defaultDiscountPrice = {{ $defaultDiscountPrice ?? 'null' }};
            
            // 🌟 AUTO-SELECT VARIAN DARI PHP
            let selectedColor = {!! json_encode($defaultColor) !!};
            let selectedSize = {!! json_encode($defaultSize) !!};
            let currentMaxStock = 0;

            const colorBtns = document.querySelectorAll('.color-btn');
            const sizeBtns = document.querySelectorAll('.size-btn');
            const btnCart = document.getElementById('btn-add-cart');
            const btnBuy = document.getElementById('btn-buy-now');
            const skuInput = document.getElementById('selected-sku-id');

            function updatePriceDisplay(basePrice, discPrice) {
                const formatter = new Intl.NumberFormat('id-ID');
                if (discPrice && parseInt(discPrice) > 0) {
                    document.getElementById('discount-view').style.setProperty('display', 'flex', 'important');
                    document.getElementById('normal-view').style.display = 'none';
                    document.getElementById('current-price').innerText = 'Rp ' + formatter.format(discPrice);
                    document.getElementById('original-price').innerText = 'Rp ' + formatter.format(basePrice);
                    const percent = Math.round(((basePrice - discPrice) / basePrice) * 100);
                    document.getElementById('discount-badge').innerText = percent + '% OFF';
                } else {
                    document.getElementById('discount-view').style.setProperty('display', 'none', 'important');
                    document.getElementById('normal-view').style.display = 'block';
                    document.getElementById('normal-price').innerText = 'Rp ' + formatter.format(basePrice);
                }
            }

            function updateVariantMatrix() {
                // 1. FILTER TOMBOL WARNA BERDASARKAN UKURAN YANG DIPILIH
                colorBtns.forEach(btn => {
                    const c = btn.dataset.color;
                    let stockForThisColor = 0;
                    if (selectedSize) {
                        const sku = skus.find(s => s.color === c && s.size === selectedSize);
                        stockForThisColor = sku ? sku.available_stock : 0;
                    } else {
                        stockForThisColor = skus.filter(s => s.color === c).reduce((sum, s) => sum + s.available_stock, 0);
                    }

                    if (stockForThisColor <= 0) {
                        btn.classList.add('disabled', 'btn-outline-secondary', 'text-muted');
                        btn.classList.remove('btn-outline-dark');
                        btn.style.borderStyle = 'dashed';
                        btn.disabled = true;
                        if (selectedColor === c) selectedColor = null; 
                    } else {
                        btn.classList.remove('disabled', 'btn-outline-secondary', 'text-muted');
                        btn.classList.add('btn-outline-dark');
                        btn.style.borderStyle = 'solid';
                        btn.disabled = false;
                    }

                    if (selectedColor === c) {
                        btn.classList.add('active', 'bg-dark', 'text-white');
                    } else {
                        btn.classList.remove('active', 'bg-dark', 'text-white');
                    }
                });

                // 2. FILTER TOMBOL UKURAN BERDASARKAN WARNA YANG DIPILIH
                sizeBtns.forEach(btn => {
                    const sz = btn.dataset.size;
                    let stockForThisSize = 0;
                    if (selectedColor) {
                        const sku = skus.find(s => s.size === sz && s.color === selectedColor);
                        stockForThisSize = sku ? sku.available_stock : 0;
                    } else {
                        stockForThisSize = skus.filter(s => s.size === sz).reduce((sum, s) => sum + s.available_stock, 0);
                    }

                    if (stockForThisSize <= 0) {
                        btn.classList.add('disabled', 'btn-outline-secondary', 'text-muted');
                        btn.classList.remove('btn-outline-dark');
                        btn.style.borderStyle = 'dashed';
                        btn.disabled = true;
                        if (selectedSize === sz) selectedSize = null; 
                    } else {
                        btn.classList.remove('disabled', 'btn-outline-secondary', 'text-muted');
                        btn.classList.add('btn-outline-dark');
                        btn.style.borderStyle = 'solid';
                        btn.disabled = false;
                    }

                    if (selectedSize === sz) {
                        btn.classList.add('active', 'bg-dark', 'text-white');
                    } else {
                        btn.classList.remove('active', 'bg-dark', 'text-white');
                    }
                });

                // 3. CEK KOMBINASI FINAL
                let finalSku = null;
                if (hasColors) {
                    if (selectedColor && selectedSize) {
                        finalSku = skus.find(s => s.color === selectedColor && s.size === selectedSize);
                    }
                } else {
                    if (selectedSize) {
                        finalSku = skus.find(s => s.size === selectedSize);
                    }
                }

                // 4. UPDATE UI & HARGA
                if (finalSku) {
                    skuInput.value = finalSku.id;
                    updatePriceDisplay(finalSku.base_price, finalSku.discount_price);
                    
                    document.getElementById('stock-status-container').style.display = 'block';
                    document.getElementById('stock-display').innerText = finalSku.available_stock + ' Pcs';
                    currentMaxStock = finalSku.available_stock;

                    if(btnCart && btnBuy) {
                        btnCart.disabled = false;
                        btnCart.innerText = 'TAMBAH KE KERANJANG';
                        btnBuy.disabled = false;
                        btnBuy.innerText = 'BELI SEKARANG';
                    }
                } else {
                    skuInput.value = '';
                    document.getElementById('stock-status-container').style.display = 'none';
                    currentMaxStock = 0;

                    if(btnCart && btnBuy) {
                        btnCart.disabled = true;
                        btnCart.innerText = 'PILIH VARIAN';
                        btnBuy.disabled = true;
                        btnBuy.innerText = 'PILIH VARIAN';
                    }

                    updatePriceDisplay(defaultBasePrice, defaultDiscountPrice);
                }
                
                document.getElementById('qty-input').value = 1;
                document.getElementById('qty-warning').style.setProperty('display', 'none', 'important');
            }

            // --- EVENT LISTENER TOMBOL ---
            colorBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const c = this.dataset.color;
                    selectedColor = (selectedColor === c) ? null : c; 
                    updateVariantMatrix();
                });
            });

            sizeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const sz = this.dataset.size;
                    selectedSize = (selectedSize === sz) ? null : sz; 
                    updateVariantMatrix();
                });
            });

            // Jalankan validasi pertama kali (Otomatis memilih SKU dari PHP)
            updateVariantMatrix();

            // ==========================================
            // KONTROL KUANTITAS (+ / -)
            // ==========================================
            document.getElementById('btn-qty-plus').addEventListener('click', function() {
                if (!skuInput.value) return; 
                let currentQty = parseInt(document.getElementById('qty-input').value);
                if (currentQty < currentMaxStock) {
                    document.getElementById('qty-input').value = currentQty + 1;
                    document.getElementById('qty-warning').style.setProperty('display', 'none', 'important');
                } else {
                    document.getElementById('qty-warning').innerText = "Maksimal pembelian " + currentMaxStock + " pcs";
                    document.getElementById('qty-warning').style.setProperty('display', 'block', 'important');
                }
            });

            document.getElementById('btn-qty-minus').addEventListener('click', function() {
                if (!skuInput.value) return;
                let currentQty = parseInt(document.getElementById('qty-input').value);
                if (currentQty > 1) {
                    document.getElementById('qty-input').value = currentQty - 1;
                    document.getElementById('qty-warning').style.setProperty('display', 'none', 'important');
                }
            });

            // ==========================================
            // LOGIKA GALERI & INTERACTIVE ZOOM
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

            function updateMainImage(index) {
                resetZoom(); 
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

            thumbs.forEach((thumb, index) => {
                thumb.addEventListener('click', () => updateMainImage(index));
            });

            btnNext.addEventListener('click', () => {
                updateMainImage((currentIndex + 1) % images.length);
            });

            btnPrev.addEventListener('click', () => {
                updateMainImage((currentIndex - 1 + images.length) % images.length);
            });

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

            // ==========================================
            // 🌟 LOGIKA ADD TO CART (RELOAD CEPAT NORMAL)
            // ==========================================
            const cartForm = document.getElementById('add-to-cart-form');
            
            if (cartForm) {
                cartForm.addEventListener('submit', function(e) {
                    if (!skuInput.value) {
                        e.preventDefault(); // Hentikan reload jika belum memilih varian lengkap
                        
                        const sizeContainer = document.getElementById('size-button-container');
                        let warning = document.getElementById('dynamic-size-warning');
                        
                        if (!warning) {
                            warning = document.createElement('span');
                            warning.id = 'dynamic-size-warning';
                            warning.className = 'text-danger fw-bold d-block mt-1';
                            warning.style.fontSize = '11px';
                            warning.innerText = 'Silakan pilih varian warna & ukuran terlebih dahulu';
                            sizeContainer.insertAdjacentElement('afterend', warning);
                        }
                        
                        sizeContainer.style.animation = 'shake 0.5s';
                        setTimeout(() => sizeContainer.style.animation = '', 500);
                    }
                    // Jika SKU sudah terisi, biarkan form berjalan normal (reload otomatis ke cart.add)
                });
            }

        });
    </script>
    @endpush
@endsection
@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom">
        <!-- TOPBAR -->
        <x-customer.topbar />
        <!-- TOPBAR -->
        
        <!-- NAVBAR -->
        <x-customer.navbar />
        <!-- NAVBAR -->
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh;">
        <div class="container-fluid px-4 px-lg-5" style="max-width: 1400px;">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 border-bottom pb-4 gap-3">
                
                <div>
                    @php
                        // 1. Ambil data filter aktif
                        $activeGen = request('gender') ?: (is_array(request('gen')) && count(request('gen')) == 1 ? request('gen')[0] : null);
                        $activeCat = request('category') ?: (is_array(request('cat')) && count(request('cat')) == 1 ? request('cat')[0] : null);
                        $activeSub = request('subcategory');
                        $activeType = request('type');

                        $currentUrl = route('product.index');
                        $breadcrumbs = [];

                        // 2. Bangun Breadcrumb beserta URL-nya
                        if (in_array($activeType, ['sale', 'new', 'featured'])) {
                            $label = $activeType == 'sale' ? 'SALE' : ($activeType == 'new' ? 'PRODUK BARU' : 'PRODUK EKSKLUSIF');
                            $breadcrumbs[] = ['label' => $label, 'url' => $currentUrl . '?type=' . $activeType];
                        } else {
                            // Link untuk Level 1: Gender
                            if ($activeGen) {
                                $breadcrumbs[] = [
                                    'label' => 'KATEGORI ' . strtoupper($activeGen), 
                                    'url' => $currentUrl . '?gender=' . urlencode($activeGen)
                                ];
                            }
                            // Link untuk Level 2: Kategori
                            if ($activeCat) {
                                $catUrl = $currentUrl . '?';
                                if ($activeGen) $catUrl .= 'gender=' . urlencode($activeGen) . '&';
                                $catUrl .= 'category=' . urlencode($activeCat);
                                
                                $breadcrumbs[] = ['label' => strtoupper($activeCat), 'url' => $catUrl];
                            }
                            // Link untuk Level 3: Subkategori
                            if ($activeSub) {
                                $subUrl = $currentUrl . '?';
                                if ($activeGen) $subUrl .= 'gender=' . urlencode($activeGen) . '&';
                                if ($activeCat) $subUrl .= 'category=' . urlencode($activeCat) . '&';
                                $subUrl .= 'subcategory=' . urlencode($activeSub);
                                
                                $breadcrumbs[] = ['label' => strtoupper($activeSub), 'url' => $subUrl];
                            }
                        }

                        // Jika kosong (Berada di halaman Semua Produk)
                        if (empty($breadcrumbs)) {
                            $breadcrumbs[] = ['label' => 'SEMUA PRODUK', 'url' => $currentUrl];
                        }

                        // 3. Judul H2 HANYA MENGAMBIL ITEM TERAKHIR
                        $lastItem = end($breadcrumbs);
                        $h2Title = $lastItem['label'];
                    @endphp

                    <div class="fw-bold text-uppercase mb-2" style="font-size: 13px; letter-spacing: 1px;">
                        <a href="{{ route('home') }}" class="text-dark text-decoration-none">BERANDA</a> 
                        
                        @foreach($breadcrumbs as $crumb)
                            <span class="text-secondary mx-1">/</span> 
                            
                            @if($loop->last)
                                <span class="text-secondary">{{ $crumb['label'] }}</span>
                            @else
                                <a href="{{ $crumb['url'] }}" class="text-dark text-decoration-none hover-link">{{ $crumb['label'] }}</a>
                            @endif
                        @endforeach
                    </div>
                    
                    <h2 class="fw-black text-uppercase mb-0" style="letter-spacing: 1px; font-weight: 900;">
                        {{ $h2Title }}
                    </h2>
                </div>
                
                <div class="d-flex align-items-center gap-4">
                    <div class="text-secondary d-none d-md-block" style="font-size: 13px; font-weight: 600;">
                        MENAMPILKAN {{ $products->total() }} PRODUK
                    </div>
                    
                    <button class="btn btn-outline-dark rounded-0 px-4 py-2 fw-bold d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterPopUp" aria-controls="filterPopUp" style="font-size: 13px; border-width: 1.5px;">
                        Filter & Urutkan <i class="bi bi-sliders ms-1"></i>
                    </button>
                </div>

            </div>

<div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 align-items-stretch">
                
                @forelse($products as $product)
                    <div class="col">
                        <x-customer.product_card :product="$product" />
                    </div>
                @empty
                    <div class="d-flex flex-column justify-content-center align-items-center text-center py-5" style="min-height: 40vh; flex: 0 0 100%; max-width: 100%;">
                        <i class="bi bi-search text-secondary mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                        <h5 class="text-secondary fw-bold" style="letter-spacing: 0.5px;">Maaf tidak ada produk yang sesuai dengan filter Anda</h5>
                        <p class="text-muted" style="font-size: 14px;">Coba ubah kata kunci atau kurangi filter untuk melihat lebih banyak hasil</p>
                    </div>
                @endforelse
                
            </div>
            
            <div class="d-flex justify-content-center mt-5 pt-3 custom-pagination">
                {{ $products->links('components.customer.pagination') }}
            </div>
            
        </div>
    </section>

    <div class="offcanvas offcanvas-end shadow" tabindex="-1" id="filterPopUp" aria-labelledby="filterPopUpLabel" style="width: 400px; border-left: 1px solid #e0e0e0;">
        
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 class="offcanvas-title fw-bold text-uppercase m-0" id="filterPopUpLabel" style="letter-spacing: 1px; font-size: 16px;">Filter & Urutkan</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        
        <div class="offcanvas-body px-4 py-4 filter-scroll-container">
            <form action="{{ route('product.index') }}" method="GET">
                
                @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                @if(request('subcategory')) <input type="hidden" name="subcategory" value="{{ request('subcategory') }}"> @endif

                <div class="mb-4">
                    <label class="fw-bold mb-2 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Urutkan</label>
                    <select name="sort" class="form-select rounded-0 shadow-none border-dark text-dark cursor-pointer" style="font-size: 13px; height: 45px;">
                        <option value="rekomendasi" {{ request('sort') == 'rekomendasi' ? 'selected' : '' }}>Rekomendasi</option>
                        <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Paling Baru</option>
                        <option value="harga_tertinggi" {{ request('sort') == 'harga_tertinggi' ? 'selected' : '' }}>Harga Tinggi ke Rendah</option>
                        <option value="harga_terendah" {{ request('sort') == 'harga_terendah' ? 'selected' : '' }}>Harga Rendah ke Tinggi</option>
                        <option value="diskon" {{ request('sort') == 'diskon' ? 'selected' : '' }}>Diskon Terbesar</option>
                    </select>
                </div>

                <hr class="border-secondary-subtle my-4">

                <div class="mb-4">
                    <label class="fw-bold mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Gender</label>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="gen[]" value="Laki-laki" id="genPria" {{ in_array('Laki-laki', (array)request('gen', [])) || request('gender') == 'Laki-laki' ? 'checked' : '' }}>
                        <label class="form-check-label" for="genPria" style="font-size: 14px;">Laki-laki</label>
                    </div>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="gen[]" value="Perempuan" id="genWanita" {{ in_array('Perempuan', (array)request('gen', [])) || request('gender') == 'Perempuan' ? 'checked' : '' }}>
                        <label class="form-check-label" for="genWanita" style="font-size: 14px;">Perempuan</label>
                    </div>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="gen[]" value="Anak-anak" id="genAnak" {{ in_array('Anak-anak', (array)request('gen', [])) || request('gender') == 'Anak-anak' ? 'checked' : '' }}>
                        <label class="form-check-label" for="genAnak" style="font-size: 14px;">Anak-anak</label>
                    </div>
                    <div class="form-check custom-checkbox">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="gen[]" value="Unisex" id="genUnisex" {{ in_array('Unisex', (array)request('gen', [])) || request('gender') == 'Unisex' ? 'checked' : '' }}>
                        <label class="form-check-label" for="genUnisex" style="font-size: 14px;">Unisex</label>
                    </div>
                </div>

                <hr class="border-secondary-subtle my-4">

                <div class="mb-4">
                    <label class="fw-bold mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Kategori</label>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="cat[]" value="Sepatu" id="catSepatu" {{ in_array('Sepatu', (array)request('cat', [])) || request('category') == 'Sepatu' ? 'checked' : '' }}>
                        <label class="form-check-label" for="catSepatu" style="font-size: 14px;">Sepatu</label>
                    </div>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="cat[]" value="Pakaian" id="catPakaian" {{ in_array('Pakaian', (array)request('cat', [])) || request('category') == 'Pakaian' ? 'checked' : '' }}>
                        <label class="form-check-label" for="catPakaian" style="font-size: 14px;">Pakaian</label>
                    </div>
                    <div class="form-check custom-checkbox">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="cat[]" value="Aksesoris" id="catAksesoris" {{ in_array('Aksesoris', (array)request('cat', [])) || request('category') == 'Aksesoris' ? 'checked' : '' }}>
                        <label class="form-check-label" for="catAksesoris" style="font-size: 14px;">Aksesoris</label>
                    </div>
                </div>

                <hr class="border-secondary-subtle my-4">

                <div class="mb-4">
                    <label class="fw-bold mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Merek</label>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="brand[]" value="Adidas" id="brandAdidas" {{ in_array('Adidas', (array)request('brand', [])) ? 'checked' : '' }}>
                        <label class="form-check-label text-uppercase" for="brandAdidas" style="font-size: 13px;">Adidas</label>
                    </div>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="brand[]" value="Nike" id="brandNike" {{ in_array('Nike', (array)request('brand', [])) ? 'checked' : '' }}>
                        <label class="form-check-label text-uppercase" for="brandNike" style="font-size: 13px;">Nike</label>
                    </div>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="brand[]" value="Puma" id="brandPuma" {{ in_array('Puma', (array)request('brand', [])) ? 'checked' : '' }}>
                        <label class="form-check-label text-uppercase" for="brandPuma" style="font-size: 13px;">Puma</label>
                    </div>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="brand[]" value="Ortuseight" id="brandOrtus" {{ in_array('Ortuseight', (array)request('brand', [])) ? 'checked' : '' }}>
                        <label class="form-check-label text-uppercase" for="brandOrtus" style="font-size: 13px;">Ortuseight</label>
                    </div>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="brand[]" value="Specs" id="brandSpecs" {{ in_array('Specs', (array)request('brand', [])) ? 'checked' : '' }}>
                        <label class="form-check-label text-uppercase" for="brandSpecs" style="font-size: 13px;">Specs</label>
                    </div>
                </div>

                <hr class="border-secondary-subtle my-4">

                <div class="mb-4">
                    <label class="fw-bold mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Harga (Rp)</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control rounded-0 shadow-none border-dark text-center" placeholder="Min" step="any" style="font-size: 13px;">
                        <span class="fw-bold text-secondary">-</span>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control rounded-0 shadow-none border-dark text-center" placeholder="Max" step="any" style="font-size: 13px;">
                    </div>
                </div>

                <hr class="border-secondary-subtle my-4">

                <div class="mb-5">
                    <label class="fw-bold mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Ukuran</label>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2" style="font-size: 11px;">PAKAIAN</small>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $sz)
                                <input type="checkbox" class="btn-check" id="size{{ $sz }}" name="size[]" value="{{ $sz }}" {{ in_array($sz, (array)request('size', [])) ? 'checked' : '' }}>
                                <label class="btn btn-outline-dark rounded-0 fw-bold px-3 py-1 size-btn" for="size{{ $sz }}" style="font-size: 12px;">{{ $sz }}</label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <small class="text-muted d-block mb-2" style="font-size: 11px;">SEPATU</small>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['38', '39', '40', '41', '42', '43', '44'] as $sz)
                                <input type="checkbox" class="btn-check" id="size{{ $sz }}" name="size[]" value="{{ $sz }}" {{ in_array($sz, (array)request('size', [])) ? 'checked' : '' }}>
                                <label class="btn btn-outline-dark rounded-0 fw-bold px-3 py-1 size-btn" for="size{{ $sz }}" style="font-size: 12px;">{{ $sz }}</label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 sticky-bottom bg-white pt-2 pb-1" style="margin-top: 50px;">
                    <a href="{{ request()->url() }}?{{ http_build_query(request()->only(['type', 'subcategory'])) }}" class="btn btn-outline-dark rounded-0 w-50 py-3 fw-bold text-uppercase d-flex justify-content-center align-items-center text-decoration-none" style="letter-spacing: 1px; font-size: 13px;">Reset</a>
                    <button type="submit" class="btn btn-black rounded-0 w-50 py-3 fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 13px;">Terapkan</button>
                </div>

            </form>
        </div>
    </div>

    <!-- FOOTER -->
    <x-customer.footer />
    <!-- FOOTER -->

    <!-- CHATBOT -->
    <x-customer.chatbot />
    <!-- CHATBOT -->
@endsection
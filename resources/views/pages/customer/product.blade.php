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
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 border-bottom pb-3 gap-3">
                
                <div class="fw-bold text-uppercase" style="font-size: 15px; letter-spacing: 1px;">
                    {{ $gender }} / {{ $category }} <span class="text-secondary">/ {{ $subcategory }}</span>
                </div>
                
                <div class="d-flex align-items-center gap-4">
                    <div class="text-secondary d-md-block" style="font-size: 13px; font-weight: 600;">
                        MENAMPILKAN {{ count($products) }} PRODUK
                    </div>
                    
                    <button class="btn btn-outline-dark rounded-0 px-4 py-2 fw-bold d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterPopUp" aria-controls="filterPopUp" style="font-size: 13px; border-width: 1.5px;">
                        Filter & Urutkan <i class="bi bi-sliders ms-1"></i>
                    </button>
                </div>

            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 align-items-stretch">
                
                @foreach($products as $product)
                    <div class="col">
                        <x-customer.product_card :product="$product" />
                    </div>
                @endforeach
                
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-5 pt-3">
                <a href="#" class="btn btn-black rounded-0 px-4 py-2 fw-bold text-capitalize" style="letter-spacing: 0.5px; font-size: 14px;">
                    Sebelumnya
                </a>
                <span class="fw-bold text-dark" style="font-size: 15px;">
                    Halaman 1 dari 10
                </span>
                <a href="#" class="btn btn-black rounded-0 px-4 py-2 fw-bold text-capitalize" style="letter-spacing: 0.5px; font-size: 14px;">
                    Berikutnya
                </a>
            </div>
            
        </div>
    </section>

    <div class="offcanvas offcanvas-end shadow" tabindex="-1" id="filterPopUp" aria-labelledby="filterPopUpLabel" style="width: 400px; border-left: 1px solid #e0e0e0;">
        
        <div class="offcanvas-header border-bottom py-3 px-4">
            <h5 class="offcanvas-title fw-bold text-uppercase m-0" id="filterPopUpLabel" style="letter-spacing: 1px; font-size: 16px;">Filter & Urutkan</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        
        <div class="offcanvas-body px-4 py-4 filter-scroll-container">
            <form action="{{ route('products.index') }}" method="GET">
                
                <div class="mb-4">
                    <label class="fw-bold mb-2 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Urutkan</label>
                    <select name="sort" class="form-select rounded-0 shadow-none border-dark text-dark cursor-pointer" style="font-size: 13px; height: 45px;">
                        <option value="rekomendasi">Rekomendasi</option>
                        <option value="terbaru">Paling Baru</option>
                        <option value="harga_tertinggi">Harga Tinggi ke Rendah</option>
                        <option value="harga_terendah">Harga Rendah ke Tinggi</option>
                        <option value="diskon">Diskon Terbesar</option>
                    </select>
                </div>

                <hr class="border-secondary-subtle my-4">

                <div class="mb-4">
                    <label class="fw-bold mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Gender</label>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="gen[]" value="pria" id="genPria">
                        <label class="form-check-label" for="genPria" style="font-size: 14px;">Pria</label>
                    </div>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="gen[]" value="wanita" id="genWanita">
                        <label class="form-check-label" for="genWanita" style="font-size: 14px;">Wanita</label>
                    </div>
                    <div class="form-check custom-checkbox">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="gen[]" value="unisex" id="genUnisex" checked>
                        <label class="form-check-label" for="genUnisex" style="font-size: 14px;">Unisex</label>
                    </div>
                </div>

                <hr class="border-secondary-subtle my-4">

                <div class="mb-4">
                    <label class="fw-bold mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Kategori</label>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="cat[]" value="sepatu" id="catSepatu" checked>
                        <label class="form-check-label" for="catSepatu" style="font-size: 14px;">Sepatu</label>
                    </div>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="cat[]" value="pakaian" id="catPakaian">
                        <label class="form-check-label" for="catPakaian" style="font-size: 14px;">Pakaian</label>
                    </div>
                    <div class="form-check custom-checkbox">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="cat[]" value="aksesoris" id="catAksesoris">
                        <label class="form-check-label" for="catAksesoris" style="font-size: 14px;">Aksesoris</label>
                    </div>
                </div>

                <hr class="border-secondary-subtle my-4">

                <div class="mb-4">
                    <label class="fw-bold mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Merek</label>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="brand[]" value="adidas" id="brandAdidas" checked>
                        <label class="form-check-label text-uppercase" for="brandAdidas" style="font-size: 13px;">Adidas</label>
                    </div>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="brand[]" value="nike" id="brandNike">
                        <label class="form-check-label text-uppercase" for="brandNike" style="font-size: 13px;">Nike</label>
                    </div>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="brand[]" value="puma" id="brandPuma">
                        <label class="form-check-label text-uppercase" for="brandPuma" style="font-size: 13px;">Puma</label>
                    </div>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="brand[]" value="ortuseight" id="brandOrtus">
                        <label class="form-check-label text-uppercase" for="brandOrtus" style="font-size: 13px;">Ortuseight</label>
                    </div>
                    <div class="form-check custom-checkbox mb-2">
                        <input class="form-check-input rounded-0 shadow-none border-dark" type="checkbox" name="brand[]" value="specs" id="brandSpecs">
                        <label class="form-check-label text-uppercase" for="brandSpecs" style="font-size: 13px;">Specs</label>
                    </div>
                </div>

                <hr class="border-secondary-subtle my-4">

                <div class="mb-4">
                    <label class="fw-bold mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Harga (Rp)</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="number" name="min_price" class="form-control rounded-0 shadow-none border-dark text-center" placeholder="Min" style="font-size: 13px;">
                        <span class="fw-bold text-secondary">-</span>
                        <input type="number" name="max_price" class="form-control rounded-0 shadow-none border-dark text-center" placeholder="Max" style="font-size: 13px;">
                    </div>
                </div>

                <hr class="border-secondary-subtle my-4">

                <div class="mb-5">
                    <label class="fw-bold mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Ukuran</label>
                    <div class="d-flex flex-wrap gap-2">
                        <input type="checkbox" class="btn-check" id="size39" name="size[]" value="39">
                        <label class="btn btn-outline-dark rounded-0 fw-bold px-3 py-1 size-btn" for="size39" style="font-size: 12px;">39</label>

                        <input type="checkbox" class="btn-check" id="size40" name="size[]" value="40" checked>
                        <label class="btn btn-outline-dark rounded-0 fw-bold px-3 py-1 size-btn" for="size40" style="font-size: 12px;">40</label>

                        <input type="checkbox" class="btn-check" id="size41" name="size[]" value="41">
                        <label class="btn btn-outline-dark rounded-0 fw-bold px-3 py-1 size-btn" for="size41" style="font-size: 12px;">41</label>

                        <input type="checkbox" class="btn-check" id="size42" name="size[]" value="42">
                        <label class="btn btn-outline-dark rounded-0 fw-bold px-3 py-1 size-btn" for="size42" style="font-size: 12px;">42</label>

                        <input type="checkbox" class="btn-check" id="size43" name="size[]" value="43">
                        <label class="btn btn-outline-dark rounded-0 fw-bold px-3 py-1 size-btn" for="size43" style="font-size: 12px;">43</label>
                    </div>
                </div>

                <div class="d-flex gap-2 sticky-bottom bg-white pt-2 pb-1" style="margin-top: 50px;">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-dark rounded-0 w-50 py-3 fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 13px;">Reset</a>
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
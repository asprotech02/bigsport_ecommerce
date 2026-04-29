<nav class="navbar navbar-expand-lg bg-black-custom position-relative z-3 p-0">
    <div class="container flex-column">
        
        <div class="row w-100 align-items-center pt-3 pb-2 m-0">
            <div class="col-3 col-lg-3 ps-0 text-start">
                <a href="/" class="text-white text-decoration-none fs-4 fw-bold">LOGO</a>
            </div>
            
            <div class="col-6 col-lg-6 d-none d-lg-block">
                <div class="input-group">
                    <input type="text" class="form-control rounded-0 border-0 py-2 shadow-none" placeholder="Cari produk...">
                    <button class="btn bg-white rounded-0 border-0 px-3 py-2" type="button" style="border-left: 1px solid #ddd !important;">
                        <i class="bi bi-search fs-5 text-dark"></i>
                    </button>
                </div>
            </div>
            
            <div class="col-9 col-lg-3 pe-0 d-flex justify-content-end align-items-center gap-3 gap-lg-4">
                <a href="{{ route('notification') }}" class="text-white text-decoration-none d-lg-inline-block position-relative">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">2</span>
                </a>
                <a href="{{ route('profile') }}" class="text-white text-decoration-none d-lg-inline-block"><i class="bi bi-person fs-5"></i></a>
                <a href="{{ route('wishlist') }}" class="text-white text-decoration-none"><i class="bi bi-heart fs-5"></i></a>
                <a href="{{ route('cart') }}" class="text-white text-decoration-none"><i class="bi bi-cart2 fs-5"></i></a>
                <button class="navbar-toggler border-0 shadow-none p-0 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#unifiedMenu">
                    <i class="bi bi-list text-white fs-2"></i>
                </button>
            </div>
        </div>

        <div class="collapse navbar-collapse w-100 pb-3" id="unifiedMenu">
            
            <div class="input-group mb-4 mt-2 d-lg-none">
                <input type="text" class="form-control rounded-0 border-0 py-2 shadow-none" placeholder="Cari produk...">
                <button class="btn bg-white rounded-0 border-0 px-3 py-2" type="button">
                    <i class="bi bi-search fs-5 text-dark"></i>
                </button>
            </div>

            <ul class="navbar-nav mx-auto d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center m-0 p-3 p-lg-0 mt-2" style="max-width: 850px; width: 100%;">
                <li class="nav-item"><a href="{{ route('product.index', ['type' => 'sale']) }}" class="text-decoration-none nav-link-custom text-sale">SALE</a></li>
                <li class="nav-item"><a href="{{ route('product.index', ['type' => 'featured']) }}" class="text-decoration-none nav-link-custom">EKSKLUSIF</a></li>
                <li class="nav-item"><a href="{{ route('product.index', ['type' => 'new']) }}" class="text-decoration-none nav-link-custom">PRODUK BARU</a></li>
                <li class="nav-item"><a href="{{ route('product.index') }}" class="text-decoration-none nav-link-custom">SEMUA PRODUK</a></li>
                
                <li class="nav-item dropdown dropdown-mega position-lg-static">
                    <a href="#" class="text-decoration-none nav-link-custom" role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        LAKI-LAKI <i class="bi bi-chevron-down d-lg-none ms-1 text-secondary"></i>
                    </a>
                    <div class="dropdown-menu mega-menu border-0 p-0 m-0">
                        <div class="container-fluid px-3 px-lg-0 pb-4 pt-2">
                            <div class="row mx-auto m-0" style="max-width: 850px; width: 100%;">
                                
                                <div class="col-lg-4 px-0 text-start mb-3 mb-lg-0"> 
                                    <h6 class="mega-menu-title mt-2 mt-lg-0 mb-3 fs-6">SEPATU</h6>
                                    <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Sepatu']) }}" class="mega-menu-link m-0 mb-2">Semua Sepatu</a>
                                    <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Sepatu', 'subcategory' => 'Sepak Bola']) }}" class="mega-menu-link m-0 mb-2">Sepak Bola</a>
                                    <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Sepatu', 'subcategory' => 'Basket']) }}" class="mega-menu-link m-0 mb-2">Basket</a>
                                    <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Sepatu', 'subcategory' => 'Volly']) }}" class="mega-menu-link m-0 mb-2">Volly</a>
                                    <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Sepatu', 'subcategory' => 'Casual']) }}" class="mega-menu-link m-0 mb-2">Casual</a>
                                    <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Sepatu', 'subcategory' => 'Sepatu Lari']) }}" class="mega-menu-link m-0 mb-2">Sepatu Lari</a>
                                </div>
                                
                                <div class="col-lg-4 px-0 d-flex justify-content-lg-center text-start mb-3 mb-lg-0">
                                    <div>
                                        <h6 class="mega-menu-title mt-2 mt-lg-0 mb-3 fs-6">PAKAIAN</h6>
                                        <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Pakaian']) }}" class="mega-menu-link m-0 mb-2">Semua Pakaian</a>
                                        <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Pakaian', 'subcategory' => 'Kaos']) }}" class="mega-menu-link m-0 mb-2">Kaos</a>
                                        <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Pakaian', 'subcategory' => 'Jersey']) }}" class="mega-menu-link m-0 mb-2">Jersey</a>
                                        <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Pakaian', 'subcategory' => 'Hoodie']) }}" class="mega-menu-link m-0 mb-2">Hoodie</a>
                                        <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Pakaian', 'subcategory' => 'Celana']) }}" class="mega-menu-link m-0 mb-2">Celana</a>
                                    </div>
                                </div>
                                
                                <div class="col-lg-4 px-0 d-flex justify-content-lg-end text-start mb-3 mb-lg-0">
                                    <div>
                                        <h6 class="mega-menu-title mt-2 mt-lg-0 mb-3 fs-6">AKSESORIS</h6>
                                        <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Aksesoris']) }}" class="mega-menu-link m-0 mb-2">Semua Aksesoris</a>
                                        <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Aksesoris', 'subcategory' => 'Tas']) }}" class="mega-menu-link m-0 mb-2">Tas</a>
                                        <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Aksesoris', 'subcategory' => 'Topi']) }}" class="mega-menu-link m-0 mb-2">Topi</a>
                                        <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Aksesoris', 'subcategory' => 'Kaos Kaki']) }}" class="mega-menu-link m-0 mb-2">Kaos Kaki</a>
                                        <a href="{{ route('product.index', ['gender' => 'Laki-laki', 'category' => 'Aksesoris', 'subcategory' => 'Alat Olahraga']) }}" class="mega-menu-link m-0 mb-2">Alat Olahraga</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="nav-item dropdown dropdown-mega position-lg-static">
                    <a href="#" class="text-decoration-none nav-link-custom" role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        PEREMPUAN <i class="bi bi-chevron-down d-lg-none ms-1 text-secondary"></i>
                    </a>
                    <div class="dropdown-menu mega-menu border-0 p-0 m-0">
                        <div class="container-fluid px-3 px-lg-0 pb-4 pt-2">
                            <div class="row mx-auto m-0" style="max-width: 850px; width: 100%;">
                                <div class="col-lg-4 px-0 text-start mb-3 mb-lg-0">
                                    <h6 class="mega-menu-title mt-2 mt-lg-0 mb-3 fs-6">SEPATU</h6>
                                    <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Sepatu']) }}" class="mega-menu-link m-0 mb-2">Semua Sepatu</a>
                                    <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Sepatu', 'subcategory' => 'Sepak Bola']) }}" class="mega-menu-link m-0 mb-2">Sepak Bola</a>
                                    <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Sepatu', 'subcategory' => 'Basket']) }}" class="mega-menu-link m-0 mb-2">Basket</a>
                                    <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Sepatu', 'subcategory' => 'Volly']) }}" class="mega-menu-link m-0 mb-2">Volly</a>
                                    <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Sepatu', 'subcategory' => 'Casual']) }}" class="mega-menu-link m-0 mb-2">Casual</a>
                                    <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Sepatu', 'subcategory' => 'Sepatu Lari']) }}" class="mega-menu-link m-0 mb-2">Sepatu Lari</a>
                                </div>
                                <div class="col-lg-4 px-0 d-flex justify-content-lg-center text-start mb-3 mb-lg-0">
                                    <div>
                                        <h6 class="mega-menu-title mt-2 mt-lg-0 mb-3 fs-6">PAKAIAN</h6>
                                        <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Pakaian']) }}" class="mega-menu-link m-0 mb-2">Semua Pakaian</a>
                                        <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Pakaian', 'subcategory' => 'Kaos']) }}" class="mega-menu-link m-0 mb-2">Kaos</a>
                                        <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Pakaian', 'subcategory' => 'Jersey']) }}" class="mega-menu-link m-0 mb-2">Jersey</a>
                                        <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Pakaian', 'subcategory' => 'Hoodie']) }}" class="mega-menu-link m-0 mb-2">Hoodie</a>
                                        <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Pakaian', 'subcategory' => 'Celana']) }}" class="mega-menu-link m-0 mb-2">Celana</a>
                                    </div>
                                </div>
                                <div class="col-lg-4 px-0 d-flex justify-content-lg-end text-start mb-3 mb-lg-0">
                                    <div>
                                        <h6 class="mega-menu-title mt-2 mt-lg-0 mb-3 fs-6">AKSESORIS</h6>
                                        <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Aksesoris']) }}" class="mega-menu-link m-0 mb-2">Semua Aksesoris</a>
                                        <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Aksesoris', 'subcategory' => 'Tas']) }}" class="mega-menu-link m-0 mb-2">Tas</a>
                                        <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Aksesoris', 'subcategory' => 'Topi']) }}" class="mega-menu-link m-0 mb-2">Topi</a>
                                        <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Aksesoris', 'subcategory' => 'Kaos Kaki']) }}" class="mega-menu-link m-0 mb-2">Kaos Kaki</a>
                                        <a href="{{ route('product.index', ['gender' => 'Perempuan', 'category' => 'Aksesoris', 'subcategory' => 'Alat Olahraga']) }}" class="mega-menu-link m-0 mb-2">Alat Olahraga</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="nav-item dropdown dropdown-mega position-lg-static">
                    <a href="#" class="text-decoration-none nav-link-custom" role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        ANAK-ANAK <i class="bi bi-chevron-down d-lg-none ms-1 text-secondary"></i>
                    </a>
                    <div class="dropdown-menu mega-menu border-0 p-0 m-0">
                        <div class="container-fluid px-3 px-lg-0 pb-4 pt-2">
                            <div class="row mx-auto m-0" style="max-width: 850px; width: 100%;">
                                <div class="col-lg-4 px-0 text-start mb-3 mb-lg-0">
                                    <h6 class="mega-menu-title mt-2 mt-lg-0 mb-3 fs-6">SEPATU</h6>
                                    <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Sepatu']) }}" class="mega-menu-link m-0 mb-2">Semua Sepatu</a>
                                    <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Sepatu', 'subcategory' => 'Sepak Bola']) }}" class="mega-menu-link m-0 mb-2">Sepak Bola</a>
                                    <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Sepatu', 'subcategory' => 'Basket']) }}" class="mega-menu-link m-0 mb-2">Basket</a>
                                    <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Sepatu', 'subcategory' => 'Volly']) }}" class="mega-menu-link m-0 mb-2">Volly</a>
                                    <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Sepatu', 'subcategory' => 'Casual']) }}" class="mega-menu-link m-0 mb-2">Casual</a>
                                    <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Sepatu', 'subcategory' => 'Sepatu Lari']) }}" class="mega-menu-link m-0 mb-2">Sepatu Lari</a>
                                </div>
                                <div class="col-lg-4 px-0 d-flex justify-content-lg-center text-start mb-3 mb-lg-0">
                                    <div>
                                        <h6 class="mega-menu-title mt-2 mt-lg-0 mb-3 fs-6">PAKAIAN</h6>
                                        <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Pakaian']) }}" class="mega-menu-link m-0 mb-2">Semua Pakaian</a>
                                        <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Pakaian', 'subcategory' => 'Kaos']) }}" class="mega-menu-link m-0 mb-2">Kaos</a>
                                        <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Pakaian', 'subcategory' => 'Jersey']) }}" class="mega-menu-link m-0 mb-2">Jersey</a>
                                        <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Pakaian', 'subcategory' => 'Hoodie']) }}" class="mega-menu-link m-0 mb-2">Hoodie</a>
                                        <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Pakaian', 'subcategory' => 'Celana']) }}" class="mega-menu-link m-0 mb-2">Celana</a>
                                    </div>
                                </div>
                                <div class="col-lg-4 px-0 d-flex justify-content-lg-end text-start mb-3 mb-lg-0">
                                    <div>
                                        <h6 class="mega-menu-title mt-2 mt-lg-0 mb-3 fs-6">AKSESORIS</h6>
                                        <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Aksesoris']) }}" class="mega-menu-link m-0 mb-2">Semua Aksesoris</a>
                                        <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Aksesoris', 'subcategory' => 'Tas']) }}" class="mega-menu-link m-0 mb-2">Tas</a>
                                        <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Aksesoris', 'subcategory' => 'Topi']) }}" class="mega-menu-link m-0 mb-2">Topi</a>
                                        <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Aksesoris', 'subcategory' => 'Kaos Kaki']) }}" class="mega-menu-link m-0 mb-2">Kaos Kaki</a>
                                        <a href="{{ route('product.index', ['gender' => 'Anak-anak', 'category' => 'Aksesoris', 'subcategory' => 'Alat Olahraga']) }}" class="mega-menu-link m-0 mb-2">Alat Olahraga</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="nav-item dropdown dropdown-mega position-lg-static">
                    <a href="#" class="text-decoration-none nav-link-custom" role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        BRAND <i class="bi bi-chevron-down d-lg-none ms-1 text-secondary"></i>
                    </a>

                    <div class="dropdown-menu mega-menu border-0 p-0 m-0 bg-black"> 
                        <div class="container-fluid px-3 px-lg-0 pb-4 pt-4">
                            <div class="row mx-auto m-0" style="max-width: 850px; width: 100%;">
                                <div class="col-lg-12 px-0 text-start">
                                    <h6 class="mega-menu-title mt-2 mt-lg-0 mb-3 fs-6">DAFTAR BRAND</h6>
                                                        
                                    <div class="mega-menu-brand-container">
                    
                                        <a href="{{ route('product.index', ['brand' => ['Adidas']]) }}" class="text-decoration-none">
                                            <img src="{{ asset('assets/customer/images/brand/brand adidas.svg') }}" 
                                                alt="Adidas" class="brand-nav-logo nav-logo-adidas">
                                        </a>

                                        <a href="{{ route('product.index', ['brand' => ['Nike']]) }}" class="text-decoration-none">
                                            <img src="{{ asset('assets/customer/images/brand/brand nike.svg') }}" 
                                                alt="Nike" class="brand-nav-logo nav-logo-nike">
                                        </a>

                                        <a href="{{ route('product.index', ['brand' => ['Puma']]) }}" class="text-decoration-none">
                                            <img src="{{ asset('assets/customer/images/brand/brand puma.svg') }}" 
                                                alt="Puma" class="brand-nav-logo nav-logo-puma">
                                        </a>

                                        <a href="{{ route('product.index', ['brand' => ['Ortuseight']]) }}" class="text-decoration-none">
                                            <img src="{{ asset('assets/customer/images/brand/brand ortuseight.svg') }}" 
                                                alt="Ortuseight" class="brand-nav-logo nav-logo-ortus">
                                        </a>

                                        <a href="{{ route('product.index', ['brand' => ['Specs']]) }}" class="text-decoration-none">
                                            <img src="{{ asset('assets/customer/images/brand/brand specs.svg') }}" 
                                                alt="Specs" class="brand-nav-logo nav-logo-specs">
                                        </a>

                                        <a href="{{ route('product.index', ['brand' => ['Mizuno']]) }}" class="text-decoration-none">
                                            <img src="{{ asset('assets/customer/images/brand/brand mizuno.svg') }}" 
                                                alt="Mizuno" class="brand-nav-logo nav-logo-mizuno">
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
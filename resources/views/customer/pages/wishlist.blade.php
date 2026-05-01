@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh;">
        <div class="container-fluid px-4 px-lg-5" style="max-width: 1400px;">
            
            <div class="mb-5 text-center">
                <h3 class="fw-bold text-uppercase m-0" style="letter-spacing: 1.5px;">Wishlist Saya</h3>
            </div>

            
            @forelse($wishlistItems as $item)
            <div class="w-100">
                <div class="d-none d-md-flex fw-bold border-bottom border-dark pb-3 mb-4 text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
                    <div class="col-6">Item</div>
                    <div class="col-3 text-center">Harga</div>
                    <div class="col-3 text-end">Aksi</div>
                </div>
                    @php
                        $product = $item->product;
                        $isDiscount = $product->discount_price && $product->discount_price < $product->base_price;
                        $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                        $imagePath = $primaryImage ? asset('storage/' . $primaryImage->image_path) : asset('assets/images/default.jpg');
                        
                        // Logika sederhana stok (bisa disesuaikan dengan skus_sum_stock)
                        $totalStock = $product->skus->sum('stock');
                    @endphp

                    <div class="row align-items-center border-bottom pb-4 mb-4 m-0">
                        
                        <div class="col-12 col-md-6 d-flex mb-4 mb-md-0 px-0">
                            <div class="cart-img-wrapper me-3 me-md-4">
                                <img src="{{ $imagePath }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover; display: block; background-color: #f8f9fa;">
                            </div>
                            <div>
                                <div class="fw-bold text-uppercase mb-1" style="font-size: 14px;">{{ $product->brand->name }}</div>
                                <div class="fw-bold text-dark mb-1" style="font-size: 15px;">{{ $product->name }}</div>
                                <div class="text-secondary mb-2" style="font-size: 12px;">{{ $product->gender }}</div>
                                
                                @if($totalStock > 5)
                                    <div class="fw-bold mb-3 text-success" style="font-size: 11px;"><i class="bi bi-check-circle-fill me-1"></i> Tersedia</div>
                                @elseif($totalStock > 0)
                                    <div class="fw-bold mb-3 text-warning" style="font-size: 11px;"><i class="bi bi-exclamation-circle-fill me-1"></i> Stok Terbatas</div>
                                @else
                                    <div class="fw-bold mb-3 text-danger" style="font-size: 11px;"><i class="bi bi-x-circle-fill me-1"></i> Stok Habis</div>
                                @endif

                                <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0 text-danger fw-bold text-decoration-none text-uppercase shadow-none" style="font-size: 11px; letter-spacing: 1px;">Hapus</button>
                                </form>
                            </div>
                        </div>

                        <div class="col-12 col-md-3 mb-3 mb-md-0 d-flex justify-content-between justify-content-md-center align-items-center px-0">
                            <span class="d-md-none fw-bold text-secondary text-uppercase" style="font-size: 12px;">Harga</span>
                            <div class="text-md-center">
                                @if($isDiscount)
                                    <span class="fw-bold text-danger d-block" style="font-size: 15px;">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                                    <span class="text-secondary text-decoration-line-through" style="font-size: 12px;">Rp {{ number_format($product->base_price, 0, ',', '.') }}</span>
                                @else
                                    <span class="fw-bold" style="font-size: 15px;">Rp {{ number_format($product->base_price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 col-md-3 d-flex justify-content-end align-items-center px-0">
                            <a href="{{ route('product.detail', $product->slug) }}" class="btn btn-black rounded-0 px-3 py-2 w-100 fw-bold text-uppercase d-flex justify-content-center align-items-center gap-2" style="font-size: 12px; letter-spacing: 1px;">
                                <i class="bi bi-eye fs-5"></i> Lihat Produk
                            </a>
                        </div>
                        
                    </div>

                @empty
                    <div class="text-center py-5 mb-4 w-100">
                        <i class="bi bi-heart text-secondary mb-3 d-block" style="font-size: 3rem; opacity: 0.5;"></i>
                        <h5 class="fw-bold text-uppercase mb-2">Wishlist Kosong</h5>
                        <p class="text-secondary mb-4" style="font-size: 14px;">Belum ada produk yang Anda sukai</p>
                        <a href="{{ route('product.index') }}" class="btn btn-black px-4 py-2 fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Cari Produk</a>
                    </div>
                @endforelse

            </div>
        </div>
    </section>

    @include('customer.components.footer')
    @include('customer.components.chatbot')
@endsection
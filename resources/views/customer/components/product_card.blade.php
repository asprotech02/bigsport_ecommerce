@props(['product'])

<div class="product-card h-100">
    <div class="product-image-wrapper position-relative">
        @php
            // 1. Cek Wishlist
            $isInWishlist = false;
            if(auth()->check()) {
                $isInWishlist = \App\Models\Wishlist::where('user_id', auth()->id())
                    ->where('product_id', $product->id)
                    ->exists();
            }

            // 2. Hitung Diskon
            $discountPercentage = 0;
            if ($product->discount_price && $product->base_price > 0) {
                $discountPercentage = round((($product->base_price - $product->discount_price) / $product->base_price) * 100);
            }

            // 3. Ambil Gambar (Sesuai format di halaman lain)
            $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
            $imagePath = $primaryImage ? asset('storage/' . $primaryImage->image_path) : asset('assets/images/default.jpg');

            // 4. FIX LOGIKA STOK CARD PRODUK: Hanya hitung ukuran yang valid
            $standardSizes = [];
            $isKids = str_contains(strtolower($product->gender ?? ''), 'anak');
            $catName = $product->category->name ?? '';

            if ($catName == 'Sepatu') {
                $standardSizes = $isKids ? ['19','20','21','22','23','24','25','26','27','28','29','30'] : ['31','32','33','34','35','36','37','38','39','40','41','42','43','44'];
            } elseif ($catName == 'Pakaian') {
                $standardSizes = $isKids ? ['S', 'M', 'L', 'XL'] : ['S', 'M', 'L', 'XL', 'XXL'];
            } else {
                $standardSizes = ['All Size'];
            }

            $totalValidStock = 0;
            if ($product->skus) {
                foreach($product->skus as $sku) {
                    if(in_array($sku->size, $standardSizes) && $sku->stock > 0) {
                        $totalValidStock += $sku->stock;
                    }
                }
            }
        @endphp

        <button class="btn-wishlist toggle-wishlist" data-product-id="{{ $product->id }}">
            <i class="bi {{ $isInWishlist ? 'bi-heart-fill text-danger' : 'bi-heart' }} fs-5"></i>
        </button>

        <img src="{{ $imagePath }}" 
             alt="{{ $product->name }}" 
             class="product-img">

        {{-- 🌟 FIX: Tampilkan badge jika totalValidStock habis --}}
        @if($totalValidStock <= 0)
            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background-color: rgba(255,255,255,0.6); z-index: 5;">
                <span class="badge bg-dark py-2 px-3 rounded-0 shadow-sm" style="font-size: 14px; letter-spacing: 1px;">STOK HABIS</span>
            </div>
        @else
            <div class="product-overlay">
                <a href="{{ route('product.detail', $product->slug) }}" 
                   class="btn btn-light btn-sm product-btn px-3 py-2 rounded-0">
                    Lihat Detail
                </a>
            </div>
        @endif
    </div>

    <div class="product-body">
        <span class="product-brand text-uppercase fw-bold text-secondary" style="font-size: 13px;">
            {{ $product->brand->name ?? '-' }}
        </span>

        <h6 class="product-title text-truncate fw-bold mb-3">
            {{ $product->name }}
        </h6>

        <div class="product-meta mb-2">
            <div class="text-secondary" style="font-size: 14px;">{{ $product->gender ?? '-' }}</div>
        </div>

        <div class="product-price d-flex align-items-center gap-2 flex-wrap mb-2">
            @if($product->discount_price)
                <span class="fw-bold text-danger" style="font-size: 18px;">
                    Rp. {{ number_format($product->discount_price, 0, ',', '.') }}
                </span>
                <span class="text-secondary text-decoration-line-through" style="font-size: 14px;">
                    Rp. {{ number_format($product->base_price, 0, ',', '.') }}
                </span>
                <span class="badge rounded-0" style="background-color: #ffebee; color: #ff5252; font-size: 12px; font-weight: bold;">
                    {{ $discountPercentage }}% OFF
                </span>
            @else
                <span class="fw-bold text-dark" style="font-size: 18px;">
                    Rp. {{ number_format($product->base_price, 0, ',', '.') }}
                </span>
            @endif
        </div>

        <div class="product-rating">
            <div class="stars">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= round($product->reviews_avg_rating ?? 0))
                        <i class="bi bi-star-fill text-dark"></i>
                    @else
                        <i class="bi bi-star-fill text-secondary opacity-25"></i>
                    @endif
                @endfor
            </div>
            <span>({{ $product->reviews_count ?? 0 }})</span>
        </div>
    </div>
</div>
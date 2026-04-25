@props(['product'])

<div class="product-card h-100">
    <div class="product-image-wrapper">
        <button class="btn-wishlist">
            <i class="bi bi-heart fs-5"></i>
        </button>

        @php
            $primaryImage = $product->images->where('is_primary', true)->first();
            $imagePath = $primaryImage ? asset('storage/' . $primaryImage->image_path) : 'https://placehold.co/600x600?text=No+Image';
            
            // Hitung Persentase Diskon
            $discountPercentage = 0;
            if ($product->discount_price && $product->base_price > 0) {
                $discountPercentage = round((($product->base_price - $product->discount_price) / $product->base_price) * 100);
            }
        @endphp

        <img src="{{ $imagePath }}" 
             alt="{{ $product->name }}" 
             class="product-img">

        <div class="product-overlay">
            <a href="{{ route('detail_product', $product->slug) }}" 
               class="btn btn-light btn-sm product-btn px-3 py-2 rounded-0">
                Lihat Detail
            </a>
        </div>
    </div>

    <div class="product-body">
        <span class="product-brand text-uppercase fw-bold text-secondary" style="font-size: 13px;">
            {{ $product->brand->name ?? '-' }}
        </span>

        <h6 class="product-title text-truncate fw-bold mb-3">
            {{ $product->name }}
        </h6>

        <div class="product-meta mb-2">
            <div class="text-secondary" style="font-size: 14px;">{{ $product->category->name ?? '-' }}</div>
            <!-- <div class="text-secondary" style="font-size: 14px;"><b>Warna :</b> Putih</div> -->
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
                    @if($i <= ($product->reviews_avg_rating ?? 0))
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
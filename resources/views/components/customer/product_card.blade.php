@props(['product'])

<div class="product-card">

    <div class="product-image-wrapper">

        <button class="btn-wishlist">
            <i class="bi bi-heart fs-5"></i>
        </button>

        <img src="{{ $product['image'] }}" 
             alt="{{ $product['name'] }}" 
             class="product-img">

        <div class="product-overlay">
            <a href="{{ route('detail_product') }}" class="btn btn-light btn-sm product-btn px-3 py-2 rounded-0">
                Lihat Detail
            </a>
        </div>

    </div>

    <div class="product-body">

        <span class="product-brand">
            {{ $product['brand'] }}
        </span>

        <h6 class="product-title">
            {{ $product['name'] }}
        </h6>

        <div class="product-meta">
            <div>{{ $product['gender_type'] }}</div>
            <div><b>Warna :</b> {{ $product['color'] }}</div>
        </div>

        <div class="product-price">
            Rp. {{ number_format($product['price'], 0, ',', '.') }}
        </div>

        <div class="product-rating">
            <div class="stars">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $product['rating'])
                        <i class="bi bi-star-fill text-dark"></i>
                    @else
                        <i class="bi bi-star-fill text-secondary opacity-25"></i>
                    @endif
                @endfor
            </div>
            <span>({{ $product['reviews'] }})</span>
        </div>

    </div>

</div>
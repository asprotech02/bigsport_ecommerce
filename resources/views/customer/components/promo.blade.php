@if(isset($promoBanners) && count($promoBanners) > 0)
    @php $banner = $promoBanners->first(); @endphp
    <section class="py-5 text-center deals-section position-relative" style="background-image: url('{{ asset('storage/' . $banner->image_path) }}'); background-size: cover; background-position: center;">
        <!-- Overlay to ensure text remains readable -->
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>
        
        <div class="container position-relative z-index-1">
            <h2 class="fw-bold mb-3 deals-title text-white">
                {!! nl2br(e($banner->title)) !!}
            </h2>

            <a href="{{ $banner->link_url ?? '#' }}" class="shop-now btn btn-light text-dark fw-bold px-4 py-2 text-uppercase deals-btn">
                {{ $banner->subtitle ?? 'BELANJA SEKARANG' }}
            </a>
        </div>
    </section>
@else
    <section class="py-5 text-center deals-section">
        <div class="container">
            <h2 class="fw-bold mb-3 deals-title">
                3.3 DEALS DISKON SAMPAI 50%
            </h2>

            <a href="#" class="shop-now btn btn-black deals-btn">
                BELANJA SEKARANG
            </a>
        </div>
    </section>
@endif
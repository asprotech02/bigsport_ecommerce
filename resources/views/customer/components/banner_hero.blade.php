<div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">

    @if(isset($sliderBanners) && count($sliderBanners) > 0)
        <!-- Dynamic Indicators -->
        <div class="carousel-indicators">
            @foreach($sliderBanners as $index => $banner)
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}"></button>
            @endforeach
        </div>

        <!-- Dynamic Items -->
        <div class="carousel-inner">
            @foreach($sliderBanners as $index => $banner)
                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                    <div class="hero-overlay overlay-left"></div>
                    
                    @if($banner->link_url)
                        <a href="{{ $banner->link_url }}">
                    @endif
                    
                    <img src="{{ asset('storage/' . $banner->image_path) }}" class="d-block w-100 hero-img" alt="{{ $banner->title ?? 'Banner' }}">
                    
                    @if($banner->link_url)
                        </a>
                    @endif

                    @if($banner->title || $banner->subtitle)
                        <div class="container hero-content content-left">
                            @if($banner->title)
                                <h1>{!! nl2br(e($banner->title)) !!}</h1>
                            @endif
                            @if($banner->subtitle)
                                <p class="lead text-white fw-bold">{{ $banner->subtitle }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <!-- Fallback Hardcoded Indicators -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>

        <!-- Fallback Hardcoded Items -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-overlay overlay-left"></div>
                <img src="{{ asset('assets/customer/images/banner/banner1.svg') }}" class="d-block w-100 hero-img" alt="Banner 1">
                <div class="container hero-content content-left">
                    <h1>ADIDAS<br>SAMBA<br>BLACK SERIES</h1>
                </div>
            </div>

            <div class="carousel-item">
                <div class="hero-overlay overlay-bottom"></div>
                <img src="{{ asset('assets/customer/images/banner/banner2.svg') }}" class="d-block w-100 hero-img" alt="Banner 2">
                <div class="container hero-content content-bottom-center">
                    <h1>NEW<br>ARRIVAL<br>SPORT</h1>
                </div>
            </div>

            <div class="carousel-item">
                <div class="hero-overlay overlay-left"></div>
                <img src="{{ asset('assets/customer/images/banner/banner3.svg') }}" class="d-block w-100 hero-img" alt="Banner 3">
                <div class="container hero-content content-left">
                    <h1>LIMITED<br>EDITION<br>GEAR</h1>
                </div>
            </div>
        </div>
    @endif

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>

</div>
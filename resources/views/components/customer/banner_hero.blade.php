<div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">

    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner">

        <div class="carousel-item active">
            <div class="hero-overlay overlay-left"></div>
            <img src="{{ asset('assets/images/banner1.svg') }}" class="d-block w-100 hero-img" alt="Banner 1">

            <div class="container hero-content content-left">
                <h1>ADIDAS<br>SAMBA<br>BLACK SERIES</h1>
            </div>
        </div>

        <div class="carousel-item">
            <div class="hero-overlay overlay-bottom"></div>
            <img src="{{ asset('assets/images/banner2.svg') }}" class="d-block w-100 hero-img" alt="Banner 2">

            <div class="container hero-content content-bottom-center">
                <h1>NEW<br>ARRIVAL<br>SPORT</h1>
            </div>
        </div>

        <div class="carousel-item">
            <div class="hero-overlay overlay-left"></div>
            <img src="{{ asset('assets/images/banner3.svg') }}" class="d-block w-100 hero-img" alt="Banner 3">

            <div class="container hero-content content-left">
                <h1>LIMITED<br>EDITION<br>GEAR</h1>
            </div>
        </div>

    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>

</div>
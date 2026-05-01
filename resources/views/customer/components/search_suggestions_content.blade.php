<div class="p-4 bg-white shadow-lg border-top border-secondary-subtle">
    <div class="row">
        <div class="col-12 col-md-6 mb-4">
            <h6 class="fw-bold text-uppercase text-dark" style="font-size: 11px; letter-spacing: 1px;">Pencarian Populer</h6>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <a href="{{ route('product.index', ['search' => 'Samba']) }}" class="btn btn-outline-secondary btn-sm rounded-0 border-secondary-subtle text-dark" style="font-size: 11px;">Samba</a>
                <a href="{{ route('product.index', ['search' => 'Running']) }}" class="btn btn-outline-secondary btn-sm rounded-0 border-secondary-subtle text-dark" style="font-size: 11px;">Running</a>
                <a href="{{ route('product.index', ['search' => 'Jersey']) }}" class="btn btn-outline-secondary btn-sm rounded-0 border-secondary-subtle text-dark" style="font-size: 11px;">Jersey</a>
                <a href="{{ route('product.index', ['search' => 'Specs']) }}" class="btn btn-outline-secondary btn-sm rounded-0 border-secondary-subtle text-dark" style="font-size: 11px;">Specs</a>
            </div>
        </div>

        <div class="col-12 col-md-6 mb-4">
            <h6 class="fw-bold text-uppercase text-dark" style="font-size: 11px; letter-spacing: 1px;">Kategori</h6>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <a href="{{ route('product.index', ['gender' => 'Laki-laki']) }}" class="btn btn-outline-dark btn-sm rounded-0 px-3" style="font-size: 11px;">Kategori Laki-laki</a>
                <a href="{{ route('product.index', ['gender' => 'Perempuan']) }}" class="btn btn-outline-dark btn-sm rounded-0 px-3" style="font-size: 11px;">Kategori Perempuan</a>
                <a href="{{ route('product.index', ['gender' => 'Anak-anak']) }}" class="btn btn-outline-dark btn-sm rounded-0 px-3" style="font-size: 11px;">Kategori Anak-anak</a>
            </div>
        </div>

        <div class="col-12 border-top pt-4">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h6 class="fw-bold text-uppercase mb-0 text-dark" style="font-size: 11px; letter-spacing: 1px;">Pilih Berdasarkan Merek</h6>

                <a href="{{ route('product.index') }}" class="text-dark text-decoration-none fw-bold" style="font-size: 11px;">Lihat Semua <i class="bi bi-arrow-right"></i></a>

            </div>

            <div class="d-flex flex-wrap gap-4 align-items-center">

                <a href="{{ route('product.index', ['brand' => ['Adidas']]) }}"><img src="{{ asset('assets/customer/images/brand/brand adidas.svg') }}" style="height: 30px; filter: grayscale(1);"></a>

                <a href="{{ route('product.index', ['brand' => ['Nike']]) }}"><img src="{{ asset('assets/customer/images/brand/brand nike.svg') }}" style="height: 15px; filter: grayscale(1);"></a>

                <a href="{{ route('product.index', ['brand' => ['Puma']]) }}"><img src="{{ asset('assets/customer/images/brand/brand puma.svg') }}" style="height: 30px; filter: grayscale(1);"></a>

                <a href="{{ route('product.index', ['brand' => ['Ortuseight']]) }}"><img src="{{ asset('assets/customer/images/brand/brand ortuseight.svg') }}" style="height: 43px; filter: grayscale(1);"></a>

            </div>

        </div>
    </div>
</div>
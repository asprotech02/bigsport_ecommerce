@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh;">
        <div class="container-fluid px-4 px-lg-5" style="max-width: 1000px;">
            
            @if(isset($page))
                <div class="text-center mb-5">
                    <h3 class="fw-bold text-uppercase m-0" style="font-size: 30px; font-weight: 800; letter-spacing: 1px;">{{ $page->title }}</h3>
                    <p class="text-secondary mt-3">Terakhir Diperbarui: {{ $page->updated_at->format('d M Y') }}</p>
                </div>

                <div class="content-policy" style="line-height: 1.8; font-size: 15px;">
                    {!! $page->content !!}
                    
                    <hr class="border-secondary-subtle my-5">
                    
                    <div class="text-center">
                        <a href="{{ route('home') }}" class="btn btn-black rounded-0 fw-bold px-4 py-2 text-uppercase" style="letter-spacing: 1px;">KEMBALI KE BERANDA</a>
                    </div>
                </div>
            @else
                <div class="text-center mb-5">
                    <h3 class="fw-bold text-uppercase m-0" style="font-size: 30px; font-weight: 800; letter-spacing: 1px;">Lokasi Toko BigSport</h3>
                    <p class="text-secondary mt-3">Kunjungi Flagship Store kami</p>
                </div>

                <div class="content-policy" style="line-height: 1.8; font-size: 15px;">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-6">
                            <h5 class="fw-bold text-uppercase mb-3" style="font-size: 18px; letter-spacing: 0.5px;">BigSport Flagship Store Jakarta</h5>
                            <p class="text-secondary">
                                Jl. Sudirman No. 45, Kav 21-22,<br>
                                Jakarta Pusat, DKI Jakarta 10210
                            </p>
                            <p class="text-secondary">
                                <strong>Jam Operasional:</strong><br>
                                Senin - Minggu: 10:00 - 22:00 WIB
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="ratio ratio-16x9 bg-light border d-flex align-items-center justify-content-center text-muted" style="border-radius: 4px;">
                                <span><i class="bi bi-map-fill fs-2"></i> Peta Lokasi Toko</span>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="border-secondary-subtle my-5">
                    
                    <div class="text-center">
                        <a href="{{ route('home') }}" class="btn btn-black rounded-0 fw-bold px-4 py-2 text-uppercase" style="letter-spacing: 1px;">KEMBALI KE BERANDA</a>
                    </div>
                </div>
            @endif

        </div>
    </section>

    @include('customer.components.footer')
@endsection

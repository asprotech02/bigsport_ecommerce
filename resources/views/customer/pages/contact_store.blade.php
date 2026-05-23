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
                    <h3 class="fw-bold text-uppercase m-0" style="font-size: 30px; font-weight: 800; letter-spacing: 1px;">Hubungi Kami</h3>
                    <p class="text-secondary mt-3">Hubungi tim BigSport Customer Service</p>
                </div>

                <div class="content-policy" style="line-height: 1.8; font-size: 15px;">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h5 class="fw-bold text-uppercase mb-3" style="font-size: 18px; letter-spacing: 0.5px;">Informasi Kontak</h5>
                            <p class="text-secondary">
                                <i class="bi bi-envelope-fill me-2"></i> <strong>Email:</strong> support@bigsport.com<br>
                                <i class="bi bi-telephone-fill me-2"></i> <strong>Telepon:</strong> (021) 500-888<br>
                                <i class="bi bi-whatsapp me-2"></i> <strong>WhatsApp CS:</strong> +62 812-3456-7890
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold text-uppercase mb-3" style="font-size: 18px; letter-spacing: 0.5px;">Sosial Media Resmi</h5>
                            <p class="text-secondary">
                                <i class="bi bi-instagram me-2"></i> @bigsport_id<br>
                                <i class="bi bi-tiktok me-2"></i> @bigsport.official<br>
                                <i class="bi bi-youtube me-2"></i> BigSport Official Channel
                            </p>
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

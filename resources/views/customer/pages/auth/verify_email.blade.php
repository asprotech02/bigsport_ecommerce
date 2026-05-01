@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white" style="min-height: 60vh;">
        <div class="container" style="max-width: 500px;">
            <div class="auth-card h-100 text-center">
                
                <i class="bi bi-envelope-check" style="font-size: 4rem; color: #333;"></i>
                <h4 class="fw-bold mt-4 mb-3">VERIFIKASI EMAIL ANDA</h4>
                
                <p class="auth-subtitle mb-4 text-muted" style="line-height: 1.6;">
                    Terima kasih telah mendaftar di Big Sport! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan ke kotak masuk Anda.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success rounded-0 mb-4" style="font-size: 14px;">
                        Tautan verifikasi baru telah dikirim ke alamat email Anda.
                    </div>
                @endif

                <p class="text-muted mb-3" style="font-size: 14px;">Tidak menerima email?</p>

                <form action="{{ route('verification.send') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-black w-100 py-2 fw-bold" style="letter-spacing: 1px;">
                        KIRIM ULANG EMAIL
                    </button>
                </form>

                <!-- <div class="mt-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link text-dark fw-bold text-decoration-none p-0" style="font-size: 14px;">
                            <i class="bi bi-box-arrow-left me-1"></i> Keluar
                        </button>
                    </form>
                </div> -->
            </div>
        </div>
    </section>

    @include('customer.components.footer')
@endsection
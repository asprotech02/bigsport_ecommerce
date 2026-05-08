@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh; display: flex; align-items: center;">
        <div class="container d-flex justify-content-center">
            
            <div class="auth-card w-100" style="max-width: 500px;">
                <h4 class="fw-bold mb-4 text-uppercase text-center" style="letter-spacing: 0.5px;">EDIT EMAIL</h4>
                <p class="text-secondary text-center mb-4 small">Pastikan email baru Anda aktif dan dapat menerima pesan verifikasi.</p>

                <form action="#" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label auth-label">Email Saat Ini <span class="text-danger">*</span></label>
                        <input type="email" class="form-control rounded-0 shadow-none auth-input bg-light" value="{{ $user->email }}" readonly>
                    </div>

                    <div class="mb-5">
                        <label class="form-label auth-label">Email Baru <span class="text-danger">*</span></label>
                        <input type="email" name="new_email" class="form-control rounded-0 shadow-none auth-input" required autofocus>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold text-uppercase rounded-0 mb-3" style="letter-spacing: 1px;">SIMPAN EMAIL</button>
                    
                    <!-- <div class="text-center">
                        <a href="{{ route('profile') }}" class="text-secondary text-decoration-none fw-bold" style="font-size: 13px;">
                            <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
                        </a>
                    </div> -->
                </form>
            </div>

        </div>
    </section>

    @include('customer.components.footer')
    @include('customer.components.chatbot')
@endsection
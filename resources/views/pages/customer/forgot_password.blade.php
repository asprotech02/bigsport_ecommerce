@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom">
        <!-- TOPBAR -->
        <x-customer.topbar />
        <!-- TOPBAR -->
        
        <!-- NAVBAR -->
        <x-customer.navbar />
        <!-- NAVBAR -->
    </header>

    <section class="py-5 bg-white" style="min-height: 60vh; display: flex; align-items: center;">
        <div class="container d-flex justify-content-center">
            
            <div class="auth-card w-100" style="max-width: 500px;">
                <h4 class="fw-bold mb-3 text-center">LUPA PASSWORD?</h4>
                <p class="auth-subtitle mb-4 text-center">
                    Masukkan alamat email yang terdaftar pada akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang password Anda.
                </p>

                <form action="#" method="POST">
                    <div class="mb-4">
                        <label class="form-label auth-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control rounded-0 shadow-none auth-input" required>
                    </div>

                    <button type="submit" class="btn btn-black w-100 mb-4">KIRIM TAUTAN RESET</button>
                    
                    <!-- <div class="text-center">
                        <a href="{{ route('login') }}" class="auth-forgot-link text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div> -->
                </form>
            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <x-customer.footer />
    <!-- FOOTER -->

    <!-- CHATBOT -->
    <x-customer.chatbot />
    <!-- CHATBOT -->
@endsection
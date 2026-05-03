@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white" style=" display: flex; align-items: center;">
        <div class="container d-flex justify-content-center">
            
            <div class="auth-card w-100" style="max-width: 500px;">
                <h4 class="fw-bold mb-3 text-center">LUPA PASSWORD?</h4>
                <p class="auth-subtitle mb-4 text-center">
                    Masukkan email yang terdaftar pada akun Anda <br> Kami akan mengirimkan tautan untuk mengatur ulang password Anda
                </p>

                @if (session('status'))
                    <div class="alert text-center mb-4 border-0 rounded-0" style="background-color: #f1f1f1; color: #000000; font-size: 14px;">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST" novalidate>
                    @csrf
                    <div class="mb-4">
                        <label class="form-label auth-label">Email <span class="text-danger">*</span></label>
                        
                        <input type="email" name="email" class="form-control rounded-0 shadow-none auth-input @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-black w-100 mb-4">KIRIM TAUTAN RESET</button>
                    
                </form>
            </div>

        </div>
    </section>

    @include('customer.components.footer')
    @include('customer.components.chatbot')
@endsection
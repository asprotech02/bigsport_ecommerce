@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="auth-card">
                        <h4 class="fw-bold mb-3">MASUK</h4>
                        <p class="auth-subtitle mb-4">Apabila sudah memiliki akun, Masuk dengan email terdaftar</p>

                        <form action="{{ route('login') }}" method="POST" novalidate>
                            @csrf 
                            <div class="mb-3">
                                <label class="form-label auth-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control rounded-0 shadow-none auth-input @error('email', 'login') is-invalid @enderror" value="{{ old('email') }}" autofocus>
                                @error('email', 'login')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label auth-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password" id="loginPassword" class="form-control rounded-0 shadow-none auth-input border-end-0 @error('password', 'login') is-invalid @enderror">
                                    <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer" onclick="togglePassword('loginPassword', 'loginIcon')">
                                        <i class="bi bi-eye-slash text-muted" id="loginIcon"></i>
                                    </span>
                                    @error('password', 'login') 
                                        <div class="invalid-feedback d-block">{{ $message }}</div> 
                                    @enderror
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-black w-100 mb-3">MASUK</button>

                            <div class="text-center mb-3">
                                <a href="{{ route('password.request') }}" class="auth-forgot-link">Lupa Password?</a>
                            </div>

                            <div class="d-flex align-items-center my-3">
                                <hr class="flex-grow-1 text-muted">
                                <span class="mx-3 text-muted fw-bold" style="font-size: 12px;">ATAU</span>
                                <hr class="flex-grow-1 text-muted">
                            </div>

                            <a href="{{ route('google.login') }}" class="btn btn-outline-dark w-100 rounded-0 d-flex align-items-center justify-content-center gap-2 mb-4 py-2" style="font-size: 14px; font-weight: 600;">
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="18" height="18">
                                Masuk dengan Google
                            </a>

                            <div class="text-center border-top pt-3">
                                <p class="auth-subtitle mb-0">Belum punya akun? <a href="{{ route('register') }}" class="text-dark fw-bold text-decoration-none">Daftar Sekarang</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('customer.components.footer')
    @include('customer.components.chatbot')


    @push('scripts')
    <script>
        function togglePassword(inputId, iconId) {
            var input = document.getElementById(inputId);
            var icon = document.getElementById(iconId);
            input.type = input.type === "password" ? "text" : "password";
            icon.classList.toggle("bi-eye");
            icon.classList.toggle("bi-eye-slash");
        }
    </script>
    @endpush
@endsection
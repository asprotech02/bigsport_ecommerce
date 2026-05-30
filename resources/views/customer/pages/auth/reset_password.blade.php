@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh; display: flex; align-items: center;">
        <div class="container" style="max-width: 500px;">
            <div class="auth-card h-100">
                
                {{-- Judul Dinamis --}}
                <h4 class="fw-bold mb-3 text-center text-uppercase">
                    {{ isset($token) ? 'BuAT PASSWORD BARU' : 'EDIT PASSWORD' }}
                </h4>
                <p class="text-secondary mb-4 text-center" style="font-size: 13px;">
                    {{ isset($token) ? 'Silakan masukkan password baru untuk memulihkan akun Anda.' : 'Perbarui password Anda secara berkala untuk menjaga keamanan akun.' }}
                </p>

                {{-- Action Form Dinamis --}}
                <form action="{{ isset($token) ? route('password.store') : route('password.update.profile') }}" method="POST" novalidate>
                    @csrf
                    
                    {{-- JIKA DARI EMAIL (LUPA PASSWORD) --}}
                    @if(isset($token))
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="mb-3">
                            <label class="form-label auth-label">Email Anda <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-0 shadow-none auth-input bg-light @error('email') is-invalid @enderror" value="{{ request()->email }}" readonly required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    {{-- JIKA DARI PROFIL (USER LOGIN) --}}
                    @if(auth()->check() && !isset($token))
                        @method('PATCH')
                        <div class="mb-4">
                            <label class="form-label auth-label">Password Lama <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="old_password" id="oldPassword" class="form-control rounded-0 shadow-none auth-input border-end-0 @error('old_password') is-invalid @enderror" required>
                                <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer @error('old_password') border-danger @enderror" onclick="togglePassword('oldPassword', 'oldIcon')">
                                    <i class="bi bi-eye-slash text-muted" id="oldIcon"></i>
                                </span>
                                @error('old_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    @endif

                    {{-- FIELD PASSWORD BARU (DIGUNAKAN KEDUANYA) --}}
                    <div class="mb-3">
                        <label class="form-label auth-label">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="resetPassword" class="form-control rounded-0 shadow-none auth-input border-end-0 @error('password') is-invalid @enderror" required autofocus>
                            <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer @error('password') border-danger @enderror" onclick="togglePassword('resetPassword', 'resetIcon')">
                                <i class="bi bi-eye-slash text-muted" id="resetIcon"></i>
                            </span>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label auth-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="resetConfirmPassword" class="form-control rounded-0 shadow-none auth-input border-end-0" required>
                            <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer" onclick="togglePassword('resetConfirmPassword', 'resetConfirmIcon')">
                                <i class="bi bi-eye-slash text-muted" id="resetConfirmIcon"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold text-uppercase rounded-0 mb-3" style="letter-spacing: 1px;">
                        SIMPAN PASSWORD
                    </button>
                    
                    {{-- Tombol Batal Hanya Muncul Kalau Lagi Login --}}
                    @if(auth()->check() && !isset($token))
                        <div class="text-center">
                            <a href="{{ route('profile') }}" class="text-secondary text-decoration-none fw-bold" style="font-size: 13px;">
                                <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
                            </a>
                        </div>
                    @endif

                </form>
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

            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace("bi-eye-slash", "bi-eye");
                icon.classList.replace("text-muted", "text-dark");
            } else {
                input.type = "password";
                icon.classList.replace("bi-eye", "bi-eye-slash");
                icon.classList.replace("text-dark", "text-muted");
            }
        }
    </script>
    @endpush
@endsection
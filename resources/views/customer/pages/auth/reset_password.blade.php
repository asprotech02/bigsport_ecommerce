@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
        </header>

    <section class="py-5 bg-white" style="min-height: 70vh;">
        <div class="container" style="max-width: 500px;">
            <div class="auth-card h-100">
                <h4 class="fw-bold mb-3 text-center">BUAT PASSWORD BARU</h4>
                <p class="auth-subtitle mb-4 text-center">Silakan masukkan password baru untuk akun Anda.</p>

                <form action="{{ route('password.store') }}" method="POST" novalidate>
                    @csrf
                    
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label class="form-label auth-label">Email Anda <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-0 shadow-none auth-input @error('email') is-invalid @enderror" value="{{ request()->email }}" readonly required>
                        @error('email') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label auth-label">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="resetPassword" class="form-control rounded-0 shadow-none auth-input border-end-0 @error('password') is-invalid @enderror" required autofocus>
                            
                            <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer @error('password') border-danger @enderror" onclick="togglePassword('resetPassword', 'resetIcon')">
                                <i class="bi bi-eye-slash text-muted" id="resetIcon"></i>
                            </span>
                            
                            @error('password') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
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

                    <button type="submit" class="btn btn-black w-100 py-2 fw-bold" style="letter-spacing: 1px;">SIMPAN PASSWORD</button>
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
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
                icon.classList.remove("text-muted");
                icon.classList.add("text-dark");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
                icon.classList.remove("text-dark");
                icon.classList.add("text-muted");
            }
        }
    </script>
    @endpush
@endsection
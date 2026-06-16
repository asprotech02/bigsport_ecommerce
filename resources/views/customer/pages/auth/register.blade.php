@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="auth-card">
                        <h4 class="fw-bold mb-3">BUAT AKUN</h4>
                        <p class="auth-subtitle mb-4">Daftar sekarang untuk pengalaman belanja yang lebih cepat dan praktis</p>

                        <form action="{{ route('register') }}" method="POST" novalidate>
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label auth-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control rounded-0 shadow-none auth-input @error('name', 'register') is-invalid @enderror" value="{{ old('name') }}" required>
                                    @error('name', 'register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label auth-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" name="birthday" class="form-control rounded-0 shadow-none auth-input @error('birthday', 'register') is-invalid @enderror" value="{{ old('birthday') }}" required>
                                    @error('birthday', 'register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label auth-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select rounded-0 shadow-none auth-input @error('gender', 'register') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender', 'register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label auth-label">Nomor Handphone <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-0 bg-white border-end-0 text-dark fw-bold px-2">+62</span>
                                    <input type="tel" name="phone_number" class="form-control rounded-0 shadow-none auth-input border-start-0 ps-0 @error('phone_number', 'register') is-invalid @enderror" value="{{ old('phone_number') }}" required>
                                </div>
                                @error('phone_number', 'register') <div class="text-danger mt-1" style="font-size: 12px;">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label auth-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control rounded-0 shadow-none auth-input @error('email', 'register') is-invalid @enderror" value="{{ old('email') }}" required>
                                @error('email', 'register') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label auth-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="password" id="regPassword" class="form-control rounded-0 shadow-none auth-input border-end-0 @error('password', 'register') is-invalid @enderror" required>
                                    <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer" onclick="togglePassword('regPassword', 'regIcon')">
                                        <i class="bi bi-eye-slash text-muted" id="regIcon"></i>
                                    </span>
                                </div>
                                @error('password', 'register') <div class="text-danger mt-1" style="font-size: 12px;">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label auth-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" id="regConfirmPassword" class="form-control rounded-0 shadow-none auth-input border-end-0" required>
                                    
                                    <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer" onclick="togglePassword('regConfirmPassword', 'regConfirmIcon')">
                                        <i class="bi bi-eye-slash text-muted" id="regConfirmIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input rounded-0 shadow-none mt-1" type="checkbox" name="terms" id="termsCheck" required>
                                <label class="form-check-label auth-terms" for="termsCheck">
                                    Dengan mendaftar, saya menyetujui pemberian data personal saya ke Bagindo Jaya yang dilindungi oleh <a href="{{ route('privacy') }}" class="text-dark fw-bold text-decoration-underline" target="_blank">kebijakan keamanan data</a> <span class="text-danger">*</span>
                                </label>
                            </div>

                            {{-- FIX: Tambahkan ID dan status awal "disabled" pada tombol --}}
                            <button type="submit" id="btnRegister" class="btn btn-black w-100 mb-3" disabled>DAFTAR</button>

                            <div class="d-flex align-items-center my-3">
                                <hr class="flex-grow-1 text-muted">
                                <span class="mx-3 text-muted fw-bold" style="font-size: 12px;">ATAU</span>
                                <hr class="flex-grow-1 text-muted">
                            </div>

                            <a href="{{ route('google.login') }}" class="btn btn-outline-dark w-100 rounded-0 d-flex align-items-center justify-content-center gap-2 mb-4 py-2" style="font-size: 14px; font-weight: 600;">
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="18" height="18">
                                Daftar dengan Google
                            </a>
                            
                            <div class="text-center border-top pt-3">
                                <p class="auth-subtitle mb-0">Sudah punya akun? <a href="{{ route('login') }}" class="text-dark fw-bold text-decoration-none">Masuk di sini</a></p>
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
        // Logika Toggle Eye Password
        function togglePassword(inputId, iconId) {
            var input = document.getElementById(inputId);
            var icon = document.getElementById(iconId);
            input.type = input.type === "password" ? "text" : "password";
            icon.classList.toggle("bi-eye");
            icon.classList.toggle("bi-eye-slash");
        }

        // FIX: Logika Checkbox untuk mengaktifkan tombol Daftar
        document.addEventListener('DOMContentLoaded', function() {
            const termsCheck = document.getElementById('termsCheck');
            const btnRegister = document.getElementById('btnRegister');

            // Saat halaman pertama dimuat, pastikan tombol disable jika checkbox belum dicentang
            btnRegister.disabled = !termsCheck.checked;

            // Tambahkan event listener saat checkbox di-klik
            termsCheck.addEventListener('change', function() {
                if (this.checked) {
                    btnRegister.disabled = false; // Aktifkan tombol
                } else {
                    btnRegister.disabled = true; // Nonaktifkan tombol
                }
            });
        });
    </script>
    @endpush
@endsection
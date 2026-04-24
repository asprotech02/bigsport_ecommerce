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

    <section class="py-5 bg-white">
        <div class="container" style="max-width: 1000px;">
            <div class="row g-4">
                
                <div class="col-12 col-md-6">
                    <div class="auth-card h-100">
                        <h4 class="fw-bold mb-3">MASUK</h4>
                        <p class="auth-subtitle mb-4">Apabila Anda sudah memiliki akun, Masuk dengan alamat email.</p>

                        <form action="#" method="POST">
                            <div class="mb-3">
                                <label class="form-label auth-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control rounded-0 shadow-none auth-input" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label auth-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="loginPassword" class="form-control rounded-0 shadow-none auth-input border-end-0" required>
                                    <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer" onclick="togglePassword('loginPassword', 'loginIcon')">
                                        <i class="bi bi-eye-slash text-muted" id="loginIcon"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-black w-100 mb-3">MASUK</button>

                            <div class="text-center mt-auto">
                                <a href="{{ route('forgot.password') }}" class="auth-forgot-link">Lupa Password?</a>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3 mt-3">
                                <hr class="flex-grow-1 text-muted">
                                <span class="mx-3 text-muted fw-bold" style="font-size: 12px;">ATAU</span>
                                <hr class="flex-grow-1 text-muted">
                            </div>

                            <a href="{{ url('/auth/google') }}" class="btn btn-outline-dark w-100 rounded-0 d-flex align-items-center justify-content-center gap-2 mb-4 py-2" style="font-size: 14px; font-weight: 600;">
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="18" height="18">
                                Masuk dengan Google
                            </a>

                        </form>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="auth-card h-100">
                        <h4 class="fw-bold mb-3">BUAT AKUN</h4>
                        <p class="auth-subtitle mb-4">Daftar sekarang untuk pengalaman belanja yang lebih cepat dan praktis. Simpan alamat, lakukan checkout dalam hitungan detik, dan pantau status pesanan Anda secara real-time.</p>

                        <form action="#" method="POST">
                            <div class="mb-3">
                                <label class="form-label auth-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-0 shadow-none auth-input" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label auth-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control rounded-0 shadow-none auth-input" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label auth-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-select rounded-0 shadow-none auth-input" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label auth-label">Nomor Handphone <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-0 bg-white border-end-0 text-dark fw-bold px-2">+62</span>
                                    <input type="tel" class="form-control rounded-0 shadow-none auth-input border-start-0 ps-0" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label auth-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control rounded-0 shadow-none auth-input" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label auth-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="regPassword" class="form-control rounded-0 shadow-none auth-input border-end-0" required>
                                    <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer" onclick="togglePassword('regPassword', 'regIcon')">
                                        <i class="bi bi-eye-slash text-muted" id="regIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label auth-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="regConfirmPassword" class="form-control rounded-0 shadow-none auth-input border-end-0" required>
                                    <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer" onclick="togglePassword('regConfirmPassword', 'regConfirmIcon')">
                                        <i class="bi bi-eye-slash text-muted" id="regConfirmIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input rounded-0 shadow-none mt-1" type="checkbox" id="termsCheck" required>
                                <label class="form-check-label auth-terms" for="termsCheck">
                                    Dengan mendaftar, saya menyetujui pemberian data personal saya ke Big Sport Tangerang yang dilindungi oleh <a href="#" class="text-dark">kebijakan keamanan data</a><span class="text-danger">*</span>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-black w-100 mb-3">DAFTAR</button>

                            <div class="d-flex align-items-center mb-3">
                                <hr class="flex-grow-1 text-muted">
                                <span class="mx-3 text-muted fw-bold" style="font-size: 12px;">ATAU</span>
                                <hr class="flex-grow-1 text-muted">
                            </div>

                            <a href="{{ url('/auth/google') }}" class="btn btn-outline-dark w-100 rounded-0 d-flex align-items-center justify-content-center gap-2 py-2" style="font-size: 14px; font-weight: 600;">
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="18" height="18">
                                Daftar dengan Google
                            </a>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <x-customer.footer />
    <!-- FOOTER -->

    <!-- ICON CHATBOT -->
    <x-customer.chatbot />
    <!-- ICON CHATBOT -->



    <!-- ===================================================== -->
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
    <!-- ===================================================== -->
@endsection
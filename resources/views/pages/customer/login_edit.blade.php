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

    <section class="py-5 bg-white" style="min-height: 70vh; display: flex; align-items: center;">
        <div class="container" style="max-width: 1000px;">
            <div class="row g-4">
                
                <div class="col-12 col-md-6">
                    <div class="auth-card h-100">
                        <h5 class="fw-bold mb-4 text-uppercase">EDIT EMAIL</h5>

                        <form action="#" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label auth-label">Email Lama <span class="text-danger">*</span></label>
                                <input type="email" name="old_email" class="form-control rounded-0 shadow-none auth-input" required>
                            </div>

                            <div class="mb-5">
                                <label class="form-label auth-label">Email Baru <span class="text-danger">*</span></label>
                                <input type="email" name="new_email" class="form-control rounded-0 shadow-none auth-input" required>
                            </div>

                            <button type="submit" class="btn btn-black w-100 py-2 mt-auto">SIMPAN</button>
                        </form>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="auth-card h-100">
                        <h5 class="fw-bold mb-4 text-uppercase">EDIT PASSWORD</h5>

                        <form action="#" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label auth-label">Password Lama <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="old_password" id="oldPassword" class="form-control rounded-0 shadow-none auth-input border-end-0" required>
                                    <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer" onclick="togglePassword('oldPassword', 'oldIcon')">
                                        <i class="bi bi-eye-slash text-muted" id="oldIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label auth-label">Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="new_password" id="newPassword" class="form-control rounded-0 shadow-none auth-input border-end-0" required>
                                    <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer" onclick="togglePassword('newPassword', 'newIcon')">
                                        <i class="bi bi-eye-slash text-muted" id="newIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label class="form-label auth-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="new_password_confirmation" id="confirmPassword" class="form-control rounded-0 shadow-none auth-input border-end-0" required>
                                    <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer" onclick="togglePassword('confirmPassword', 'confirmIcon')">
                                        <i class="bi bi-eye-slash text-muted" id="confirmIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-black w-100 py-2">SIMPAN</button>
                        </form>
                    </div>
                </div>

            </div>
            
            <!-- <div class="text-center mt-5">
                <a href="{{ route('profile') }}" class="text-dark text-decoration-none fw-bold" style="font-size: 14px;">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Profil
                </a>
            </div> -->
            
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
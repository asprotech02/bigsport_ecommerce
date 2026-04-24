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
        <div class="container d-flex justify-content-center">
            
            <div class="auth-card w-100" style="max-width: 600px;">
                
                <h4 class="fw-bold mb-3 text-uppercase">BUAT PASSWORD BARU</h4>
                
                <hr class="mb-4" style="border-color: #e0e0e0; opacity: 1;">

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">
                    
                    <input type="hidden" name="email" value="{{ request()->query('email') }}">

                    <div class="mb-4">
                        <label class="form-label auth-label">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="newPassword" class="form-control rounded-0 shadow-none auth-input border-end-0" required>
                            <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer" onclick="togglePassword('newPassword', 'newIcon')">
                                <i class="bi bi-eye-slash text-muted" id="newIcon"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label auth-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="confirmPassword" class="form-control rounded-0 shadow-none auth-input border-end-0" required>
                            <span class="input-group-text rounded-0 bg-white border-start-0 cursor-pointer" onclick="togglePassword('confirmPassword', 'confirmIcon')">
                                <i class="bi bi-eye-slash text-muted" id="confirmIcon"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-black w-100 py-3 text-uppercase" style="letter-spacing: 1px;">SIMPAN</button>
                    
                </form>
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
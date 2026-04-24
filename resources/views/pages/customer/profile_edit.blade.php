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
                <h3 class="fw-bold mb-5 text-uppercase" style="letter-spacing: 0.5px;">EDIT DETAIL PENGGUNA</h3>

                <form action="#" method="POST">
                    
                    <div class="mb-4">
                        <label class="form-label auth-label">Nama Pengguna <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-0 shadow-none auth-input" value="Wisnu Azi" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label auth-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" class="form-control rounded-0 shadow-none auth-input" value="2004-10-02" required>
                    </div>

                    <div class="mb-5">
                        <label class="form-label auth-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-select rounded-0 shadow-none auth-input" required>
                            <option value="L" selected>Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-black w-100 mb-4">SIMPAN</button>
                    
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
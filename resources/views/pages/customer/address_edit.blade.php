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
                <h3 class="fw-bold mb-5 text-uppercase" style="letter-spacing: 0.5px;">EDIT ALAMAT</h3>

                <form action="#" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label auth-label">Provinsi <span class="text-danger">*</span></label>
                        <input type="text" name="provinsi" class="form-control rounded-0 shadow-none auth-input" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label auth-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                        <input type="text" name="kota" class="form-control rounded-0 shadow-none auth-input" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label auth-label">Kecamatan <span class="text-danger">*</span></label>
                        <input type="text" name="kecamatan" class="form-control rounded-0 shadow-none auth-input" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label auth-label">Kode Pos <span class="text-danger">*</span></label>
                        <input type="number" name="kode_pos" class="form-control rounded-0 shadow-none auth-input" required>
                    </div>

                    <div class="mb-5">
                        <label class="form-label auth-label">Detail Titik Alamat <span class="text-danger">*</span></label>
                        <textarea name="detail_alamat" class="form-control rounded-0 shadow-none auth-input" style="height: 100px; resize: none;" placeholder="Jl. Soekarno Hatta RT.004/rw.002..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-black w-100 py-3 text-uppercase fw-bold" style="letter-spacing: 1px;">SIMPAN</button>
                    
                    <!-- <div class="text-center mt-4">
                        <a href="{{ route('address') }}" class="text-dark text-decoration-none fw-bold" style="font-size: 14px;">
                            <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
                        </a>
                    </div> -->
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
@endsection
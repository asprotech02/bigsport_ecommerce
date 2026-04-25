@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        <x-customer.topbar />
        <x-customer.navbar />
    </header>

    <section class="py-4 py-lg-5 bg-white" style="min-height: 70vh;">
        <div class="container">
            
            <div class="row g-5">
                
                <x-customer.profile_menu />

                <div class="col-12 col-lg-9 ps-lg-5">
                    
                    <div class="mb-4">
                        <h3 class="fw-bold text-uppercase m-0" style="font-size: 24px; letter-spacing: 1px;">Hubungi Kami</h3>
                        <p class="text-secondary mt-1" style="font-size: 13px;">Kami siap membantu Anda. Silakan isi form di bawah atau hubungi layanan pelanggan kami.</p>
                    </div>

                    <div class="row g-5">
                        
                        <div class="col-12 col-xl-5">
                            <div class="bg-light-gray p-4 p-md-5 border border-secondary-subtle rounded-0 h-100">
                                <h5 class="fw-bold text-uppercase mb-4" style="font-size: 16px;">Informasi Kontak</h5>
                                
                                <div class="d-flex align-items-start mb-4">
                                    <i class="bi bi-geo-alt fs-5 me-3 text-dark"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1" style="font-size: 13px;">Alamat Toko Utama</h6>
                                        <p class="text-secondary mb-0" style="font-size: 12px; line-height: 1.6;">Jl. Jenderal Sudirman No. 45<br>Tangerang, Banten 15114<br>Indonesia</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start mb-4">
                                    <i class="bi bi-envelope fs-5 me-3 text-dark"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1" style="font-size: 13px;">Email</h6>
                                        <p class="text-secondary mb-0" style="font-size: 12px;">support@bigsport.com</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start">
                                    <i class="bi bi-telephone fs-5 me-3 text-dark"></i>
                                    <div>
                                        <h6 class="fw-bold mb-1" style="font-size: 13px;">Telepon / WhatsApp</h6>
                                        <p class="text-secondary mb-0" style="font-size: 12px;">+62 812 3456 7890<br>(Senin - Sabtu, 09:00 - 18:00)</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-xl-7">
                            <form action="#">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="fw-bold mb-2 text-uppercase" style="font-size: 11px; letter-spacing: 1px;">Nama Lengkap</label>
                                        <input type="text" class="form-control rounded-0 border-dark shadow-none p-3" style="font-size: 13px;" placeholder="John Doe">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="fw-bold mb-2 text-uppercase" style="font-size: 11px; letter-spacing: 1px;">Alamat Email</label>
                                        <input type="email" class="form-control rounded-0 border-dark shadow-none p-3" style="font-size: 13px;" placeholder="john@example.com">
                                    </div>
                                    <div class="col-12">
                                        <label class="fw-bold mb-2 text-uppercase" style="font-size: 11px; letter-spacing: 1px;">Subjek Pesan</label>
                                        <input type="text" class="form-control rounded-0 border-dark shadow-none p-3" style="font-size: 13px;" placeholder="Terkait pesanan, produk, dll">
                                    </div>
                                    <div class="col-12">
                                        <label class="fw-bold mb-2 text-uppercase" style="font-size: 11px; letter-spacing: 1px;">Pesan Anda</label>
                                        <textarea class="form-control rounded-0 border-dark shadow-none p-3" rows="5" style="font-size: 13px;" placeholder="Tuliskan detail pesan Anda di sini..."></textarea>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-action-main w-100 m-0">KIRIM PESAN</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>

            </div>
        </div>
    </section>

    <x-customer.footer />
    <x-customer.chatbot />
@endsection
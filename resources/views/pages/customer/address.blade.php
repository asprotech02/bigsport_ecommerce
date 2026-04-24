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

    <section class="py-5 bg-white" style="min-height: 70vh;">
        <div class="container">
            <div class="row g-5">
                
                <x-customer.profile_menu />

                <div class="col-12 col-lg-9 ps-lg-5">
                    <h3 class="fw-bold mb-5 text-uppercase" style="letter-spacing: 0.5px;">INFORMASI ALAMAT</h3>

                    <div class="mb-5">
                        <h6 class="fw-bold mb-3 text-dark">Detail Alamat</h6>
                        <p class="profile-info-text mb-4" style="line-height: 1.6; max-width: 400px;">
                            Jl. raya pangkalan RT. 004/ RW. 002, Desa Pangkalan, Kecamatan Losarang, Kabupaten Indramayu, Jawa Barat 5560
                        </p>
                        
                        <a href="{{ route('address_edit') }}" class="btn btn-black btn-sm px-4 py-2">Edit</a>
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
@endsection
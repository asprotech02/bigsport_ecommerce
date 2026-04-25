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
                    <h3 class="fw-bold mb-5 text-uppercase" style="letter-spacing: 0.5px;">INFORMASI PENGGUNA</h3>

                    <div class="mb-5">
                        <h6 class="fw-bold mb-3 text-dark">Detail Pengguna</h6>
                        <div class="profile-info-text mb-2">Wisnu Azi</div>
                        <div class="profile-info-text mb-2">02 Oktober 2004</div>
                        <div class="profile-info-text mb-4">Laki-laki</div>
                        <a href="{{ route('profile_edit') }}" class="btn btn-black btn-sm px-4 py-2">Edit</a>
                    </div>
                    
                    <div>
                        <h6 class="fw-bold mb-3 text-dark">Detail Login</h6>
                        <div class="profile-info-text mb-2">wisnuazi404@gmail.com</div>
                        <div class="profile-info-text mb-4">*************</div>
                        <a href="{{ route('login_edit') }}" class="btn btn-black btn-sm px-4 py-2">Edit</a>
                    </div>
                </div>

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
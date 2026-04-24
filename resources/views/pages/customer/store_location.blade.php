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
                    <h3 class="fw-bold mb-5 text-uppercase" style="letter-spacing: 0.5px;">INFORMASI LOKASI TOKO</h3>

                    <div class="row g-4 mb-5 border-bottom pb-4">
                        
                        <div class="col-12 col-lg-6">
                            <div class="rounded overflow-hidden border border-secondary-subtle">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2111151819176!2d106.74217770000001!3d-6.2358797!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f1c3428125af%3A0x948466498a5b6768!2sBigsport%20Tangerang%20Selatan!5e0!3m2!1sen!2sid!4v1776976900153!5m2!1sen!2sid" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-12 col-lg-6 d-flex flex-column justify-content-center">
                            <h5 class="fw-bold text-dark mb-3">Bigsport Tangerang Selatan</h5>
                            
                            <p class="profile-info-text mb-4" style="line-height: 1.6;">
                                Jl. HOS Cokroaminoto No.52, RT.001/RW.005, Larangan, Kec. Larangan, Kota Tangerang, Banten 15156
                            </p>
                            
                            <a href="https://maps.app.goo.gl/rnMiEKk4Zsj1QvNY7" target="_blank" class="btn btn-black px-4 py-2 fw-bold w-100 mt-md-auto">
                                Buka di Google Maps
                            </a>
                        </div>

                    </div>

                    <div class="row g-4 mb-5 border-bottom pb-4">
                        
                        <div class="col-12 col-lg-6">
                            <div class="rounded overflow-hidden border border-secondary-subtle">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.199776241046!2d106.5240772!3d-6.2373785999999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e4207ddf6ae715b%3A0x2c489afb42e19571!2sBig%20Sport%20Tangerang!5e0!3m2!1sen!2sid!4v1776977167727!5m2!1sen!2sid" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-12 col-lg-6 d-flex flex-column justify-content-center">
                            <h5 class="fw-bold text-dark mb-3">Bigsport Citra Raya Tangerang</h5>
                            
                            <p class="profile-info-text mb-4" style="line-height: 1.6;">
                                QG7F+2JW, Cikupa, Tangerang Regency, Banten 15710
                            </p>
                            
                            <a href="https://maps.app.goo.gl/5LmwmRAtm3d8YrQdA" target="_blank" class="btn btn-black px-4 py-2 fw-bold w-100 mt-md-auto">
                                Buka di Google Maps
                            </a>
                        </div>

                    </div>

                    <div class="row g-4 mb-5 border-bottom pb-4">
                        
                        <div class="col-12 col-lg-6">
                            <div class="rounded overflow-hidden border border-secondary-subtle">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.270456395281!2d106.83511519999999!3d-6.487395599999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c10018832d49%3A0x433465532fa752fd!2sBigsport%20Bogor!5e0!3m2!1sen!2sid!4v1776977335820!5m2!1sen!2sid" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-12 col-lg-6 d-flex flex-column justify-content-center">
                            <h5 class="fw-bold text-dark mb-3">Bigsport Bogor</h5>
                            
                            <p class="profile-info-text mb-4" style="line-height: 1.6;">
                                Jl. Kol. Edy Yoso Martadipura No.82, Pakansari, Kec. Cibinong, Kabupaten Bogor, Jawa Barat 16915
                            </p>
                            
                            <a href="https://maps.app.goo.gl/mN5zNpULFVC1xeoa7" target="_blank" class="btn btn-black px-4 py-2 fw-bold w-100 mt-md-auto">
                                Buka di Google Maps
                            </a>
                        </div>

                    </div>

                    <div class="row g-4 mb-5 border-bottom pb-4">
                        
                        <div class="col-12 col-lg-6">
                            <div class="rounded overflow-hidden border border-secondary-subtle">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3967.087146544351!2d106.16038479999999!3d-6.1189712!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e41f58ec1c7445b%3A0x317b7488d0f32851!2sBigsport%20Serang!5e0!3m2!1sen!2sid!4v1776977725872!5m2!1sen!2sid" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                        
                        <div class="col-12 col-lg-6 d-flex flex-column justify-content-center">
                            <h5 class="fw-bold text-dark mb-3">Bigsport Serang</h5>
                            
                            <p class="profile-info-text mb-4" style="line-height: 1.6;">
                                Jl. Jenderal Ahmad Yani Serang, Cipare, Kec. Serang, Kota Serang, Banten 42117
                            </p>
                            
                            <a href="https://maps.app.goo.gl/kBUMAdqRgA466jfo8" target="_blank" class="btn btn-black px-4 py-2 fw-bold w-100 mt-md-auto">
                                Buka di Google Maps
                            </a>
                        </div>

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
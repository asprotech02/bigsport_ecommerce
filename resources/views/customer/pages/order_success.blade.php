@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white d-flex align-items-center justify-content-center" style="min-height: 75vh;">
        <div class="container text-center" style="max-width: 600px;">
            
            <div class="d-flex justify-content-center mb-4">
                <div class="bg-dark text-white d-flex align-items-center justify-content-center rounded-0" style="width: 80px; height: 80px;">
                    <i class="bi bi-check-lg" style="font-size: 40px;"></i>
                </div>
            </div>

            <h2 class="fw-black text-uppercase mb-3" style="font-size: 32px; letter-spacing: 1.5px;">Pesanan Berhasil</h2>
            
            <p class="text-secondary mb-5" style="font-size: 15px; line-height: 1.8;">
                Terima kasih telah berbelanja di toko kami, nomor pesanan Anda adalah <br>
                
                {{-- LOGIKA DINAMIS DIMULAI DI SINI --}}
                <span class="fw-bold text-dark mt-2 mb-2 d-inline-block" style="font-size: 20px;">
                    @if($order)
                        #{{ $order->invoice_number }}
                    @else
                        #TIDAK-ADA-PESANAN
                    @endif
                </span><br>
                {{-- LOGIKA DINAMIS SELESAI --}}

                Kami telah mengirimkan detail pesanan ke email Anda, Cek Email Untuk Cetak Invoice.
            </p>

            <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-3">
                
                <a href="{{ url('/') }}" class="btn btn-outline-dark fw-bold text-uppercase rounded-0 d-flex justify-content-center align-items-center" style="width: 240px; height: 50px; font-size: 13px; letter-spacing: 1px; border-width: 2px;">
                    Kembali Belanja
                </a>
                
                <a href="{{ route('profile', ['tab' => 'orders']) }}" class="btn btn-action-main m-0 fw-bold text-uppercase d-flex justify-content-center align-items-center" style="width: 240px; height: 50px; font-size: 13px; letter-spacing: 1px;">
                    Lihat Pesanan
                </a>

            </div>

        </div>
    </section>

    @include('customer.components.footer')
    @include('customer.components.chatbot')
@endsection
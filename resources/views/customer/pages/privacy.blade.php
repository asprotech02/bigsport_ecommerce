@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh;">
        <div class="container-fluid px-4 px-lg-5" style="max-width: 1000px;">
            
            @if(isset($page))
                <div class="text-center mb-5">
                    <h3 class="fw-bold text-uppercase m-0" style="font-size: 30px; font-weight: 800; letter-spacing: 1px;">{{ $page->title }}</h3>
                    <p class="text-secondary mt-3">Terakhir Diperbarui: {{ $page->updated_at->format('d M Y') }}</p>
                </div>

                <div class="content-policy" style="line-height: 1.8; font-size: 15px;">
                    {!! $page->content !!}
                    
                    <hr class="border-secondary-subtle my-5">
                    
                    <div class="text-center">
                        <a href="{{ route('home') }}" class="btn btn-black rounded-0 fw-bold px-4 py-2 text-uppercase" style="letter-spacing: 1px;">KEMBALI KE BERANDA</a>
                    </div>
                </div>
            @else
                <div class="text-center mb-5">
                    <h3 class="fw-bold text-uppercase m-0" style="font-size: 30px; font-weight: 800; letter-spacing: 1px;">Kebijakan Keamanan Data</h3>
                    <p class="text-secondary mt-3">Terakhir Diperbarui: {{ date('d M Y') }}</p>
                </div>

                <div class="content-policy" style="line-height: 1.8; font-size: 15px;">
                    <p class="mb-4 text-secondary">
                        Selamat datang di Big Sport. Kami sangat menghargai privasi Anda dan berkomitmen untuk melindungi informasi pribadi yang Anda berikan kepada kami. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi data Anda saat menggunakan layanan platform kami.
                    </p>

                    <h5 class="fw-bold text-uppercase mb-3 mt-5" style="font-size: 16px; letter-spacing: 0.5px;">1. Pengumpulan Data Informasi</h5>
                    <p class="mb-4 text-secondary">
                        Kami mengumpulkan informasi yang Anda berikan secara langsung kepada kami saat mendaftar akun, melakukan transaksi, atau mengisi form alamat pengiriman. Informasi ini mencakup namun tidak terbatas pada: Nama lengkap, email, nomor telepon genggam, dan alamat lengkap pengiriman.
                    </p>

                    <h5 class="fw-bold text-uppercase mb-3 mt-4" style="font-size: 16px; letter-spacing: 0.5px;">2. Penggunaan Data</h5>
                    <p class="mb-3 text-secondary">Data personal yang kami kumpulkan akan digunakan secara eksklusif untuk:</p>
                    <ul class="text-secondary mb-4">
                        <li class="mb-2">Memproses pesanan dan melakukan pengiriman barang melalui kurir rekanan (seperti JNE, J&T, SiCepat).</li>
                        <li class="mb-2">Mengirimkan pembaruan status pesanan (Order Tracking) dan notifikasi.</li>
                        <li class="mb-2">Meningkatkan keamanan transaksi belanja Anda.</li>
                    </ul>

                    <h5 class="fw-bold text-uppercase mb-3 mt-4" style="font-size: 16px; letter-spacing: 0.5px;">3. Perlindungan & Pembagian Data</h5>
                    <p class="mb-4 text-secondary">
                        Big Sport <strong>tidak akan pernah</strong> menjual, menyewakan, atau menukar data pribadi Anda kepada pihak ketiga untuk tujuan pemasaran. Data Anda hanya akan dibagikan kepada pihak ketiga yang berwenang (seperti Midtrans untuk pemrosesan pembayaran dan layanan ekspedisi) semata-mata untuk keperluan penyelesaian pesanan Anda.
                    </p>

                    <h5 class="fw-bold text-uppercase mb-3 mt-4" style="font-size: 16px; letter-spacing: 0.5px;">4. Keamanan Pembayaran</h5>
                    <p class="mb-4 text-secondary">
                        Semua transaksi pembayaran di Big Sport diproses melalui gerbang pembayaran resmi berstandar internasional (Payment Gateway - Midtrans). Kami tidak pernah menyimpan detail kartu kredit atau informasi perbankan rahasia Anda di dalam server kami.
                    </p>

                    <h5 class="fw-bold text-uppercase mb-3 mt-4" style="font-size: 16px; letter-spacing: 0.5px;">5. Hak Pengguna</h5>
                    <p class="mb-5 text-secondary">
                        Anda memiliki hak penuh untuk mengakses, memperbarui, atau meminta penghapusan data pribadi Anda kapan saja melalui menu Profil. Jika Anda membutuhkan bantuan lebih lanjut, silakan hubungi tim dukungan kami di <a href="mailto:support@bigsport.com" class="text-dark fw-bold text-decoration-underline">support@bigsport.com</a>.
                    </p>
                    
                    <hr class="border-secondary-subtle my-5">
                    
                    <div class="text-center">
                        <a href="{{ route('home') }}" class="btn btn-black rounded-0 fw-bold px-4 py-2 text-uppercase" style="letter-spacing: 1px;">KEMBALI KE BERANDA</a>
                    </div>
                </div>
            @endif

        </div>
    </section>

    @include('customer.components.footer')
@endsection
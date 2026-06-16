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
                    <h3 class="fw-bold text-uppercase m-0" style="font-size: 30px; font-weight: 800; letter-spacing: 1px;">Syarat & Ketentuan</h3>
                    <p class="text-secondary mt-3">Ketentuan Penggunaan Platform Bagindo Jaya</p>
                </div>

                <div class="content-policy" style="line-height: 1.8; font-size: 15px;">
                    <p class="mb-4 text-secondary">
                        Dengan mengakses dan menggunakan situs web serta layanan kami di <strong>Bagindo Jaya</strong>, Anda dinyatakan telah menyetujui, tunduk pada, dan terikat oleh syarat dan ketentuan penggunaan di bawah ini. Harap membaca ketentuan ini secara saksama sebelum bertransaksi.
                    </p>

                    <h5 class="fw-bold text-uppercase mb-3 mt-5" style="font-size: 16px; letter-spacing: 0.5px;">1. Akun Pengguna</h5>
                    <p class="mb-4 text-secondary">
                        Untuk berbelanja di Bagindo Jaya, Anda diwajibkan melakukan pendaftaran akun dengan data email dan informasi personal yang sah. Anda bertanggung jawab penuh untuk menjaga kerahasiaan informasi akun dan password Anda, serta bertanggung jawab atas semua aktivitas yang terjadi di bawah akun Anda.
                    </p>

                    <h5 class="fw-bold text-uppercase mb-3 mt-4" style="font-size: 16px; letter-spacing: 0.5px;">2. Kebijakan Transaksi & Pembelian</h5>
                    <p class="mb-4 text-secondary">
                        Semua harga produk yang tertera di situs kami dinyatakan dalam Rupiah (IDR). Kami berhak melakukan penyesuaian harga atau memperbarui detail produk sewaktu-waktu tanpa pemberitahuan terlebih dahulu. Pesanan Anda dianggap selesai setelah pembayaran terverifikasi secara resmi oleh sistem Payment Gateway kami (Midtrans).
                    </p>

                    <h5 class="fw-bold text-uppercase mb-3 mt-4" style="font-size: 16px; letter-spacing: 0.5px;">3. Ketentuan Pengiriman</h5>
                    <p class="mb-4 text-secondary">
                        Pengiriman pesanan diproses melalui layanan logistik terintegrasi (Biteship) menggunakan ekspedisi rekanan. Estimasi waktu pengiriman disesuaikan dengan jenis paket pengiriman yang dipilih pada saat checkout. Kesalahan pengisian alamat oleh pengguna yang menyebabkan keterlambatan atau hilangnya paket bukan merupakan tanggung jawab Bagindo Jaya.
                    </p>

                    <h5 class="fw-bold text-uppercase mb-3 mt-4" style="font-size: 16px; letter-spacing: 0.5px;">4. Pembatalan Transaksi</h5>
                    <p class="mb-4 text-secondary">
                        Pembatalan pesanan hanya dapat diajukan apabila status pesanan masih dalam tahap verifikasi pembayaran (Pending) atau sebelum barang diproses oleh Admin. Begitu nomor resi pengiriman telah diterbitkan oleh sistem logistik, pesanan tidak dapat dibatalkan secara sepihak oleh pelanggan.
                    </p>

                    <h5 class="fw-bold text-uppercase mb-3 mt-4" style="font-size: 16px; letter-spacing: 0.5px;">5. Hak Kekayaan Intelektual</h5>
                    <p class="mb-5 text-secondary">
                        Semua konten yang tersedia di situs web ini, termasuk namun tidak terbatas pada teks, grafik, logo, ikon, gambar, klip suara, dan kompilasi data adalah milik eksklusif Bagindo Jaya atau pemegang lisensinya dan dilindungi oleh undang-undang hak cipta yang berlaku di Indonesia.
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

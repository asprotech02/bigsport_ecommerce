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
                    <h3 class="fw-bold text-uppercase m-0" style="font-size: 30px; font-weight: 800; letter-spacing: 1px;">Tentang Kami</h3>
                    <p class="text-secondary mt-3">Mengenal Lebih Dekat Bagindo Jaya</p>
                </div>

                <div class="content-policy" style="line-height: 1.8; font-size: 15px;">
                    <p class="mb-4 text-secondary text-center fs-5 fw-semibold" style="color: #111 !important;">
                        "Langkah Terbaik untuk Performa Maksimal Anda"
                    </p>
                    <p class="mb-4 text-secondary">
                        Selamat datang di <strong>Bagindo Jaya</strong>, destinasi terpercaya Anda untuk kebutuhan sepatu olahraga, pakaian, dan aksesori olahraga premium. Berdiri dengan komitmen untuk menyediakan produk berkualitas tinggi, kami bangga menjadi mitra terpercaya bagi para atlet, pecinta olahraga, dan siapa saja yang mengutamakan kenyamanan serta performa terbaik dalam aktivitas sehari-hari.
                    </p>

                    <h5 class="fw-bold text-uppercase mb-3 mt-5" style="font-size: 16px; letter-spacing: 0.5px;">Visi Kami</h5>
                    <p class="mb-4 text-secondary">
                        Menjadi e-commerce perlengkapan olahraga terdepan di Indonesia yang menginspirasi gaya hidup sehat dan aktif bagi seluruh lapisan masyarakat melalui penyediaan produk original berkualitas tinggi dan layanan pelanggan terbaik.
                    </p>

                    <h5 class="fw-bold text-uppercase mb-3 mt-4" style="font-size: 16px; letter-spacing: 0.5px;">Misi Kami</h5>
                    <ul class="text-secondary mb-4">
                        <li class="mb-2">Menyediakan kurasi produk olahraga orisinal dari merk-merk global dan lokal terkemuka.</li>
                        <li class="mb-2">Menghadirkan pengalaman berbelanja online yang mudah, aman, cepat, dan transparan.</li>
                        <li class="mb-2">Memberikan pelayanan pelanggan yang ramah, solutif, dan profesional untuk menjamin kepuasan maksimal.</li>
                        <li class="mb-2">Mendukung perkembangan komunitas olahraga di Indonesia melalui program kemitraan dan kolaborasi aktif.</li>
                    </ul>

                    <h5 class="fw-bold text-uppercase mb-3 mt-4" style="font-size: 16px; letter-spacing: 0.5px;">Mengapa Berbelanja di Bagindo Jaya?</h5>
                    <p class="mb-4 text-secondary">
                        Kami menjamin semua produk yang kami jual adalah <strong>100% Original</strong> langsung dari distributor resmi. Dengan sistem logistik modern yang terintegrasi (Biteship) dan pembayaran instan yang aman (Midtrans), kami memastikan pesanan Anda sampai ke tujuan dengan aman dan tepat waktu.
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

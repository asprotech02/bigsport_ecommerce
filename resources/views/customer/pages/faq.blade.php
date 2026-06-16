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
                    <h3 class="fw-bold text-uppercase m-0" style="font-size: 30px; font-weight: 800; letter-spacing: 1px;">Pertanyaan yang sering diajukan (FAQ)</h3>
                    <p class="text-secondary mt-3">Temukan jawaban cepat atas pertanyaan Anda</p>
                </div>

                <div class="content-policy" style="line-height: 1.8; font-size: 15px;">
                    
                    <div class="accordion accordion-flush" id="faqAccordion">
                        
                        <!-- FAQ 1 -->
                        <div class="accordion-item border-bottom py-2">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed fw-bold text-uppercase shadow-none bg-transparent py-3 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne" style="font-size: 15px; letter-spacing: 0.5px;">
                                    Bagaimana cara memesan di Bagindo Jaya?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary" style="font-size: 14px; line-height: 1.7;">
                                    Sangat mudah! Pilih produk yang Anda inginkan, tentukan ukuran (size/SKU) yang sesuai, lalu klik tombol <strong>"Tambah ke Keranjang"</strong>. Setelah selesai memilih, buka Keranjang Belanja Anda, klik <strong>"Checkout"</strong>, masukkan alamat pengiriman dengan lengkap, pilih kurir ekspedisi, lalu selesaikan pembayaran Anda menggunakan metode yang tersedia.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="accordion-item border-bottom py-2">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed fw-bold text-uppercase shadow-none bg-transparent py-3 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="font-size: 15px; letter-spacing: 0.5px;">
                                    Apa saja metode pembayaran yang didukung?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary" style="font-size: 14px; line-height: 1.7;">
                                    Kami terintegrasi dengan Payment Gateway Midtrans yang mendukung berbagai opsi pembayaran aman, termasuk:
                                    <ul class="mt-2 mb-0 ps-3">
                                        <li>Transfer Bank / Virtual Account (BCA, Mandiri, BNI, BRI, dll.)</li>
                                        <li>QRIS (Gopay, ShopeePay, Dana, LinkAja, OVO, dll.)</li>
                                        <li>Kartu Kredit / Debit Online (Visa, Mastercard, JCB)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="accordion-item border-bottom py-2">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed fw-bold text-uppercase shadow-none bg-transparent py-3 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" style="font-size: 15px; letter-spacing: 0.5px;">
                                    Apakah produk di Bagindo Jaya 100% Original?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary" style="font-size: 14px; line-height: 1.7;">
                                    Ya, jaminan 100% Original. Semua produk sepatu olahraga dan aksesori di Bagindo Jaya dipasok langsung dari distributor resmi merk masing-masing. Jika terbukti palsu, kami memberikan garansi uang kembali 100%.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="accordion-item border-bottom py-2">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed fw-bold text-uppercase shadow-none bg-transparent py-3 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour" style="font-size: 15px; letter-spacing: 0.5px;">
                                    Berapa lama proses pengiriman barang?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary" style="font-size: 14px; line-height: 1.7;">
                                    Proses penyiapan barang dari gudang kami memakan waktu maksimal 1x24 jam setelah pembayaran lunas pada hari kerja. Estimasi pengiriman kurir bervariasi bergantung pada lokasi tujuan Anda:
                                    <ul class="mt-2 mb-0 ps-3">
                                        <li>Instant/Sameday: 3 - 6 Jam</li>
                                        <li>Regular/Hemat: 2 - 4 Hari Kerja</li>
                                        <li>Kargo (luar pulau/berat): 5 - 10 Hari Kerja</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="accordion-item border-bottom py-2">
                            <h2 class="accordion-header" id="headingFive">
                                <button class="accordion-button collapsed fw-bold text-uppercase shadow-none bg-transparent py-3 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive" style="font-size: 15px; letter-spacing: 0.5px;">
                                    Bagaimana cara melacak pesanan saya?
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary" style="font-size: 14px; line-height: 1.7;">
                                    Anda dapat melacak status pesanan secara real-time langsung melalui web kami. Caranya, masuk ke menu <strong>Profil</strong> Anda, klik tab <strong>Pesanan</strong>, kemudian klik pesanan yang ingin Anda lacak. Halaman rincian akan menampilkan status perjalanan kurir secara berkala.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6 -->
                        <div class="accordion-item border-bottom py-2">
                            <h2 class="accordion-header" id="headingSix">
                                <button class="accordion-button collapsed fw-bold text-uppercase shadow-none bg-transparent py-3 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix" style="font-size: 15px; letter-spacing: 0.5px;">
                                    Bisakah saya menukar ukuran sepatu yang kekecilan/kebesaran?
                                </button>
                            </h2>
                            <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary" style="font-size: 14px; line-height: 1.7;">
                                    Bisa. Kami memperbolehkan penukaran ukuran (size exchange) dalam waktu maksimal 3 hari sejak barang diterima, dengan syarat tag label produk masih utuh, kotak sepatu tidak rusak, dan produk belum pernah digunakan untuk beraktivitas. Untuk informasi selengkapnya, silakan baca halaman <strong>Kebijakan Pengembalian</strong>.
                                </div>
                            </div>
                        </div>

                    </div>
                    
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

@push('styles')
<style>
    .accordion-button::after {
        filter: grayscale(1) invert(0);
    }
    .accordion-button:not(.collapsed) {
        color: #000 !important;
        background-color: transparent !important;
        box-shadow: none !important;
    }
    .accordion-button:focus {
        box-shadow: none !important;
    }
</style>
@endpush

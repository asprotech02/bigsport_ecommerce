<footer class="bg-black-custom text-white pt-5 pb-4 mt-5">
    <div class="container">
        
        <div class="text-center mb-5">
            <a href="/" class="d-inline-block">
                <img src="{{ asset('assets/logo.png') }}" alt="Bagindo Jaya Logo" style="height: 50px; object-fit: contain; filter: drop-shadow(0 0 5px rgba(255,255,255,0.25));">
            </a>
        </div>

        <div class="row justify-content-between mb-5">
            
            <div class="col-12 col-md-6 col-lg-3 mb-4 mb-lg-0">
                <h6 class="fw-bold mb-4 text-white">Hubungi Kami</h6>
                <div class="footer-text mb-4">
                    <strong class="d-block text-white mb-1">Jam Operasional</strong>
                    Senin - Minggu<br>
                    08:00 - 22:00
                </div>
                <div class="footer-text">
                    Email : bagindojaya@gmail.com<br>
                    Whatsapp : +62 0899 0689 0788
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-auto mb-4 mb-lg-0">
                <h6 class="fw-bold mb-4 text-white">Tentang Kami</h6>
                <ul class="list-unstyled footer-links m-0 p-0">
                    <li class="mb-3"><a href="{{ route('about') }}">Tentang Kami</a></li>
                    <li class="mb-3"><a href="{{ route('terms') }}">Syarat & Ketentuan</a></li>
                    <li class="mb-3"><a href="{{ route('privacy') }}">Kebijakan Privasi</a></li>
                </ul>
            </div>

            <div class="col-12 col-md-6 col-lg-auto mb-4 mb-lg-0">
                <h6 class="fw-bold mb-4 text-white">Pelayanan Pelanggan</h6>
                <ul class="list-unstyled footer-links m-0 p-0">
                    <li class="mb-3"><a href="{{ route('faq') }}">Pertanyaan yang sering diajukan (FAQ)</a></li>
                    <li class="mb-3"><a href="{{ route('returns') }}">Kebijakan Pengembalian</a></li>
                    <li class="mb-3"><a href="{{ route('store_location') }}">Lokasi Toko</a></li>
                </ul>
            </div>

            <div class="col-12 col-md-6 col-lg-auto">
                <h6 class="fw-bold mb-4 text-white">Ikuti Kami</h6>
                <div class="d-flex flex-column gap-3">
                    <a href="#" class="social-icon-box" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="social-icon-box" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="social-icon-box" aria-label="TikTok">
                        <i class="bi bi-tiktok"></i>
                    </a>
                </div>
            </div>

        </div>

        <div class="text-center pt-3 footer-copyright">
            <p class="mb-0">2026 &copy; BAGINDO JAYA. SEMUA HAK CIPTA DILINDUNGI</p>
        </div>

    </div>
</footer>
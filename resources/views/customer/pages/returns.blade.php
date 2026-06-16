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
                    <h3 class="fw-bold text-uppercase m-0" style="font-size: 30px; font-weight: 800; letter-spacing: 1px;">Kebijakan Pengembalian & Penukaran</h3>
                    <p class="text-secondary mt-3">Panduan Pengembalian Barang dan Penukaran Ukuran</p>
                </div>

                <div class="content-policy" style="line-height: 1.8; font-size: 15px;">
                    <p class="mb-4 text-secondary">
                        Kami di <strong>Bagindo Jaya</strong> selalu berkomitmen untuk memastikan kepuasan belanja Anda terjamin. Jika barang yang Anda terima mengalami cacat produksi, salah pengiriman, atau Anda ingin menukar ukuran (size exchange), kami menyediakan layanan pengembalian dan penukaran yang mudah dan transparan.
                    </p>

                    <h5 class="fw-bold text-uppercase mb-3 mt-5" style="font-size: 16px; letter-spacing: 0.5px;">1. Syarat dan Ketentuan Pengembalian</h5>
                    <p class="mb-3 text-secondary">Barang dapat dikembalikan atau ditukar dengan ketentuan sebagai berikut:</p>
                    <ul class="text-secondary mb-4">
                        <li class="mb-2">Pengajuan dilakukan maksimal <strong>3 hari</strong> sejak barang dinyatakan diterima berdasarkan data resi pengiriman kurir.</li>
                        <li class="mb-2">Produk harus dalam kondisi asli, belum pernah dipakai untuk beraktivitas, tidak dicuci, dan tidak meninggalkan aroma parfum/keringat.</li>
                        <li class="mb-2">Label harga (price tag), tag merk, dan semua kelengkapan aksesoris produk wajib terpasang utuh pada produk.</li>
                        <li class="mb-2">Kotak sepatu asli (shoebox) atau plastik kemasan pelindung harus dikirim kembali dalam keadaan utuh dan tidak ditempeli isolasi/lakban secara langsung (gunakan plastik pembungkus luar tambahan).</li>
                    </ul>

                    <h5 class="fw-bold text-uppercase mb-3 mt-4" style="font-size: 16px; letter-spacing: 0.5px;">2. Produk yang Tidak Dapat Ditukar</h5>
                    <p class="mb-4 text-secondary">
                        Demi menjaga higienitas dan standar kualitas produk, beberapa kategori barang berikut <strong>tidak dapat dikembalikan atau ditukar</strong>: Pakaian dalam (undergarments), kaos kaki (socks), deker/pelindung kaki, serta produk diskon clearance sale tertentu yang berlabel "Tidak Dapat Ditukar/Dikembalikan".
                    </p>

                    <h5 class="fw-bold text-uppercase mb-3 mt-4" style="font-size: 16px; letter-spacing: 0.5px;">3. Alur Proses Pengembalian (Step-by-Step)</h5>
                    <ol class="text-secondary mb-4">
                        <li class="mb-2">Hubungi Customer Service kami via WhatsApp di <strong class="text-dark">+62 812-3456-7890</strong> dengan melampirkan nomor invoice pesanan Anda.</li>
                        <li class="mb-2">Kirimkan bukti berupa foto produk yang ingin ditukar dan video unboxing saat paket pertama kali dibuka (wajib untuk klaim cacat produksi/salah kirim).</li>
                        <li class="mb-2">Setelah mendapatkan konfirmasi persetujuan dari tim CS kami, kirimkan produk kembali ke alamat gudang kami yang diinformasikan oleh CS.</li>
                        <li class="mb-2">Setelah produk tiba di gudang kami dan lolos inspeksi kualitas, kami akan segera mengirimkan produk pengganti atau memproses pengembalian dana (refund) dalam kurun waktu 1-3 hari kerja.</li>
                    </ol>

                    <h5 class="fw-bold text-uppercase mb-3 mt-4" style="font-size: 16px; letter-spacing: 0.5px;">4. Ongkos Kirim Pengembalian</h5>
                    <p class="mb-5 text-secondary">
                        Apabila kesalahan terletak pada pihak kami (barang cacat, salah kirim warna/ukuran), maka seluruh biaya pengiriman bolak-balik akan ditanggung sepenuhnya oleh Bagindo Jaya. Namun, apabila penukaran dilakukan atas keinginan pelanggan (misalnya penukaran ukuran karena kekecilan/kebesaran), maka ongkos kirim pengiriman kembali ke gudang kami dan pengiriman ulang ke alamat pelanggan ditanggung sepenuhnya oleh pihak pelanggan.
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

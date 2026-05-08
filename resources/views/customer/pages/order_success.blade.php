@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 d-flex align-items-center justify-content-center" style="min-height: 85vh;">
        <div class="container d-flex justify-content-center">
            
            {{-- Kotak Invoice dengan gaya Minimalis & Garis Tegas --}}
            <div class="bg-white p-4 p-md-5 border border-dark shadow-sm position-relative" style="max-width: 550px; width: 100%; border-width: 2px !important;">
                
                {{-- 🌟 PERMINTAAN 1: HILANGKAN PUTIH HITAM JADIKAN GARIS HITAM SAJA (Receipt Style Points) --}}
                {{-- Kita pakai pseudo-element CSS di style tag bawah biar lebih rapi dan 'lines only' --}}
                <div class="receipt-edge-top position-absolute top-0 start-0 w-100"></div>

                <div class="d-flex justify-content-center mb-4 mt-4">
                    {{-- 🌟 PERMINTAAN 2: BUAT JADI BACKGROUND HITAM CEKLISNYA PUTIH --}}
                    <div class="success-checkmark-wrapper d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background-color: #000;">
                        {{-- Hapus stroke biar kelihatan bersih --}}
                        <i class="bi bi-check-lg text-white" style="font-size: 40px;"></i>
                    </div>
                </div>

                <div class="text-center mb-4">
                    <h2 class="fw-black text-uppercase mb-2" style="font-size: 26px; letter-spacing: 1px;">Pembayaran Berhasil</h2>
                    <p class="text-secondary" style="font-size: 14px; line-height: 1.6;">
                        Terima kasih telah berbelanja di Big Sport, Pesanan Anda telah kami terima dan akan segera kami proses.
                    </p>
                </div>

                {{-- KOTAK DETAIL PESANAN DINAMIS --}}
                @if(isset($order) && $order)
                <div class="bg-light p-4 mb-4 border border-secondary-subtle">
                    <div class="row g-3 text-start">
                        <div class="col-6">
                            <span class="text-secondary d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Nomor Invoice</span>
                            <span class="fw-bold text-dark" style="font-size: 15px;">#{{ $order->invoice_number }}</span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-secondary d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Waktu Transaksi</span>
                            <span class="fw-bold text-dark" style="font-size: 14px;">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="col-12 border-top border-secondary-subtle pt-3 mt-3 d-flex justify-content-between align-items-center">
                            <span class="text-dark fw-bold" style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Total Pembayaran</span>
                            
                            {{-- 🌟 PERMINTAAN 3: FONT UKURANNYA SAMA DAN WARNANYA JUGA SAMA HITAM --}}
                            {{-- Ubah text-danger jadi text-dark (HILANGKAN MERAH JADI HITAM) --}}
                            <span class="fw-bold text-dark" style="font-size: 15px;">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-light p-3 mb-4 border border-secondary-subtle text-center">
                    <span class="text-secondary" style="font-size: 13px;">Detail pesanan tidak dapat dimuat saat ini</span>
                </div>
                @endif

                <div class="text-center mb-5">
                    <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.6;">
                        Kami telah mengirimkan detail pesanan ke email Anda, Cek Email Untuk Cetak Invoice.
                        Simpan nomor invoice di atas untuk keperluan pelacakan.
                    </p>
                </div>

                <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center gap-2">
                    <a href="{{ route('profile', ['tab' => 'orders']) }}" class="btn btn-dark rounded-0 fw-bold text-uppercase w-100 d-flex justify-content-center align-items-center" style="height: 48px; font-size: 13px; letter-spacing: 1px;">
                        Lihat Pesanan
                    </a>
                    <a href="{{ url('/') }}" class="btn btn-outline-dark rounded-0 fw-bold text-uppercase w-100 d-flex justify-content-center align-items-center" style="height: 48px; font-size: 13px; letter-spacing: 1px; border-width: 2px;">
                        Kembali Belanja
                    </a>
                </div>

            </div>
        </div>
    </section>

    @include('customer.components.footer')
    @include('customer.components.chatbot')

    @push('styles')
    <style>
        /* 🌟 PERMINTAAN 1 & 2: LOGIKA STYLING RECEIPT & CEKLIS */

        /* 1. Receipt Edge (Points/Lines only) */
        .receipt-edge-top {
            height: 12px;
            /* Kita pakai gradient pointier biar kelihatan 'hanya garis/points hitam' */
            background-size: 16px 16px;
            background-image: radial-gradient(circle at 8px -8px, transparent 10px, #fff 11px);
        }

        /* 2. Animasi pop-in untuk ikon centang biar kelihatan modern */
        .success-checkmark-wrapper {
            animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            opacity: 0;
            transform: scale(0.5);
        }

        @keyframes popIn {
            0% { opacity: 0; transform: scale(0.5); }
            100% { opacity: 1; transform: scale(1); }
        }

        /* override default bg-light-gray ke yang lebih clean as requested */
        .bg-light-gray {
            background-color: #f4f5f7 !important;
        }
    </style>
    @endpush
@endsection
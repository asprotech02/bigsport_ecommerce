@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh;">
        <div class="container">
            
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                    
                    {{-- HEADER TAB NOTIFIKASI --}}
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center border-bottom border-dark pb-3 mb-4">
                        <div class="d-flex gap-4 mb-3 mb-sm-0 fw-bold text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
                            <a href="#" class="text-dark text-decoration-none border-bottom border-dark border-2 pb-1">Semua</a>
                            <a href="#" class="text-secondary text-decoration-none hover-text-dark pb-1">Transaksi</a>
                            <a href="#" class="text-secondary text-decoration-none hover-text-dark pb-1">Promo</a>
                        </div>
                        {{-- Tombol Tandai Dibaca (Bisa lu kasih fitur AJAX nanti) --}}
                        <form action="#" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 text-secondary text-decoration-none text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px; border-bottom: 1px dashed #ccc;">
                                Tandai Semua Dibaca
                            </button>
                        </form>
                    </div>

                    {{-- LOOPING DATA DARI DATABASE --}}
                    @forelse($notifications as $notif)
                        @php
                            // 1. LOGIKA IKON & WARNA BERDASARKAN JUDUL
                            $titleLower = strtolower($notif->title);
                            $icon = 'bi-box-seam'; // Default icon
                            $titleClass = '';
                            
                            if (str_contains($titleLower, 'berhasil') || str_contains($titleLower, 'tiba')) {
                                $icon = 'bi-check2-square';
                            } elseif (str_contains($titleLower, 'batal')) {
                                $icon = 'bi-x-circle';
                            } elseif (strtolower($notif->type) == 'promo' || str_contains($titleLower, 'deals')) {
                                $icon = 'bi-tag-fill';
                                $titleClass = 'text-sale text-danger'; 
                            } elseif (str_contains($titleLower, 'sandi') || str_contains($titleLower, 'akun')) {
                                $icon = 'bi-person-fill-gear';
                            }

                            // 2. LOGIKA DESAIN BELUM DIBACA VS SUDAH DIBACA
                            $itemBg = $notif->is_read ? 'bg-white border-bottom border-secondary-subtle' : 'bg-light-gray border border-dark';
                            $iconBg = $notif->is_read ? 'border border-dark text-dark' : 'bg-black text-white';
                            $opacity = $notif->is_read ? 'opacity-75' : '';
                            $textColor = $notif->is_read ? 'text-secondary' : 'text-dark';

                            // 3. LOGIKA FORMAT WAKTU (Hari Ini, Kemarin, Tanggal Lengkap)
                            $time = \Carbon\Carbon::parse($notif->created_at);
                            $timeStr = strtoupper($time->translatedFormat('d M Y, H:i')); 
                            if ($time->isToday()) {
                                $timeStr = 'HARI INI, ' . $time->format('H:i');
                            } elseif ($time->isYesterday()) {
                                $timeStr = 'KEMARIN, ' . $time->format('H:i');
                            }
                        @endphp

                        <a href="#" class="text-decoration-none text-dark d-block p-4 mb-3 notification-item position-relative {{ $itemBg }}">
                            
                            {{-- Titik Hitam (Muncul cuma kalau belum dibaca) --}}
                            @if(!$notif->is_read)
                                <div class="position-absolute bg-dark" style="top: 15px; right: 15px; width: 10px; height: 10px;"></div>
                            @endif
                            
                            <div class="d-flex align-items-start {{ $opacity }}">
                                {{-- Ikon Box --}}
                                <div class="{{ $iconBg }} d-flex justify-content-center align-items-center me-3 me-md-4 flex-shrink-0" style="width: 55px; height: 55px;">
                                    <i class="bi {{ $icon }} fs-4"></i>
                                </div>
                                
                                {{-- Konten Teks --}}
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mb-2 pe-3">
                                        <h6 class="fw-bold text-uppercase mb-1 mb-sm-0 {{ $titleClass }}" style="font-size: 14px; letter-spacing: 0.5px;">
                                            {{ $notif->title }}
                                        </h6>
                                        <span class="text-secondary fw-bold" style="font-size: 11px;">{{ $timeStr }}</span>
                                    </div>
                                    <p class="mb-0 {{ $textColor }}" style="font-size: 13px; line-height: 1.6;">
                                        {{-- Trik regex biar nomor Invoice otomatis di-Bold --}}
                                        {!! preg_replace('/(#INV-[A-Z0-9-]+)/', '<span class="fw-bold">$1</span>', $notif->message) !!}
                                    </p>
                                </div>
                            </div>
                        </a>

                    @empty
                        {{-- TAMPILAN JIKA TIDAK ADA NOTIFIKASI --}}
                        <div class="text-center py-5 border border-secondary-subtle bg-light mt-4">
                            <i class="bi bi-bell-slash text-secondary opacity-50 display-1 mb-3 d-block"></i>
                            <h6 class="fw-bold text-uppercase">Belum Ada Notifikasi</h6>
                            <p class="text-secondary" style="font-size: 13px;">Anda akan mendapatkan pemberitahuan pesanan dan promo di sini.</p>
                        </div>
                    @endforelse

                    {{-- Tombol Muat Lebih Banyak (Hanya muncul jika ada notif) --}}
                    @if($notifications->count() > 0)
                        <div class="text-center mt-5">
                            <button class="btn btn-outline-dark rounded-0 px-5 py-2 fw-bold text-uppercase" style="font-size: 13px; letter-spacing: 1px;">
                                Muat Lebih Banyak
                            </button>
                        </div>
                    @endif

                </div>
            </div>
            
        </div>
    </section>

    @include('customer.components.footer')
    @include('customer.components.chatbot')
    
    @push('styles')
    <style>
        .bg-light-gray { background-color: #f8f9fa; }
    </style>
    @endpush
@endsection
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
                            <a href="{{ route('notification') }}" class="{{ !request('type') ? 'text-dark border-bottom border-dark border-2' : 'text-secondary hover-text-dark' }} text-decoration-none pb-1">Semua</a>
                            <a href="{{ route('notification', ['type' => 'transaksi']) }}" class="{{ request('type') == 'transaksi' ? 'text-dark border-bottom border-dark border-2' : 'text-secondary hover-text-dark' }} text-decoration-none pb-1">Transaksi</a>
                            <a href="{{ route('notification', ['type' => 'promo']) }}" class="{{ request('type') == 'promo' ? 'text-dark border-bottom border-dark border-2' : 'text-secondary hover-text-dark' }} text-decoration-none pb-1">Promo</a>
                        </div>
                        {{-- Tombol Tandai Dibaca --}}
                        <button type="button" id="mark-all-read-btn" class="btn btn-link p-0 text-secondary text-decoration-none text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 0.5px; border-bottom: 1px dashed #ccc;">
                            Tandai Semua Dibaca
                        </button>
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

                        <a href="#" data-id="{{ $notif->id }}" data-read="{{ $notif->is_read ? 'true' : 'false' }}" class="text-decoration-none text-dark d-block p-4 mb-3 notification-item position-relative {{ $itemBg }}">
                            
                            {{-- Titik Hitam (Muncul cuma kalau belum dibaca) --}}
                            @if(!$notif->is_read)
                                <div class="position-absolute bg-dark unread-dot" style="top: 15px; right: 15px; width: 10px; height: 10px;"></div>
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

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // 1. Single Notification Click Handler
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // Prevent default hash jump
                e.preventDefault();

                const isRead = this.getAttribute('data-read') === 'true';
                const notifId = this.getAttribute('data-id');

                if (!isRead) {
                    fetch(`/notification/${notifId}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update UI of this item to Read
                            this.setAttribute('data-read', 'true');
                            this.classList.remove('bg-light-gray', 'border', 'border-dark');
                            this.classList.add('bg-white', 'border-bottom', 'border-secondary-subtle');
                            
                            // Set opacity on the content
                            const dFlex = this.querySelector('.d-flex');
                            if (dFlex) dFlex.classList.add('opacity-75');

                            // Change text color from text-dark to text-secondary
                            const msgPara = this.querySelector('p');
                            if (msgPara) {
                                msgPara.classList.remove('text-dark');
                                msgPara.classList.add('text-secondary');
                            }

                            // Remove unread dot
                            const dot = this.querySelector('.unread-dot');
                            if (dot) dot.remove();

                            // Update navbar badge
                            updateNavbarBadge(data.unread_count);
                        }
                    })
                    .catch(err => console.error('Error marking notification as read:', err));
                }
            });
        });

        // 2. Mark All as Read Button Handler
        const markAllBtn = document.getElementById('mark-all-read-btn');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function(e) {
                e.preventDefault();

                fetch('/notification/read-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update all unread items
                        notificationItems.forEach(item => {
                            item.setAttribute('data-read', 'true');
                            item.classList.remove('bg-light-gray', 'border', 'border-dark');
                            item.classList.add('bg-white', 'border-bottom', 'border-secondary-subtle');
                            
                            const dFlex = item.querySelector('.d-flex');
                            if (dFlex) dFlex.classList.add('opacity-75');

                            const msgPara = item.querySelector('p');
                            if (msgPara) {
                                msgPara.classList.remove('text-dark');
                                msgPara.classList.add('text-secondary');
                            }

                            const dot = item.querySelector('.unread-dot');
                            if (dot) dot.remove();
                        });

                        // Update navbar badge
                        updateNavbarBadge(0);
                    }
                })
                .catch(err => console.error('Error marking all notifications as read:', err));
            });
        }

        // Helper to update navbar badge
        function updateNavbarBadge(count) {
            const badge = document.getElementById('notif-badge-count');
            if (badge) {
                if (count > 0) {
                    badge.innerText = count > 99 ? '99+' : count;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            }
        }
    });
    </script>
    @endpush
@endsection
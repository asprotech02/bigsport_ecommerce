@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-4 py-lg-5 bg-white text-dark" style="min-height: 70vh;">
        <div class="container" style="max-width: 600px;">

            <h4 class="fw-bold text-uppercase mb-4" style="letter-spacing: 1px;">Lacak Pesanan</h4>

            {{-- 1. Kartu Informasi Resi --}}
            <div class="card rounded-0 border-dark mb-4 shadow-sm bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 1px;">No. Pesanan</span>
                        <span class="fw-bold" style="font-size: 13px;">#{{ $order->invoice_number }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary text-uppercase fw-bold" style="font-size: 11px; letter-spacing: 1px;">Kurir Pengiriman</span>
                        <span class="fw-bold text-uppercase" style="font-size: 13px;">
                            {{ $order->shippingDetail->courier_company ?? 'Kurir' }} - {{ $order->shippingDetail->courier_type ?? 'Reguler' }}
                        </span>
                    </div>

                    <div class="p-3 bg-light border border-secondary-subtle">
                        <span class="text-secondary d-block text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 1px;">Nomor Resi</span>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold fs-5">{{ $order->shippingDetail->tracking_number ?? '-' }}</span>
                            @if($order->shippingDetail && $order->shippingDetail->tracking_number)
                                <button onclick="copyToClipboard('{{ $order->shippingDetail->tracking_number }}')" class="btn btn-sm btn-outline-dark rounded-0 px-3 py-1" style="font-size: 11px; font-weight: bold;">
                                    SALIN
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Area Timeline Pelacakan --}}
            <h6 class="fw-bold text-uppercase mb-4" style="letter-spacing: 1px; font-size: 14px;">Riwayat Perjalanan</h6>

            @if($errorMessage)
                <div class="alert alert-warning rounded-0 border-dark text-dark fw-bold small text-center p-4">
                    <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
                    {{ $errorMessage }}
                </div>
            @elseif($trackingData && !empty($trackingData['history']))
                <div class="tracking-timeline-container p-3">
                    @foreach($trackingData['history'] as $index => $history)
                        @php
                            $isLatest = $index === 0;
                            $timeFormatted = \Carbon\Carbon::parse($history['updated_at'])->locale('id')->translatedFormat('d F Y, HH:i') . ' WIB';
                        @endphp
                        <div class="timeline-item {{ $isLatest ? 'active' : '' }}">
                            <div class="timeline-date text-secondary">{{ $timeFormatted }}</div>
                            <h6 class="timeline-title fw-bold text-uppercase">{{ $history['status'] }}</h6>
                            <p class="timeline-desc text-secondary">{{ $history['note'] }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 border border-secondary-subtle">
                    <i class="bi bi-box-seam text-secondary fs-1 d-block mb-2"></i>
                    <p class="text-secondary small fw-bold text-uppercase m-0">Menunggu pembaruan dari pihak ekspedisi.</p>
                </div>
            @endif

        </div>
    </section>

    @include('customer.components.footer')

    @push('styles')
    <style>
        /* Desain Timeline Monokrom */
        .tracking-timeline-container {
            border-left: 2px solid #e9ecef;
            margin-left: 20px;
            padding-left: 25px;
            position: relative;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        /* Bulatan penanda timeline */
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -33px; /* Sesuaikan dengan margin-left container */
            top: 2px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: #ffffff;
            border: 3px solid #ced4da;
            z-index: 2;
        }

        /* Status terbaru (paling atas) berwarna Hitam */
        .timeline-item.active::before {
            background-color: #000000;
            border-color: #000000;
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.1);
        }

        .timeline-item.active .timeline-title {
            color: #000000;
        }

        .timeline-item.active .timeline-desc {
            color: #333333;
            font-weight: 600;
        }

        .timeline-title {
            font-size: 13px;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            color: #6c757d;
        }

        .timeline-desc {
            font-size: 13px;
            margin-bottom: 0;
            line-height: 1.5;
        }

        .timeline-date {
            font-size: 11px;
            margin-bottom: 4px;
            font-weight: bold;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                Toastify({
                    text: "Resi berhasil disalin!",
                    duration: 3000,
                    gravity: "top", 
                    position: "center", 
                    style: { background: "#000", color: "#fff", fontSize: "12px", borderRadius: "0px" }
                }).showToast();
            }, function(err) {
                console.error('Gagal menyalin text: ', err);
            });
        }
    </script>
    @endpush
@endsection
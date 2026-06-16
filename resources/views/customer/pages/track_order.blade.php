@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    @php
        // Ekstraksi alamat bersih dari format snapshot order (biasanya "Nama | Alamat")
        $addressParts = explode(' | ', $order->shipping_address);
        $cleanAddress = $addressParts[1] ?? $order->shipping_address;
        
        // Bersihkan data alamat dari karakter aneh untuk geocoding
        $cleanAddressForGeocode = trim(str_replace(['"', "'", "\r", "\n"], ' ', $cleanAddress));
    @endphp

    <section class="py-4 py-lg-5 bg-white text-dark" style="min-height: 70vh;">
        <div class="container" style="max-width: 600px;">

            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold text-uppercase m-0" style="letter-spacing: 1px;">Lacak Pesanan</h4>
                <a href="{{ route('order.detail', $order->id) }}" class="btn btn-sm btn-outline-dark rounded-0 px-3 fw-bold" style="font-size: 11px;">KEMBALI</a>
            </div>

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

            {{-- Peta Pelacakan Pengiriman Premium --}}
            <h6 class="fw-bold text-uppercase mb-3" style="letter-spacing: 1px; font-size: 14px;">Peta Rute Pengiriman</h6>
            <div class="card rounded-0 border-dark mb-4 shadow-sm bg-white">
                <div class="card-body p-0 position-relative" style="height: 350px;">
                    <div id="map-loading" class="position-absolute top-50 start-50 translate-middle text-center w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-white" style="z-index: 999; opacity: 0.95;">
                        <div class="spinner-border spinner-border-sm text-dark mb-2"></div>
                        <span class="fw-bold small text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;" id="loading-text">Menghubungkan Koordinat...</span>
                    </div>
                    <div id="map" style="height: 100%; width: 100%;"></div>
                </div>
                <div class="card-footer bg-light border-top border-dark p-3">
                    <div class="d-flex align-items-start gap-2 small text-secondary">
                        <i class="bi bi-info-circle-fill text-dark mt-0.5"></i>
                        <span id="map-status-text">Menginisialisasi titik rute dari Toko Bagindo Jaya ke alamat Anda...</span>
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
    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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

        /* Premium Leaflet Popup and Markers Styles */
        .leaflet-popup-content-wrapper {
            border-radius: 0px !important;
            border: 1px solid #000 !important;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15) !important;
        }
        .leaflet-popup-content {
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 8px 12px !important;
            color: #000;
        }
        .leaflet-popup-tip {
            border: 1px solid #000;
            background: #fff;
        }
        .truck-marker-icon {
            transition: all 0.1s linear;
        }
        /* Custom map markers */
        .custom-pin {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #000;
            color: #fff;
            font-size: 16px;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        .custom-pin-user {
            background: #28a745;
        }
    </style>
    @endpush

    @push('scripts')
    <!-- Leaflet.js Library -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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

        // ========================================================
        // INTEGRASI PETA INTERAKTIF LASELET.JS & OPENSTREETMAP
        // ========================================================
        function initMap() {
            // Sembunyikan Loading Instan karena Koordinat Sudah Dihitung di Sisi Server (PHP)
            const loader = document.getElementById('map-loading');
            if (loader) {
                loader.classList.remove('d-flex');
                loader.classList.add('d-none');
            }

            try {
                if (typeof L === 'undefined') {
                    throw new Error("Leaflet.js library is not loaded.");
                }

                // 1. Titik Asal (Store Bagindo Jaya Larangan)
                const originLat = -6.2263;
                const originLng = 106.7291;
                const originName = "Bagindo Jaya Store (Larangan)";

                // 2. Alamat User & Koordinat dari Server
                const destLat = parseFloat({!! json_encode($destLat ?? -6.1783) !!});
                const destLng = parseFloat({!! json_encode($destLng ?? 106.6319) !!});
                const orderStatus = {!! json_encode(strtolower($order->status)) !!};
                const invoiceNumber = {!! json_encode($order->invoice_number) !!};

                // Inisialisasi Peta Leaflet (Default center ke Toko)
                const map = L.map('map', {
                    zoomControl: true,
                    scrollWheelZoom: false
                }).setView([originLat, originLng], 12);

                // Gunakan CartoDB Positron Theme (Monokrom/Greyscale Premium)
                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    subdomains: 'abcd',
                    maxZoom: 20
                }).addTo(map);

                // Tambahkan Marker Toko
                const storeIcon = L.divIcon({
                    html: '<div class="custom-pin"><i class="bi bi-shop"></i></div>',
                    className: 'custom-div-icon',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });

                const storeMarker = L.marker([originLat, originLng], { icon: storeIcon }).addTo(map);
                storeMarker.bindPopup(`<strong class="text-uppercase">${originName}</strong><br><span class="text-secondary small" style="font-size:9px; text-transform:none;">Origin Warehouse</span>`).openPopup();

                // Tambahkan Marker Rumah User
                const userIcon = L.divIcon({
                    html: '<div class="custom-pin custom-pin-user"><i class="bi bi-house-door-fill"></i></div>',
                    className: 'custom-div-icon',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });

                const userMarker = L.marker([destLat, destLng], { icon: userIcon }).addTo(map);
                userMarker.bindPopup(`<strong>ALAMAT PENGIRIMAN ANDA</strong>`);

                // Fit Bounds agar kedua titik terlihat di layar
                const bounds = L.latLngBounds([[originLat, originLng], [destLat, destLng]]);
                map.fitBounds(bounds, { padding: [50, 50] });

                // Menggambar Jalur Pengiriman (Dotted/Dashed Line untuk Style Premium)
                const routePath = L.polyline([[originLat, originLng], [destLat, destLng]], {
                    color: '#000000',
                    weight: 3,
                    opacity: 0.5,
                    dashArray: '8, 12'
                }).addTo(map);

                // Tentukan arah hadap truk secara dinamis berdasarkan bujur (longitude) rute pengiriman
                const isGoingWest = destLng < originLng;

                // Helper untuk membuat ikon truk dengan arah hadap tertentu secara dinamis
                function getTruckIcon(isMovingToDestination) {
                    const isFacingWest = isMovingToDestination ? isGoingWest : !isGoingWest;
                    const transformValue = isFacingWest ? "" : "transform: scaleX(-1);";
                    
                    return L.divIcon({
                        html: `<div style="font-size: 28px; line-height: 1; filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.35)); ${transformValue}">🚚</div>`,
                        className: 'truck-marker-icon',
                        iconSize: [30, 30],
                        iconAnchor: [15, 15]
                    });
                }

                // Tambahkan marker truk dengan arah hadap awal menuju destinasi
                const truckMarker = L.marker([originLat, originLng], { icon: getTruckIcon(true) }).addTo(map);

                // Helper untuk membalikkan arah ikon truk secara dinamis (WOW Effect!) menggunakan API Leaflet setIcon
                function updateTruckFacing(isMovingToDestination) {
                    truckMarker.setIcon(getTruckIcon(isMovingToDestination));
                }

                // Mengatur Status Pelacakan dan Animasi Berdasarkan Status Biteship
                let statusText = "";
                let fraction = 0;
                let direction = 1;

                // Helper Fungsi Interpolasi Koordinat
                function interpolate(start, end, frac) {
                    const lat = start.lat + (end.lat - start.lat) * frac;
                    const lng = start.lng + (end.lng - start.lng) * frac;
                    return [lat, lng];
                }

                if (orderStatus === 'completed' || orderStatus === 'delivered') {
                    statusText = "Paket telah sampai di tujuan Anda dengan selamat! Terima kasih.";
                    document.getElementById('map-status-text').innerText = statusText;

                    // Pastikan menghadap ke tujuan
                    setTimeout(() => updateTruckFacing(true), 200);

                    // Animasi truk berjalan sekali dari toko ke rumah, lalu menetap
                    let animFraction = 0;
                    const interval = setInterval(() => {
                        animFraction += 0.02;
                        if (animFraction >= 1) {
                            animFraction = 1;
                            clearInterval(interval);
                            
                            // Letakkan marker checkmark sukses di rumah
                            L.circle([destLat, destLng], {
                                color: '#28a745',
                                fillColor: '#28a745',
                                fillOpacity: 0.15,
                                radius: 250
                            }).addTo(map);
                            
                            userMarker.openPopup();
                        }
                        truckMarker.setLatLng(interpolate(
                            { lat: originLat, lng: originLng },
                            { lat: destLat, lng: destLng },
                            animFraction
                        ));
                    }, 40);

                } else if (orderStatus === 'shipped' || orderStatus === 'processing') {
                    statusText = "Pesanan sedang dalam proses pengiriman kurir. Lacak perjalanan di atas!";
                    document.getElementById('map-status-text').innerText = statusText;

                    // Pastikan menghadap ke tujuan saat awal berjalan
                    setTimeout(() => updateTruckFacing(true), 200);

                    // Truk terus bergerak bolak-balik untuk melambangkan pengiriman aktif (WOW Effect!)
                    setInterval(() => {
                        fraction += 0.004 * direction;
                        if (fraction >= 1) {
                            fraction = 1;
                            direction = -1; // Berbalik arah
                            updateTruckFacing(false); // Balik hadap gudang asal
                        } else if (fraction <= 0) {
                            fraction = 0;
                            direction = 1; // Kembali maju
                            updateTruckFacing(true); // Balik hadap rumah tujuan
                        }
                        truckMarker.setLatLng(interpolate(
                            { lat: originLat, lng: originLng },
                            { lat: destLat, lng: destLng },
                            fraction
                        ));
                    }, 45);

                } else {
                    statusText = "Pesanan sedang disiapkan di gudang kami dan bersiap untuk dikirim.";
                    document.getElementById('map-status-text').innerText = statusText;
                    
                    // Truk parkir di gudang (Toko)
                    truckMarker.setLatLng([originLat, originLng]);
                }
            } catch (err) {
                console.error("Map rendering error:", err);
                const loader = document.getElementById('map-loading');
                if (loader) {
                    loader.classList.remove('d-flex');
                    loader.classList.add('d-none');
                }
                document.getElementById('map-status-text').innerText = "Gagal memuat peta interaktif secara lengkap. Anda tetap dapat melacak riwayat kurir di bawah.";
            }
        }

        // Jalankan inisialisasi peta secara aman mengantisipasi document readyState
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", initMap);
        } else {
            initMap();
        }
    </script>
    @endpush
@endsection
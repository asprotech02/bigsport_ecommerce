@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-4 py-lg-5 bg-white" style="min-height: 70vh;">
        <div class="container">
            <div class="row g-4 g-lg-5">
                
                {{-- 1. SIDEBAR NAV TABS --}}
                <div class="col-12 col-lg-3">
                    <div class="profile-sidebar border pb-2 nav flex-column" role="tablist" aria-orientation="vertical">
                        <a class="sidebar-link active" id="tab-akun" data-bs-toggle="tab" href="#content-akun" role="tab" style="cursor: pointer;">Akun Saya</a>
                        <a class="sidebar-link" id="tab-alamat" data-bs-toggle="tab" href="#content-alamat" role="tab" style="cursor: pointer;">Alamat</a>
                        <div class="sidebar-divider"></div>
                        <a class="sidebar-link" id="tab-pesanan" data-bs-toggle="tab" href="#content-pesanan" role="tab" style="cursor: pointer;">Pesanan</a>
                        <a class="sidebar-link" id="tab-lacak" data-bs-toggle="tab" href="#content-lacak" role="tab" style="cursor: pointer;">Lacak Pesanan</a>
                        <div class="sidebar-divider"></div>
                        <a class="sidebar-link" id="tab-kontak" data-bs-toggle="tab" href="#content-kontak" role="tab" style="cursor: pointer;">Kontak Kami</a>
                        <a class="sidebar-link" id="tab-lokasi" data-bs-toggle="tab" href="#content-lokasi" role="tab" style="cursor: pointer;">Lokasi Toko</a>
                    </div>
                </div>

                {{-- 2. KONTEN TAB PANES --}}
                <div class="col-12 col-lg-9 ps-lg-5">
                    <div class="tab-content" id="profileTabContent">
                        
                        {{-- TAB: AKUN SAYA --}}
                        <div class="tab-pane fade show active" id="content-akun" role="tabpanel">
                            <h3 class="fw-bold mb-5 text-uppercase" style="letter-spacing: 0.5px;">INFORMASI PENGGUNA</h3>
                            <div class="mb-5">
                                <h6 class="fw-bold mb-3 text-dark">Detail Pengguna</h6>
                                <div class="profile-info-text mb-2">{{ $user->name ?? 'Belum diatur' }}</div>
                                <div class="profile-info-text mb-2">
                                    {{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->translatedFormat('d F Y') : 'Tanggal lahir belum diatur' }}
                                </div>
                                <div class="profile-info-text mb-4">
                                    {{ $user->gender == 'L' ? 'Laki-laki' : ($user->gender == 'P' ? 'Perempuan' : 'Jenis kelamin belum diatur') }}
                                </div>
                                <a href="{{ route('profile_edit') }}" class="btn btn-dark btn-sm px-4 py-2">Edit</a>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-3 text-dark">Detail Login</h6>
                                <div class="mb-4">
                                    <div class="profile-info-text mb-2">{{ $user->email }}</div>
                                    <a href="{{ route('email_edit') }}" class="btn btn-dark btn-sm px-4 py-2">Edit Email</a>
                                </div>
                                <div class="mb-2">
                                    <div class="profile-info-text mb-2">*************</div>
                                    <a href="{{ route('password_edit') }}" class="btn btn-dark btn-sm px-4 py-2">Edit Password</a>
                                </div>
                            </div>
                        </div>

                        {{-- TAB: ALAMAT --}}
                        <div class="tab-pane fade" id="content-alamat" role="tabpanel">
                            <div class="mb-5">
                                <h3 class="fw-bold text-uppercase m-0" style="letter-spacing: 0.5px;">Informasi Alamat</h3>
                            </div>
                            <div id="address-list">
                                @forelse($addresses as $addr)
                                    <div class="address-card border p-4 mb-4 position-relative {{ $addr->is_default ? 'border-dark' : 'border-secondary-subtle' }}" 
                                         id="address-card-{{ $addr->id }}" data-id="{{ $addr->id }}"
                                         style="{{ $addr->is_default ? 'border-width: 2px;' : '' }}">
                                        
                                        <div class="address-content">
                                            @if($addr->is_default)
                                                <span class="badge bg-dark text-white rounded-0 position-absolute top-0 end-0 px-3 py-2 badge-utama" style="font-size: 10px; letter-spacing: 1px;">UTAMA</span>
                                            @endif
                                            <h6 class="fw-bold text-dark mb-2 text-uppercase">{{ $addr->receiver_name }}</h6>
                                            <p class="text-secondary mb-1" style="font-size: 13px;">{{ $addr->receiver_phone }}</p>
                                            <p class="profile-info-text mb-3" style="line-height: 1.6; max-width: 550px; font-size: 14px; color: #444;">
                                                {{ $addr->full_address }}, {{ $addr->village_name }}, {{ $addr->district_name }}, {{ $addr->city_name }}, {{ $addr->province_name }}, {{ $addr->postal_code }}
                                            </p>
                                        </div>

                                        <div class="d-flex gap-3 align-items-center">
                                            <a href="{{ route('address.edit', $addr->id) }}" class="text-dark fw-bold text-decoration-none" style="font-size: 12px; letter-spacing: 0.5px;">
                                                <i class="bi bi-pencil-square me-1"></i> EDIT
                                            </a>
                                            <div class="action-buttons d-flex gap-3 align-items-center">
                                                @if(!$addr->is_default)
                                                    <div class="vr" style="height: 15px; width: 1px;"></div>
                                                    <button onclick="setMainAddress({{ $addr->id }})" class="bg-transparent border-0 p-0 text-dark fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">SET UTAMA</button>
                                                    <div class="vr" style="height: 15px; width: 1px;"></div>
                                                    <button onclick="deleteAddress({{ $addr->id }})" class="bg-transparent border-0 p-0 text-danger fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">HAPUS</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div id="empty-address" class="text-center py-5 border border-dashed mb-4">
                                        <p class="text-secondary m-0">Anda belum memiliki daftar alamat</p>
                                    </div>
                                @endforelse
                            </div>
                            <div class="mt-5">
                                <a href="{{ route('address_form') }}" class="btn btn-dark w-100 rounded-0 py-3 fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 14px;">
                                    Tambah Alamat Baru
                                </a>
                            </div>
                        </div>

                        {{-- TAB: PESANAN SAYA --}}
                        <div class="tab-pane fade" id="content-pesanan" role="tabpanel">
                            <div class="mb-4">
                                <h3 class="fw-bold text-uppercase m-0" style="font-size: 24px; letter-spacing: 1px;">Pesanan Saya</h3>
                            </div>

                            <ul class="nav border-bottom border-secondary-subtle mb-4 gap-3 gap-md-4 d-flex flex-nowrap overflow-x-auto thumbnail-scroll" id="orderTabs" role="tablist">
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 active text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#semua" type="button">Semua</button></li>
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#belum-bayar" type="button">Belum Bayar</button></li>
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#diproses" type="button">Diproses</button></li>
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#dikirim" type="button">Dikirim</button></li>
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#selesai" type="button">Selesai</button></li>
                                <li class="nav-item"><button class="nav-link nav-tab-custom fw-bold fs-6 pb-3 px-1 text-dark opacity-75 hover-opacity-100 text-nowrap" data-bs-toggle="tab" data-bs-target="#dibatalkan" type="button">Dibatalkan</button></li>
                            </ul>

                            @php
                                $orderList = $orders ?? collect();

                                function getCustomerStatus($order) {
                                    if ($order->status == 'cancelled' || in_array($order->payment_status, ['failed', 'expired', 'refunded'])) {
                                        return ['label' => 'Dibatalkan', 'class' => 'text-danger'];
                                    }
                                    if (in_array($order->payment_status, ['unpaid', 'pending'])) {
                                        return ['label' => 'Menunggu Pembayaran', 'class' => 'text-warning'];
                                    }
                                    // Jika sudah bayar
                                    if ($order->payment_status == 'paid') {
                                        // Jika pesanan sudah selesai
                                        if ($order->status == 'completed') {
                                            return ['label' => 'Selesai', 'class' => 'text-dark'];
                                        }
                                        // Jika resi sudah diinput (shippingDetail memiliki tracking_number)
                                        if ($order->shippingDetail && !empty($order->shippingDetail->tracking_number)) {
                                            return ['label' => 'Sedang Dikirim', 'class' => 'text-success'];
                                        }
                                        // Jika resi belum diinput
                                        return ['label' => 'Diproses', 'class' => 'text-primary'];
                                    }
                                    return ['label' => 'Diproses', 'class' => 'text-primary'];
                                }

                                $tabData = [
                                    'semua' => ['data' => $orderList, 'empty' => 'Belum ada pesanan'],
                                    'belum-bayar' => ['data' => $orderList->filter(function($o) {
                                        return in_array($o->payment_status, ['unpaid', 'pending']) && $o->status != 'cancelled';
                                    }), 'empty' => 'Tidak ada pesanan yang belum dibayar'],
                                    'diproses' => ['data' => $orderList->filter(function($o) {
                                        return $o->payment_status == 'paid' && $o->status != 'cancelled' && $o->status != 'completed' && (!$o->shippingDetail || empty($o->shippingDetail->tracking_number));
                                    }), 'empty' => 'Tidak ada pesanan yang sedang diproses'],
                                    'dikirim' => ['data' => $orderList->filter(function($o) {
                                        return $o->payment_status == 'paid' && $o->status != 'cancelled' && $o->status != 'completed' && $o->shippingDetail && !empty($o->shippingDetail->tracking_number);
                                    }), 'empty' => 'Tidak ada pesanan yang sedang dikirim'],
                                    'selesai' => ['data' => $orderList->where('status', 'completed'), 'empty' => 'Belum ada pesanan yang selesai'],
                                    'dibatalkan' => ['data' => $orderList->filter(function($o) {
                                        return $o->status == 'cancelled' || in_array($o->payment_status, ['failed', 'expired']);
                                    }), 'empty' => 'Tidak ada pesanan yang dibatalkan'],
                                ];
                            @endphp

                            <div class="tab-content" id="orderTabsContent">
                                @foreach($tabData as $tabId => $tabInfo)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel">
                                        @forelse($tabInfo['data'] as $order)
                                            @php
                                                $statusData = getCustomerStatus($order);
                                                $isPickup = $order->shippingDetail && $order->shippingDetail->courier_company === 'pickup';
                                            @endphp

                                            <div class="card rounded-0 mb-4 border-secondary-subtle shadow-sm">
                                                
                                                <div class="card-header bg-light border-bottom border-secondary-subtle py-2 px-3 d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="bi bi-shop fs-5"></i>
                                                        <span class="fw-bold text-uppercase" style="font-size: 13px;">Bagindo Jaya Tangerang</span>
                                                        @if($isPickup)
                                                            <span class="badge border border-dark text-dark rounded-0 px-2 py-1 ms-sm-2" style="font-size: 10px;"><i class="bi bi-shop-window me-1"></i> AMBIL DI TOKO</span>
                                                        @else
                                                            @if($order->status == 'cancelled' || in_array($order->payment_status, ['failed', 'expired', 'refunded']))
                                                                <span class="badge border border-danger text-danger rounded-0 px-2 py-1 ms-sm-2" style="font-size: 10px;"><i class="bi bi-x-circle me-1"></i> DIBATALKAN</span>
                                                            @elseif(in_array($order->payment_status, ['unpaid', 'pending']))
                                                                <span class="badge border border-dark text-dark rounded-0 px-2 py-1 ms-sm-2" style="font-size: 10px;"><i class="bi bi-clock me-1"></i> MENUNGGU PEMBAYARAN</span>
                                                            @elseif($order->payment_status == 'paid' && in_array($order->status, ['pending', 'confirmed', 'preparing', 'processing']))
                                                                <span class="badge border border-dark text-dark rounded-0 px-2 py-1 ms-sm-2" style="font-size: 10px;"><i class="bi bi-gear me-1"></i> DIPROSES</span>
                                                            @elseif(in_array($order->status, ['shipped', 'delivered']))
                                                                <span class="badge border border-dark text-dark rounded-0 px-2 py-1 ms-sm-2" style="font-size: 10px;"><i class="bi bi-truck me-1"></i> DIKIRIM</span>
                                                            @elseif($order->status == 'completed')
                                                                <span class="badge border border-dark text-dark rounded-0 px-2 py-1 ms-sm-2" style="font-size: 10px;"><i class="bi bi-check-circle me-1"></i> SELESAI</span>
                                                            @else
                                                                <span class="badge border border-dark text-dark rounded-0 px-2 py-1 ms-sm-2" style="font-size: 10px;"><i class="bi bi-info-circle me-1"></i> {{ strtoupper($order->status) }}</span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    <span class="fw-bold text-uppercase {{ $statusData['class'] }}" style="font-size: 12px; letter-spacing: 0.5px;">{{ $statusData['label'] }}</span>
                                                </div>

                                                <div class="card-body p-3">
                                                    @php
                                                        $firstItem = $order->items->first();
                                                        $otherItems = $order->items->skip(1);
                                                        $otherItemsCount = $otherItems->count();
                                                        
                                                        $getImage = function($item) {
                                                            if ($item && $item->sku && $item->sku->product && $item->sku->product->images->isNotEmpty()) {
                                                                $primaryImg = $item->sku->product->images->where('is_primary', true)->first() ?? $item->sku->product->images->first();
                                                                if ($primaryImg) return 'storage/' . $primaryImg->image_path;
                                                            }
                                                            return null;
                                                        };
                                                        $firstImagePath = $getImage($firstItem);
                                                    @endphp
                                                    
                                                    @if($firstItem)
                                                    <div class="d-flex align-items-start">
                                                        <div class="ratio ratio-1x1 border border-secondary-subtle flex-shrink-0 bg-light me-3" style="width: 80px;">
                                                            @if($firstImagePath) 
                                                                <img src="{{ asset($firstImagePath) }}" class="object-fit-cover w-100 h-100" alt="{{ $firstItem->product_name }}">
                                                            @else 
                                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary bg-light"><i class="bi bi-image text-muted fs-4"></i></div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <h6 class="fw-bold mb-1 pe-3" style="font-size: 15px;">{{ $firstItem->product_name }}</h6>
                                                                <span class="fw-bold text-nowrap" style="font-size: 14px;">Rp {{ number_format($firstItem->price_at_purchase, 0, ',', '.') }}</span>
                                                            </div>
                                                            <p class="text-secondary mb-1" style="font-size: 13px;">Variasi: {{ $firstItem->product_size ?? '-' }}</p>
                                                            <p class="text-secondary mb-0" style="font-size: 13px;">x{{ $firstItem->quantity }}</p>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if($otherItemsCount > 0)
                                                        <div class="collapse" id="collapseOrder{{ $order->id }}">
                                                            @foreach($otherItems as $item)
                                                                @php $imagePath = $getImage($item); @endphp
                                                                <div class="d-flex align-items-start mt-3 pt-3 border-top border-secondary-subtle">
                                                                    <div class="ratio ratio-1x1 border border-secondary-subtle flex-shrink-0 bg-light me-3" style="width: 80px;">
                                                                        @if($imagePath) 
                                                                            <img src="{{ asset($imagePath) }}" class="object-fit-cover w-100 h-100" alt="{{ $item->product_name }}">
                                                                        @else 
                                                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-secondary bg-light"><i class="bi bi-image text-muted fs-4"></i></div>
                                                                        @endif
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div class="d-flex justify-content-between align-items-start">
                                                                            <h6 class="fw-bold mb-1 pe-3" style="font-size: 15px;">{{ $item->product_name }}</h6>
                                                                            <span class="fw-bold text-nowrap" style="font-size: 14px;">Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</span>
                                                                        </div>
                                                                        <p class="text-secondary mb-1" style="font-size: 13px;">Variasi: {{ $item->product_size ?? '-' }}</p>
                                                                        <p class="text-secondary mb-0" style="font-size: 13px;">x{{ $item->quantity }}</p>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <div class="mt-3 pt-2 border-top border-light text-secondary text-center" 
                                                             style="font-size: 12px; font-weight: 600; cursor: pointer;" 
                                                             data-bs-toggle="collapse" 
                                                             data-bs-target="#collapseOrder{{ $order->id }}" 
                                                             aria-expanded="false" 
                                                             onclick="toggleOrderItems(this, {{ $otherItemsCount }})">
                                                            <span class="toggle-text">Tampilkan {{ $otherItemsCount }} produk lainnya</span> 
                                                            <i class="bi bi-chevron-down ms-1 toggle-icon"></i>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="card-footer bg-white border-top border-secondary-subtle p-3">
                                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                                        
                                                        <div class="text-start">
                                                            <div class="text-secondary" style="font-size: 12px;">No. Pesanan: <strong class="text-dark">#{{ $order->invoice_number }}</strong></div>
                                                            <div class="text-secondary" style="font-size: 12px; margin-top: 2px;">Total Belanja: <strong class="text-dark">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</strong></div>
                                                        </div>
                                                        
                                                        <div class="d-flex flex-wrap justify-content-md-end gap-2">

                                                            {{-- 🌟 TOMBOL YANG TERSISA HANYA INI --}}
                                                            <a href="{{ route('order.detail', $order->id) }}" class="btn btn-outline-dark fw-bold text-uppercase rounded-0" style="font-size: 11px; padding: 8px 15px;">Detail Pesanan</a>

                                                            @if(!in_array($order->status, ['shipped', 'delivered', 'completed', 'cancelled']) && (!$order->shippingDetail || empty($order->shippingDetail->tracking_number)))
                                                                <button type="button" onclick="cancelOrder({{ $order->id }})" class="btn btn-outline-danger fw-bold text-uppercase rounded-0" style="font-size: 11px; padding: 8px 15px;">Batalkan Pesanan</button>
                                                            @endif

                                                            @if(in_array($order->payment_status, ['unpaid', 'pending']) && $order->status === 'pending')
                                                                <button type="button" class="btn btn-dark fw-bold text-uppercase btn-lanjut-bayar rounded-0" style="font-size: 11px; padding: 8px 15px;" data-token="{{ $order->snap_token }}">Bayar Sekarang</button>
                                                            @endif

                                                             @if($order->payment_status == 'paid' && $order->status != 'cancelled' && $order->status != 'completed' && (($order->shippingDetail && !empty($order->shippingDetail->tracking_number)) || $order->status == 'processing' || $order->status == 'shipped' || $order->status == 'delivered'))
                                                                 <button type="button" onclick="submitOrderReceived({{ $order->id }})" id="btn-complete-{{ $order->id }}" class="btn btn-dark fw-bold text-uppercase rounded-0" style="font-size: 11px; padding: 8px 15px;">Pesanan Diterima</button>
                                                             @endif

                                                            @php
                                                                // Ambil semua ID Item Pesanan dari pesanan ini
                                                                $itemIds = $order->items->pluck('id')->toArray();
                                                                
                                                                // Cek apakah user sudah mengulas item di pesanan ini
                                                                $hasReviewed = \Illuminate\Support\Facades\DB::table('reviews')
                                                                                ->where('user_id', auth()->id())
                                                                                ->whereIn('order_item_id', $itemIds)
                                                                                ->exists();
                                                            @endphp

                                                           @if($order->status == 'completed')
    @if(!$hasReviewed)
        {{-- Ganti <a> menjadi <button> agar memicu modal --}}
        <button type="button" 
                onclick="openReviewModal({{ $order->id }}, {{ json_encode($order->items->map(fn($item) => ['id' => $item->id, 'product_name' => $item->product_name, 'product_size' => $item->product_size])) }})" 
                class="btn btn-dark fw-bold text-uppercase rounded-0" 
                style="font-size: 11px; padding: 8px 15px;">
            Beri Ulasan
        </button>
    @else
        {{-- Jika SUDAH diulas, tetap tombol link Beli Lagi --}}
        <a href="{{ route('order.buy-again', $order->id) }}" class="btn btn-dark fw-bold text-uppercase rounded-0" style="font-size: 11px; padding: 8px 15px;">Beli Lagi</a>
    @endif
@endif

                                                            {{-- Tombol Beli Lagi juga biasanya selalu muncul untuk pesanan yang Dibatalkan/Gagal --}}
                                                            @if($order->status == 'cancelled' || in_array($order->payment_status, ['failed', 'expired']))
                                                                <a href="{{ route('order.buy-again', $order->id) }}" class="btn btn-outline-dark fw-bold text-uppercase rounded-0" style="font-size: 11px; padding: 8px 15px;">Beli Lagi</a>
                                                            @endif

                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        @empty
                                            <div class="text-center py-5 bg-white border border-secondary-subtle">
                                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                    <i class="bi bi-bag-x display-5 text-secondary opacity-50"></i>
                                                </div>
                                                <h6 class="fw-bold text-uppercase mb-2">Tidak Ada Data</h6>
                                                <p class="text-secondary mb-4" style="font-size: 14px;">{{ $tabInfo['empty'] }}</p>
                                                <a href="{{ route('product.index') }}" class="btn btn-dark rounded-0 fw-bold px-4 py-2 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Mulai Belanja</a>
                                            </div>
                                        @endforelse
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- TAB: LACAK PESANAN --}}
                        <div class="tab-pane fade" id="content-lacak" role="tabpanel">
                            <div class="mb-4">
                                <h3 class="fw-bold text-uppercase m-0" style="font-size: 24px; letter-spacing: 1px;">Lacak Pesanan</h3>
                                <p class="text-secondary mt-1" style="font-size: 13px;">Daftar pesanan Anda yang sedang dalam proses pengiriman kurir.</p>
                            </div>

                            @php
                                $lacakOrders = $orders->filter(function($o) {
                                    return $o->payment_status == 'paid' && in_array($o->status, ['processing', 'shipped']);
                                });
                            @endphp

                            <div class="lacak-orders-list">
                                @forelse($lacakOrders as $order)
                                    @php
                                        $isPickup = $order->shippingDetail && $order->shippingDetail->courier_company === 'pickup';
                                    @endphp
                                    <div class="card rounded-0 mb-4 border-dark shadow-sm">
                                        <div class="card-header bg-light border-bottom border-dark py-3 px-3 d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-box-seam fs-5 text-dark"></i>
                                                <span class="fw-bold text-uppercase" style="font-size: 13px;">#{{ $order->invoice_number }}</span>
                                            </div>
                                            <span class="badge bg-dark text-white rounded-0 px-2 py-1" style="font-size: 10px; letter-spacing: 0.5px;">DALAM PENGIRIMAN</span>
                                        </div>

                                        <div class="card-body p-4">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8">
                                                    <div class="mb-2">
                                                        <span class="text-secondary d-block" style="font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Kurir</span>
                                                        <span class="fw-bold text-uppercase" style="font-size: 14px;">
                                                            {{ $order->shippingDetail->courier_company ?? 'Kurir' }} - {{ $order->shippingDetail->courier_type ?? 'Reguler' }}
                                                        </span>
                                                    </div>
                                                    @if($order->shippingDetail && $order->shippingDetail->tracking_number)
                                                        <div class="mb-2">
                                                            <span class="text-secondary d-block" style="font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Nomor Resi</span>
                                                            <span class="fw-bold font-monospace" style="font-size: 14px;">{{ $order->shippingDetail->tracking_number }}</span>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <span class="text-secondary d-block" style="font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Alamat Tujuan</span>
                                                        @php
                                                            $addressParts = explode(' | ', $order->shipping_address);
                                                            $fullAddr = $addressParts[1] ?? $order->shipping_address;
                                                        @endphp
                                                        <span class="text-dark small" style="font-size: 13px; line-height: 1.5;">{{ $fullAddr }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 d-flex flex-column justify-content-end align-items-md-end">
                                                    <div class="text-md-end mb-3">
                                                        <span class="text-secondary d-block" style="font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Total Belanja</span>
                                                        <span class="fw-bold text-dark fs-5">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                                                    </div>
                                                    <a href="{{ route('order.track', $order->id) }}" class="btn btn-dark w-100 rounded-0 py-2.5 fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                                        <i class="bi bi-geo-alt me-1"></i> Lacak Pengiriman
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5 bg-white border border-secondary-subtle">
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                            <i class="bi bi-truck display-5 text-secondary opacity-50"></i>
                                        </div>
                                        <h6 class="fw-bold text-uppercase mb-2">Tidak Ada Pengiriman Aktif</h6>
                                        <p class="text-secondary mb-4" style="font-size: 14px;">Saat ini tidak ada pesanan Anda yang sedang dalam proses pengiriman.</p>
                                        <a href="{{ route('product.index') }}" class="btn btn-dark rounded-0 fw-bold px-4 py-2 text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Belanja Sekarang</a>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- TAB: KONTAK KAMI --}}
                        <div class="tab-pane fade" id="content-kontak" role="tabpanel">
                            <div class="mb-4"><h3 class="fw-bold text-uppercase m-0" style="font-size: 24px; letter-spacing: 1px;">Hubungi Kami</h3><p class="text-secondary mt-1" style="font-size: 13px;">Kami siap membantu Anda.</p></div>
                            <div class="row g-5">
                                <div class="col-12 col-xl-5">
                                    <div class="bg-light p-4 p-md-5 border border-secondary-subtle rounded-0 h-100">
                                        <h5 class="fw-bold text-uppercase mb-4" style="font-size: 16px;">Informasi Kontak</h5>
                                        <div class="d-flex align-items-start mb-4"><i class="bi bi-geo-alt fs-5 me-3 text-dark"></i><div><h6 class="fw-bold mb-1" style="font-size: 13px;">Alamat Toko Utama</h6><p class="text-secondary mb-0" style="font-size: 12px;">Jl. Moh. Toha No.87, RT.001/RW.004, Gerendeng, Kec. Karawaci, Kota Tangerang, Banten 15115</p></div></div>
                                        <div class="d-flex align-items-start mb-4"><i class="bi bi-envelope fs-5 me-3 text-dark"></i><div><h6 class="fw-bold mb-1" style="font-size: 13px;">Email</h6><p class="text-secondary mb-0" style="font-size: 12px;">support@bagindojaya.com</p></div></div>
                                        <div class="d-flex align-items-start"><i class="bi bi-telephone fs-5 me-3 text-dark"></i><div><h6 class="fw-bold mb-1" style="font-size: 13px;">WhatsApp</h6><p class="text-secondary mb-0" style="font-size: 12px;">+62 812 3456 7890</p></div></div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-7">
                                    <form onsubmit="sendContactToWhatsApp(event)"><div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="fw-bold mb-2 text-uppercase" style="font-size: 11px;">Nama Lengkap</label>
                                            <input type="text" id="contact-name" class="form-control rounded-0 border-dark p-3 shadow-none" placeholder="Nama Anda" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-bold mb-2 text-uppercase" style="font-size: 11px;">Alamat Email</label>
                                            <input type="email" id="contact-email" class="form-control rounded-0 border-dark p-3 shadow-none" placeholder="Email Anda" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="fw-bold mb-2 text-uppercase" style="font-size: 11px;">Pesan Anda</label>
                                            <textarea id="contact-message" class="form-control rounded-0 border-dark p-3 shadow-none" rows="5" placeholder="Tulis pesan Anda..." required></textarea>
                                        </div>
                                        <div class="col-12 mt-4">
                                            <button type="submit" class="btn btn-dark w-100 rounded-0 py-3 fw-bold text-uppercase">
                                                KIRIM PESAN
                                            </button>
                                        </div>
                                    </div></form>
                                </div>
                            </div>
                        </div>

                        {{-- TAB: LOKASI TOKO --}}
                        <div class="tab-pane fade" id="content-lokasi" role="tabpanel">
                            <h3 class="fw-bold mb-5 text-uppercase" style="letter-spacing: 0.5px;">INFORMASI LOKASI TOKO</h3>
                            @php
                                $locations = [
                                    ['title' => 'Bagindo Jaya Tangerang Selatan', 'addr' => 'Jl. HOS Cokroaminoto No.52, Larangan', 'map' => 'https://maps.google.com/maps?q=Bagindo+Jaya+Tangerang+Selatan&t=&z=13&ie=UTF8&iwloc=&output=embed', 'link' => '#'],
                                    ['title' => 'Bagindo Jaya Citra Raya', 'addr' => 'QG7F+2JW, Cikupa, Tangerang', 'map' => 'https://maps.google.com/maps?q=Cikupa+Tangerang&t=&z=13&ie=UTF8&iwloc=&output=embed', 'link' => '#'],
                                ];
                            @endphp
                            @foreach($locations as $loc)
                                <div class="row g-4 mb-5 border-bottom pb-4">
                                    <div class="col-12 col-lg-6"><div class="rounded overflow-hidden border border-secondary-subtle"><iframe src="{{ $loc['map'] }}" width="100%" height="250" style="border:0;" loading="lazy"></iframe></div></div>
                                    <div class="col-12 col-lg-6 d-flex flex-column justify-content-center"><h5 class="fw-bold text-dark mb-3">{{ $loc['title'] }}</h5><p class="profile-info-text mb-4">{{ $loc['addr'] }}</p><a href="{{ $loc['link'] }}" target="_blank" class="btn btn-outline-dark rounded-0 px-4 py-3 fw-bold w-100 text-uppercase" style="letter-spacing: 1px;"><i class="bi bi-map me-2"></i>Buka di Google Maps</a></div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    @include('customer.components.footer')
    @include('customer.components.chatbot')

    {{-- 🌟 MODAL POP-UP UNTUK BERI ULASAN PRODUK 🌟 --}}
        <div class="modal fade" id="reviewOrderModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered rounded-0">
                <div class="modal-content rounded-0 border-dark">
                    <form id="form-review-produk" onsubmit="submitReviewProduk(event)">
                        <div class="modal-header bg-light border-bottom border-secondary-subtle py-3">
                            <h6 class="modal-title fw-bold text-uppercase m-0" style="letter-spacing: 0.5px; font-size: 13px;">Berikan Penilaian Produk</h6>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="fw-bold small text-uppercase mb-2">Pilih Produk Untuk Diulas</label>
                                <select id="review-product-sku" name="order_item_id" class="form-select rounded-0 border-dark shadow-none small" required>
                                    <!-- Dinamis diisi lewat JavaScript -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold small text-uppercase mb-2">Rating Produk</label>
                                <select name="rating" class="form-select rounded-0 border-dark shadow-none text-warning fw-bold" style="font-size: 13px;" required>
                                    <option value="5">⭐⭐⭐⭐⭐ 5 - Sangat Bagus</option>
                                    <option value="4">⭐⭐⭐⭐ 4 - Bagus</option>
                                    <option value="3">⭐⭐⭐ 3 - Cukup</option>
                                    <option value="2">⭐⭐ 2 - Kurang</option>
                                    <option value="1">⭐ 1 - Buruk</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="fw-bold small text-uppercase mb-2">Komentar / Ulasan</label>
                                <textarea name="comment" rows="4" class="form-control rounded-0 border-dark shadow-none small" placeholder="Bagikan pengalaman produk belanjamu di Bagindo Jaya..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-white border-top border-light p-3">
                            <button type="button" class="btn btn-outline-dark fw-bold text-uppercase rounded-0" style="font-size: 11px; padding: 10px 20px;" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="btnSubmitReview" class="btn btn-dark fw-bold text-uppercase rounded-0" style="font-size: 11px; padding: 10px 20px;">Kirim Ulasan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- POP-UP MODAL ALASAN PEMBATALAN PESANAN --}}
        <div class="modal fade" id="cancelOrderModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered rounded-0">
                <div class="modal-content rounded-0 border-dark text-start">
                    <div class="modal-header bg-light border-bottom border-secondary-subtle py-3">
                        <h6 class="modal-title fw-bold text-uppercase m-0" style="letter-spacing: 0.5px; font-size: 13px;">Alasan Pembatalan Pesanan</h6>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-secondary mb-3" style="font-size: 13px;">Silakan pilih alasan pembatalan Anda. Aksi ini akan membatalkan pesanan dan mengembalikan stok produk otomatis.</p>
                        <input type="hidden" id="modalCancelOrderId">
                        
                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-center gap-2 p-2 border border-secondary-subtle style-reason" style="cursor: pointer; font-size: 13px;">
                                <input type="radio" name="cancel_reason" value="Ingin mengubah rincian pesanan (ukuran, warna, alamat)" checked class="form-check-input border-dark shadow-none">
                                <span>Ingin mengubah rincian pesanan (ukuran, warna, alamat)</span>
                            </label>
                            
                            <label class="d-flex align-items-center gap-2 p-2 border border-secondary-subtle style-reason" style="cursor: pointer; font-size: 13px;">
                                <input type="radio" name="cancel_reason" value="Penjual tidak membalas chat / Kurang responsif" class="form-check-input border-dark shadow-none">
                                <span>Penjual tidak membalas chat / Kurang responsif</span>
                            </label>
                            
                            <label class="d-flex align-items-center gap-2 p-2 border border-secondary-subtle style-reason" style="cursor: pointer; font-size: 13px;">
                                <input type="radio" name="cancel_reason" value="Menemukan harga yang lebih murah di toko lain" class="form-check-input border-dark shadow-none">
                                <span>Menemukan harga yang lebih murah di toko lain</span>
                            </label>
                            
                            <label class="d-flex align-items-center gap-2 p-2 border border-secondary-subtle style-reason" style="cursor: pointer; font-size: 13px;">
                                <input type="radio" name="cancel_reason" value="Waktu pengiriman terlalu lama" class="form-check-input border-dark shadow-none">
                                <span>Waktu pengiriman terlalu lama</span>
                            </label>
                            
                            <label class="d-flex align-items-center gap-2 p-2 border border-secondary-subtle style-reason" style="cursor: pointer; font-size: 13px;">
                                <input type="radio" name="cancel_reason" value="Berubah pikiran / Tidak jadi membeli" class="form-check-input border-dark shadow-none">
                                <span>Berubah pikiran / Tidak jadi membeli</span>
                            </label>

                            <label class="d-flex align-items-center gap-2 p-2 border border-secondary-subtle style-reason" style="cursor: pointer; font-size: 13px;">
                                <input type="radio" name="cancel_reason" value="Lainnya" class="form-check-input border-dark shadow-none">
                                <span>Lainnya</span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer bg-white border-top border-light p-3">
                        <button type="button" class="btn btn-outline-dark fw-bold text-uppercase rounded-0" style="font-size: 11px; padding: 10px 20px;" data-bs-dismiss="modal">Kembali</button>
                        <button type="button" id="btnConfirmCancel" onclick="submitCancelOrder()" class="btn btn-dark fw-bold text-uppercase rounded-0" style="font-size: 11px; padding: 10px 20px;">Konfirmasi Batalkan</button>
                    </div>
                </div>
            </div>
        </div>

    @push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    
    <style>
        .border-end-md { border-right: 1px solid #dee2e6; }
        @media (max-width: 768px) { .border-end-md { border-right: none; border-bottom: 1px solid #dee2e6; padding-bottom: 15px; } }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');

            if (tabParam) {
                const tabMapping = {
                    'orders': 'tab-pesanan',
                    'alamat': 'tab-alamat',
                    'lacak': 'tab-lacak'
                };

                const targetTabId = tabMapping[tabParam];

                if (targetTabId) {
                    const tabTrigger = document.getElementById(targetTabId);
                    if (tabTrigger) {
                        const tab = new bootstrap.Tab(tabTrigger);
                        tab.show();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-lanjut-bayar').forEach(btn => {
                btn.addEventListener('click', function() {
                    let token = this.getAttribute('data-token');
                    let originalText = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> MEMUAT...';
                    this.disabled = true;
                    
                    window.snap.pay(token, {
                        onSuccess: () => { window.location.reload(); },
                        onPending: () => { btn.innerHTML = originalText; btn.disabled = false; },
                        onError: () => { btn.innerHTML = originalText; btn.disabled = false; },
                        onClose: () => { btn.innerHTML = originalText; btn.disabled = false; }
                    });
                });
            });
        });

       function setMainAddress(id) {
            fetch(`/address/${id}/set-main`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    window.location.href = window.location.pathname + "?tab=alamat";
                }
            });
        }

        function deleteAddress(id) {
            fetch(`/address/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    const card = document.getElementById(`address-card-${id}`);
                    if(card) {
                        card.style.transition = '0.3s'; 
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.95)'; 
                        setTimeout(() => card.remove(), 300);
                    }
                }
            });
        }

        moment.locale('id');

        function toggleOrderItems(element, count) {
            const textSpan = element.querySelector('.toggle-text');
            const icon = element.querySelector('.toggle-icon');
            
            setTimeout(() => {
                const isExpanded = element.getAttribute('aria-expanded') === 'true';
                if (isExpanded) {
                    textSpan.innerText = 'Sembunyikan produk';
                    icon.classList.remove('bi-chevron-down');
                    icon.classList.add('bi-chevron-up');
                } else {
                    textSpan.innerText = `Tampilkan ${count} produk lainnya`;
                    icon.classList.remove('bi-chevron-up');
                    icon.classList.add('bi-chevron-down');
                }
            }, 50);
        }

        // ==========================================
        // FUNGSI PESANAN DITERIMA (DARI HALAMAN PROFIL)
        // ==========================================
        // Pastikan ID select-nya benar: "review-product-sku"
function submitReviewProduk(e) {
    e.preventDefault();
    const form = document.getElementById('form-review-produk');
    const submitBtn = document.getElementById('btnSubmitReview');
    
    // Pastikan order_item_id diambil dari select box yang benar
    const orderItemId = document.getElementById('review-product-sku').value;
    
    submitBtn.disabled = true;
    submitBtn.innerText = 'MENGIRIM...';
    
    axios.post("{{ route('order.review') }}", { // Sesuaikan route-nya jika perlu
        order_item_id: orderItemId,
        rating: form.rating.value,
        comment: form.comment.value,
        _token: '{{ csrf_token() }}'
    })
    .then(res => {
        if (res.data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: res.data.message || 'Terima kasih atas ulasannya! ⭐',
                icon: 'success',
                customClass: {
                    confirmButton: 'btn btn-dark fw-bold text-uppercase rounded-0 px-4 py-2'
                },
                buttonsStyling: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Gagal',
                text: res.data.message || 'Gagal mengirim ulasan.',
                icon: 'error',
                customClass: {
                    confirmButton: 'btn btn-dark fw-bold text-uppercase rounded-0 px-4 py-2'
                },
                buttonsStyling: false
            });
            submitBtn.disabled = false;
            submitBtn.innerText = 'Kirim Ulasan';
        }
    }).catch(err => {
        console.error(err);
        Swal.fire({
            title: 'Error',
            text: 'Gagal mengirim ulasan. Pastikan Anda belum pernah mengulas produk ini.',
            icon: 'error',
            customClass: {
                confirmButton: 'btn btn-dark fw-bold text-uppercase rounded-0 px-4 py-2'
            },
            buttonsStyling: false
        });
        submitBtn.disabled = false;
        submitBtn.innerText = 'Kirim Ulasan';
    });
}


        function openReviewModal(orderId, items) {
    // 1. Tampilkan modal
    const myModal = new bootstrap.Modal(document.getElementById('reviewOrderModal'));
    myModal.show();

    // 2. Isi select secara dinamis
    const select = document.getElementById('review-product-sku');
    select.innerHTML = '';
    if (items && items.length > 0) {
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = `${item.product_name} (Size: ${item.product_size || '-'})`;
            select.appendChild(option);
        });
    }

    // 3. Jika kamu punya form di dalam modal, kamu bisa set action URL-nya secara dinamis
    const form = document.getElementById('form-review-produk');
    form.setAttribute('action', `/order/${orderId}/review`); 
    
    // 4. Simpan orderId di input hidden agar bisa dikirim ke server
    let hiddenInput = form.querySelector('input[name="order_id"]');
    if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'order_id';
        form.appendChild(hiddenInput);
    }
    hiddenInput.value = orderId;
}

function cancelOrder(id) {
    document.getElementById('modalCancelOrderId').value = id;
    new bootstrap.Modal(document.getElementById('cancelOrderModal')).show();
}

function submitCancelOrder() {
    const orderId = document.getElementById('modalCancelOrderId').value;
    const selectedReason = document.querySelector('input[name="cancel_reason"]:checked').value;
    const confirmBtn = document.getElementById('btnConfirmCancel');
    let originalText = 'Konfirmasi Batalkan';
    
    if (confirmBtn) {
        originalText = confirmBtn.innerHTML;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> MEMBATALKAN...';
        confirmBtn.disabled = true;
    }

    axios.post(`/profile/order/${orderId}/cancel-manual`, {
        _token: '{{ csrf_token() }}',
        reason: selectedReason
    }, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.data.success) {
            Swal.fire({
                title: 'Sukses',
                text: 'Pesanan berhasil dibatalkan!',
                icon: 'success',
                customClass: {
                    confirmButton: 'btn btn-dark fw-bold text-uppercase rounded-0 px-4 py-2'
                },
                buttonsStyling: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Gagal',
                text: response.data.message || 'Gagal membatalkan pesanan.',
                icon: 'error',
                customClass: {
                    confirmButton: 'btn btn-dark fw-bold text-uppercase rounded-0 px-4 py-2'
                },
                buttonsStyling: false
            });
            if (confirmBtn) {
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            }
        }
    })
    .catch(error => {
        console.error("Cancel Error:", error);
        Swal.fire({
            title: 'Error',
            text: 'Terjadi kesalahan koneksi server.',
            icon: 'error',
            customClass: {
                confirmButton: 'btn btn-dark fw-bold text-uppercase rounded-0 px-4 py-2'
            },
            buttonsStyling: false
        });
        if (confirmBtn) {
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        }
    });
}

function sendContactToWhatsApp(event) {
    event.preventDefault();
    const name = document.getElementById('contact-name').value.trim();
    const email = document.getElementById('contact-email').value.trim();
    const message = document.getElementById('contact-message').value.trim();
    
    if (!name || !email || !message) {
        Swal.fire({
            title: 'Form Belum Lengkap',
            text: 'Harap isi semua kolom formulir kontak!',
            icon: 'warning',
            customClass: {
                confirmButton: 'btn btn-dark fw-bold text-uppercase rounded-0 px-4 py-2'
            },
            buttonsStyling: false
        });
        return;
    }
    
    const waNumber = "6281234567890"; // WhatsApp CS Toko Utama
    const text = `Halo CS Bagindo Jaya, saya ingin menghubungi Anda:\n\n*Nama:* ${name}\n*Email:* ${email}\n*Pesan:* ${message}`;
    const encodedText = encodeURIComponent(text);
    
    const waUrl = `https://api.whatsapp.com/send?phone=${waNumber}&text=${encodedText}`;
    window.open(waUrl, '_blank');
}

function submitOrderReceived(orderId) {
    Swal.fire({
        title: 'Konfirmasi Penerimaan',
        text: 'Apakah Anda yakin paket telah diterima dengan aman dan ingin menyelesaikan pesanan ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'YA, SELESAIKAN',
        cancelButtonText: 'BATAL',
        customClass: {
            confirmButton: 'btn btn-dark fw-bold text-uppercase rounded-0 px-4 py-2 me-2',
            cancelButton: 'btn btn-outline-dark fw-bold text-uppercase rounded-0 px-4 py-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = document.getElementById('btn-complete-' + orderId);
            if (btn) {
                btn.disabled = true;
                btn.innerText = 'MEMPROSES...';
            }

            axios.post(`/profile/order/${orderId}/complete`, {
                _token: '{{ csrf_token() }}'
            })
            .then(res => {
                if (res.data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Pesanan berhasil diselesaikan! Terima kasih telah berbelanja di Bagindo Jaya.',
                        icon: 'success',
                        customClass: {
                            confirmButton: 'btn btn-dark fw-bold text-uppercase rounded-0 px-4 py-2'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal',
                        text: res.data.message,
                        icon: 'error',
                        customClass: {
                            confirmButton: 'btn btn-dark fw-bold text-uppercase rounded-0 px-4 py-2'
                        },
                        buttonsStyling: false
                    });
                    if (btn) {
                        btn.disabled = false;
                        btn.innerText = 'Pesanan Diterima';
                    }
                }
            }).catch(() => {
                Swal.fire({
                    title: 'Error',
                    text: 'Terjadi kesalahan koneksi server.',
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-dark fw-bold text-uppercase rounded-0 px-4 py-2'
                    },
                    buttonsStyling: false
                });
                if (btn) {
                    btn.disabled = false;
                    btn.innerText = 'Pesanan Diterima';
                }
            });
        }
    });
}
    </script>
    @endpush
@endsection
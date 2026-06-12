@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Detail Pesanan: #{{ $order->invoice_number }}</h1>
            <p class="text-muted small mb-0">Dibuat pada: {{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div>
            @if(in_array(strtolower($order->payment_status), ['paid', 'settlement']) || in_array(strtolower($order->status), ['confirmed', 'processing', 'preparing', 'shipped', 'delivered', 'completed']))
                <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-info shadow-sm fw-bold me-2">
                    <i class="fas fa-file-invoice me-1"></i> Invoice
                </a>
            @endif

            @php
                $isPickup = $order->shippingDetail && strtolower($order->shippingDetail->courier_company) === 'pickup';
            @endphp

            @if(!$isPickup)
                @if(in_array(strtolower($order->status), ['pending', 'confirmed']))
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="d-inline-block me-2">
                        @csrf
                        <input type="hidden" name="status" value="processing">
                        <input type="hidden" name="payment_status" value="{{ $order->payment_status }}">
                        <button type="submit" class="btn btn-warning shadow-sm fw-bold">
                            <i class="fas fa-box me-1"></i> Proses Pesanan
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.shippings.index', ['search' => $order->invoice_number]) }}" class="btn btn-success shadow-sm fw-bold me-2">
                    <i class="fas fa-truck me-1"></i> Buka Pengiriman
                </a>
            @else
                <a href="{{ route('admin.pickups.index', ['search' => $order->invoice_number]) }}" class="btn btn-success shadow-sm fw-bold me-2">
                    <i class="fas fa-store me-1"></i> Buka Pick Up
                </a>
            @endif

            @if(!in_array(strtolower($order->status), ['completed', 'cancelled']))
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="d-inline-block me-2">
                    @csrf
                    <input type="hidden" name="status" value="cancelled">
                    <input type="hidden" name="payment_status" value="{{ strtolower($order->payment_status) === 'paid' ? 'refunded' : 'failed' }}">
                    <button type="submit" class="btn btn-danger shadow-sm fw-bold" onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                        <i class="fas fa-times-circle me-1"></i> Batalkan
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Kolom Kiri: Info Pelanggan & Alamat -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-box-open me-1"></i> Item Pesanan</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Produk</th>
                                    <th>SKU</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end pe-4">Harga Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            @if($item->sku && $item->sku->product && $item->sku->product->images->isNotEmpty())
                                                <img src="{{ asset('storage/' . $item->sku->product->images->where('is_primary', 1)->first()->image_path ?? $item->sku->product->images->first()->image_path) }}" 
                                                     alt="Img" class="rounded me-3 border" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 border d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $item->sku->product->name ?? 'Produk Dihapus' }}</h6>
                                                <small class="text-muted">Size: {{ $item->sku->size ?? '-' }} | Color: {{ $item->sku->color ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $item->sku->id ?? '-' }}</span></td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end pe-4">Rp {{ number_format($item->sku->discount_price ?? $item->sku->base_price ?? $item->price_at_purchase ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light text-end pe-4 py-3">
                    <h6 class="mb-0 text-muted">Total Tagihan: <span class="fs-5 fw-bold text-dark ms-2">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span></h6>
                </div>
            </div>
            
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-concierge-bell me-1"></i> Informasi Layanan</h6>
                </div>
                <div class="card-body">
                    @if($order->shippingDetail)
                        <div class="row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <small class="text-muted d-block mb-1">Kurir / Layanan</small>
                                <span class="fw-semibold">{{ strtoupper($order->shippingDetail->courier_company) }} - {{ $order->shippingDetail->courier_type }}</span>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block mb-1">Nomor Resi (Tracking)</small>
                                @if($order->shippingDetail->tracking_number)
                                    <span class="badge bg-primary fs-6">{{ $order->shippingDetail->tracking_number }}</span>
                                @else
                                    <span class="text-muted fst-italic">Belum ada resi</span>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="row mt-3">
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">Biaya Ongkir</small>
                                <span class="fw-semibold">Rp {{ number_format($order->shippingDetail->cost, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-muted fst-italic py-2">Informasi layanan tidak tersedia.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Status & Pelanggan -->
        <div class="col-lg-4">
            
            <!-- Card Status -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-info-circle me-1"></i> Status Pesanan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Status Pembayaran</small>
                        <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'failed' ? 'danger' : 'warning text-white') }} fs-6">
                            {{ strtoupper($order->payment_status) }}
                        </span>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1">Status Pengiriman</small>
                        <span class="badge bg-{{ 
                            $order->status === 'completed' ? 'success' : 
                            ($order->status === 'cancelled' ? 'danger' : 
                            ($order->status === 'processing' ? 'info' : 'secondary text-white')) 
                        }} fs-6">
                            {{ strtoupper($order->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card Pelanggan -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-user me-1"></i> Data Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold me-3" style="width: 45px; height: 45px;">
                            {{ substr($order->user->name ?? 'T', 0, 1) }}
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">{{ $order->user->name ?? 'Tamu' }}</h6>
                            <small class="text-muted">{{ $order->user->email ?? '-' }}</small>
                        </div>
                    </div>
                    <hr>
                    <small class="text-muted d-block mb-1">Alamat Pengiriman (Biteship Info)</small>
                    <p class="mb-0 small" style="line-height: 1.5;">
                        <span class="fw-semibold">No HP:</span> {{ $order->user->phone_number ?? '-' }}<br>
                        <em>Detail alamat lengkap ada di Biteship Order ID jika menggunakan Biteship.</em>
                    </p>
                </div>
            </div>

            <!-- Card Pembayaran Detail -->
            @if($order->payment)
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-credit-card me-1"></i> Gateway Pembayaran</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted d-block mb-1">Tipe Pembayaran</small>
                    <p class="mb-2 fw-semibold">{{ strtoupper(str_replace('_', ' ', $order->payment->payment_type)) }}</p>
                    
                    <small class="text-muted d-block mb-1">Bank Penerbit</small>
                    <p class="mb-2 fw-semibold">{{ strtoupper($order->payment->bank_name ?? '-') }}</p>
                    
                    <small class="text-muted d-block mb-1">Midtrans Transaction ID</small>
                    <p class="mb-0 text-break small">{{ $order->payment->midtrans_transaction_id ?? '-' }}</p>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

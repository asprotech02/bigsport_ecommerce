@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Detail Pelanggan</h1>
            <p class="text-muted small mb-0">Member sejak: {{ $customer->created_at->format('d M Y') }}</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Profil & Info Dasar -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0 text-center">
                <div class="card-body py-5">
                    <div class="bg-primary bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center text-primary fw-bold mb-3" style="width: 80px; height: 80px; font-size: 30px;">
                        {{ strtoupper(substr($customer->name ?? 'U', 0, 1)) }}
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $customer->name }}</h5>
                    <p class="text-muted mb-4">{{ $customer->email }}</p>
                    
                    <div class="d-flex justify-content-between text-start mt-4 pt-3 border-top">
                        <div>
                            <small class="text-muted d-block">No HP</small>
                            <span class="fw-semibold">{{ $customer->phone_number ?? '-' }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Gender</small>
                            <span class="fw-semibold">{{ ucfirst($customer->gender ?? '-') }}</span>
                        </div>
                    </div>
                    
                    <div class="text-start mt-3">
                        <small class="text-muted d-block">Tanggal Lahir</small>
                        <span class="fw-semibold">{{ $customer->birthday ? \Carbon\Carbon::parse($customer->birthday)->format('d M Y') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alamat & Transaksi -->
        <div class="col-lg-8 mb-4">
            <!-- Alamat Tersimpan -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-map-marker-alt me-1"></i> Alamat Pengiriman ({{ $customer->addresses ? $customer->addresses->count() : 0 }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <tbody>
                                @forelse($customer->addresses ?? [] as $address)
                                <tr>
                                    <td class="ps-4">
                                        @if($address->is_main)
                                            <span class="badge bg-primary mb-1">Utama</span><br>
                                        @endif
                                        <span class="fw-bold d-block">{{ $address->recipient_name }} - {{ $address->phone_number }}</span>
                                        <p class="text-muted small mb-0 mt-1">
                                            {{ $address->street_address }}<br>
                                            {{ $address->district }}, {{ $address->city }}, {{ $address->province }} - {{ $address->postal_code }}
                                        </p>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-center py-4 text-muted">Belum ada alamat yang disimpan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pesanan -->
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-shopping-bag me-1"></i> Transaksi Terakhir</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->orders()->orderByDesc('created_at')->take(5)->get() ?? [] as $order)
                                <tr>
                                    <td class="ps-4">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-decoration-none">{{ $order->invoice_number }}</a>
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $order->status === 'completed' ? 'success' : 
                                            ($order->status === 'cancelled' ? 'danger' : 'secondary') 
                                        }}">
                                            {{ strtoupper($order->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 fw-semibold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

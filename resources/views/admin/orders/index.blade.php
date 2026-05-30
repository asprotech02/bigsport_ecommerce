@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white font-weight-bold">Kelola Pesanan</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: rgba(46, 196, 182, 0.15); color: #2ec4b6; border: 1px solid rgba(46, 196, 182, 0.3);">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Compact Filters Card -->
    <div class="card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-filter me-1"></i> Filter Pencarian</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Cari Invoice / Pelanggan</label>
                        <input type="text" name="search" class="form-control" placeholder="No. Invoice atau Nama..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-muted small">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-muted small">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-muted small">Status Pesanan</label>
                        <select name="status" class="form-select form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>PENDING</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>CONFIRMED</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>PROCESSING</option>
                            <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>PREPARING</option>
                            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>SHIPPED</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>DELIVERED</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>COMPLETED</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>CANCELLED</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-muted small">Status Bayar</label>
                        <select name="payment_status" class="form-select form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>UNPAID</option>
                            <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>PENDING</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>PAID</option>
                            <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>FAILED</option>
                            <option value="expired" {{ request('payment_status') === 'expired' ? 'selected' : '' }}>EXPIRED</option>
                            <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>REFUNDED</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-sync-alt me-1"></i> Reset</a>
                    <button type="submit" class="btn btn-sm btn-primary px-3"><i class="fas fa-search me-1"></i> Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No. Invoice</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total Tagihan</th>
                            <th>Status Bayar</th>
                            <th>Status Pesanan</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-4 fw-bold text-white">{{ $order->invoice_number }}</td>
                                <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <div class="fw-semibold text-white">{{ $order->user->name ?? 'Tamu' }}</div>
                                    <span class="text-muted small">{{ $order->user->email ?? '' }}</span>
                                </td>
                                <td class="fw-semibold text-white">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $payStatus = strtolower($order->payment_status);
                                        $payBg = $payStatus === 'paid' ? 'success' : 
                                            (($payStatus === 'failed' || $payStatus === 'expired') ? 'danger' : 'warning');
                                    @endphp
                                    <span class="badge bg-{{ $payBg }} text-white text-uppercase px-3 py-1.5 rounded-pill fs-7 fw-semibold">
                                        {{ $order->payment_status }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $orderStatus = strtolower($order->status);
                                        $orderBg = $orderStatus === 'completed' ? 'success' : 
                                            (($orderStatus === 'cancelled' || $orderStatus === 'failed') ? 'danger' : 
                                            (in_array($orderStatus, ['processing', 'preparing', 'shipped', 'delivered', 'confirmed']) ? 'info' : 'warning'));
                                    @endphp
                                    <span class="badge bg-{{ $orderBg }} text-white text-uppercase px-3 py-1.5 rounded-pill fs-7 fw-semibold">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center" style="gap: 6px;">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" 
                                           class="btn btn-sm btn-outline-light px-2.5 py-1.5 d-flex align-items-center" 
                                           style="font-size: 0.75rem; border-radius: 6px; border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);" 
                                           title="Detail">
                                            <i class="fas fa-eye me-1.5"></i> Detail
                                        </a>
                                        <button class="btn btn-sm btn-outline-light px-2.5 py-1.5 d-flex align-items-center" 
                                                style="font-size: 0.75rem; border-radius: 6px; border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);" 
                                                data-toggle="modal" 
                                                data-target="#statusModal{{ $order->id }}" 
                                                title="Ubah Status">
                                            <i class="fas fa-edit me-1.5"></i> Status
                                        </button>
                                    </div>

                                    <!-- Modal Ubah Status untuk Order Ini -->
                                    <div class="modal fade text-start" id="statusModal{{ $order->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="w-100">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-light">
                                                        <h5 class="modal-title fw-bold text-white"><i class="fas fa-edit me-1"></i> Ubah Status: #{{ $order->invoice_number }}</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label for="status_{{ $order->id }}" class="form-label fw-semibold text-white">Status Pesanan</label>
                                                            <select class="form-select" name="status" id="status_{{ $order->id }}" required>
                                                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Pending (Menunggu Pembayaran)</option>
                                                                <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>✅ Confirmed (Dibayar/Dikonfirmasi)</option>
                                                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>📦 Processing (Sedang Diproses)</option>
                                                                <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>🛠️ Preparing (Sedang Disiapkan)</option>
                                                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>🚚 Shipped (Sedang Dikirim)</option>
                                                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>🏠 Delivered (Telah Sampai)</option>
                                                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>🎉 Completed (Selesai)</option>
                                                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Cancelled (Dibatalkan)</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="payment_status_{{ $order->id }}" class="form-label fw-semibold text-white">Status Pembayaran</label>
                                                            <select class="form-select" name="payment_status" id="payment_status_{{ $order->id }}" required>
                                                                <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>❌ Unpaid</option>
                                                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>✅ Paid</option>
                                                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>❌ Failed</option>
                                                                <option value="expired" {{ $order->payment_status === 'expired' ? 'selected' : '' }}>⚠️ Expired</option>
                                                                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>💰 Refunded</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">Simpan Perubahan</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Tidak ditemukan riwayat pesanan yang cocok dengan filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
            <div class="card-footer py-3" style="background-color: var(--dark-sidebar); border-top: 1px solid var(--border-glass);">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-white font-weight-bold">Kelola Pick Up (Ambil di Toko)</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert" style="background-color: rgba(46, 196, 182, 0.15); color: #2ec4b6; border: 1px solid rgba(46, 196, 182, 0.3);">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Compact Advanced Filter Card -->
    <div class="card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-filter me-1"></i> Filter Pick Up</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pickups.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-9">
                        <label class="form-label text-muted small mb-1">Cari Invoice / Pelanggan</label>
                        <input type="text" name="search" class="form-control" placeholder="Cari nomor invoice atau nama pelanggan..." value="{{ request('search') }}">
                    </div>
                    <div class="col-auto d-flex gap-1 ms-auto">
                        <a href="{{ route('admin.pickups.index') }}" class="btn btn-secondary px-3"><i class="fas fa-sync-alt"></i></a>
                        <button type="submit" class="btn btn-primary px-4">Terapkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7">
                        <tr>
                            <th class="ps-4">No. Invoice</th>
                            <th>Nama Pelanggan</th>
                            <th>Lokasi Pengambilan</th>
                            <th>Biaya Layanan</th>
                            <th>Status Pesanan</th>
                            <th>Tanggal Pesanan</th>
                            <th class="text-end pe-4" width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pickups as $pickup)
                            <tr>
                                <td class="ps-4 fw-bold text-white">
                                    <a href="{{ route('admin.orders.show', $pickup->order_id) }}" class="text-white text-decoration-none">
                                        {{ $pickup->order->invoice_number ?? '-' }}
                                    </a>
                                </td>
                                <td>
                                    <span class="fw-semibold text-white">{{ $pickup->order->user->name ?? '-' }}</span>
                                    <span class="text-muted small d-block">{{ $pickup->order->user->email ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-white text-uppercase px-2 py-1 rounded" style="font-size: 0.8rem;">
                                        {{ str_replace('_', ' ', $pickup->courier_type) }}
                                    </span>
                                </td>
                                <td class="fw-bold text-white">Gratis</td>
                                <td>
                                    @php
                                        $orderStatus = strtolower($pickup->order->status ?? '');
                                        $orderBg = $orderStatus === 'completed' ? 'success' : 
                                            (($orderStatus === 'cancelled' || $orderStatus === 'failed') ? 'danger' : 
                                            (in_array($orderStatus, ['processing', 'preparing', 'shipped', 'delivered', 'confirmed']) ? 'info' : 'warning'));
                                    @endphp
                                    <span class="badge bg-{{ $orderBg }} text-white text-uppercase px-3 py-1.5 rounded-pill fs-7 fw-semibold">
                                        @if($orderStatus === 'delivered')
                                            Ready for Pick Up
                                        @else
                                            {{ $pickup->order->status ?? '-' }}
                                        @endif
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $pickup->created_at->format('d M Y H:i') }}</td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center" style="gap: 6px;">
                                        <button class="btn btn-sm btn-outline-light px-2.5 py-1.5 d-flex align-items-center" 
                                                style="font-size: 0.75rem; border-radius: 6px; border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);" 
                                                data-toggle="modal" 
                                                data-target="#pickupModal{{ $pickup->id }}" 
                                                title="Edit Status Pick Up">
                                            <i class="fas fa-edit me-1.5"></i> Edit Status
                                        </button>
                                    </div>
                                </td>

                                <!-- Modal Edit Pick Up Status -->
                                <div class="modal fade text-start" id="pickupModal{{ $pickup->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form method="POST" action="{{ route('admin.shippings.update', $pickup->id) }}" class="w-100">
                                            @csrf
                                            @method('PUT')
                                            
                                            <!-- Hidden Fields to Satisfy Validation -->
                                            <input type="hidden" name="courier_company" value="pickup">
                                            <input type="hidden" name="courier_type" value="{{ $pickup->courier_type }}">
                                            <input type="hidden" name="cost" value="0">
                                            <input type="hidden" name="tracking_number" value="">
                                            <input type="hidden" name="biteship_order_id" value="">

                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title fw-bold text-white"><i class="fas fa-store me-1"></i> Edit Status Pick Up: #{{ $pickup->order->invoice_number ?? '-' }}</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-muted small d-block">Pelanggan</label>
                                                        <div class="text-white fw-bold">{{ $pickup->order->user->name ?? '-' }}</div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-muted small d-block">Lokasi Pengambilan</label>
                                                        <div class="text-white text-uppercase">{{ str_replace('_', ' ', $pickup->courier_type) }}</div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-white">Status Pesanan / Pick Up <span class="text-danger">*</span></label>
                                                        <select name="order_status" class="form-select form-control" required>
                                                            <option value="pending" {{ ($pickup->order->status ?? '') === 'pending' ? 'selected' : '' }}>⏳ Pending (Menunggu Pembayaran)</option>
                                                            <option value="confirmed" {{ ($pickup->order->status ?? '') === 'confirmed' ? 'selected' : '' }}>✅ Confirmed (Dibayar/Dikonfirmasi)</option>
                                                            <option value="preparing" {{ ($pickup->order->status ?? '') === 'preparing' ? 'selected' : '' }}>🛠️ Preparing (Sedang Disiapkan)</option>
                                                            <option value="delivered" {{ ($pickup->order->status ?? '') === 'delivered' ? 'selected' : '' }}>🏪 Ready for Pick Up (Siap Diambil)</option>
                                                            <option value="completed" {{ ($pickup->order->status ?? '') === 'completed' ? 'selected' : '' }}>🎉 Completed (Selesai/Sudah Diambil)</option>
                                                            <option value="cancelled" {{ ($pickup->order->status ?? '') === 'cancelled' ? 'selected' : '' }}>❌ Cancelled (Dibatalkan)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">Simpan Status</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="mb-3"><i class="fas fa-store fa-3x text-muted"></i></div>
                                    <p class="mb-0 fw-semibold">Tidak ditemukan data pesanan Pick Up.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pickups->hasPages())
            <div class="card-footer py-3" style="background-color: var(--dark-sidebar); border-top: 1px solid var(--border-glass);">
                {{ $pickups->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .fs-7 { font-size: 0.8rem; }
</style>
@endsection

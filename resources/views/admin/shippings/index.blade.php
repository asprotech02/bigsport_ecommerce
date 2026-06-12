@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-white font-weight-bold">Kelola Pengiriman</h1>
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

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert" style="background-color: rgba(230, 57, 70, 0.15); color: #e63946; border: 1px solid rgba(230, 57, 70, 0.3);">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Compact Advanced Filter Card -->
    <div class="card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-filter me-1"></i> Filter Pengiriman</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.shippings.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label text-muted small mb-1">Cari No. Resi / Invoice / Pelanggan</label>
                        <input type="text" name="search" class="form-control" placeholder="Cari nomor resi atau invoice..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1">Kurir Ekspedisi</label>
                        <select name="courier" class="form-select form-control text-uppercase">
                            <option value="">-- Semua Kurir --</option>
                            @foreach($couriers as $cr)
                                <option value="{{ $cr }}" {{ request('courier') == $cr ? 'selected' : '' }}>
                                    {{ strtoupper($cr) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto d-flex gap-1 ms-auto">
                        <a href="{{ route('admin.shippings.index') }}" class="btn btn-secondary px-3"><i class="fas fa-sync-alt"></i></a>
                        <button type="submit" class="btn btn-primary px-4">Terapkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Form -->
    <form id="bulk-biteship-form" action="{{ route('admin.shippings.bookBiteshipBulk') }}" method="POST">
        @csrf
        
        <div class="mb-3 d-flex align-items-center gap-2">
            <button type="submit" id="btn-bulk-biteship" class="btn btn-success btn-sm d-flex align-items-center px-3 py-2" style="border-radius: 6px;" disabled>
                <i class="fas fa-shipping-fast me-1.5"></i> Kirim Otomatis (Masal)
            </button>
            <span id="bulk-select-count" class="text-muted small ms-2 d-none">0 terpilih</span>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase fs-7">
                            <tr>
                                <th class="ps-4" width="40">
                                    <input type="checkbox" id="check-all" class="form-check-input">
                                </th>
                                <th>No. Invoice</th>
                                <th>Layanan Kurir</th>
                                <th>Nomor Resi (Tracking)</th>
                                <th>Ongkos Kirim</th>
                                <th>Status Pesanan</th>
                                <th>Biteship Order ID</th>
                                <th class="text-end pe-4" width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shippings as $shipping)
                                <tr>
                                    <td class="ps-4">
                                        @if(strtolower($shipping->courier_company) !== 'pickup' && !$shipping->biteship_order_id)
                                            <input type="checkbox" name="shipping_ids[]" value="{{ $shipping->id }}" class="shipping-checkbox form-check-input">
                                        @else
                                            <input type="checkbox" disabled class="form-check-input opacity-50">
                                        @endif
                                    </td>
                                    <td class="fw-bold text-white">
                                        <a href="{{ route('admin.orders.show', $shipping->order_id) }}" class="text-white text-decoration-none">
                                            {{ $shipping->order->invoice_number ?? '-' }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-white text-uppercase">{{ $shipping->courier_company }}</span>
                                        <span class="text-muted small d-block">{{ $shipping->courier_type }}</span>
                                    </td>
                                    <td>
                                        @if($shipping->tracking_number)
                                            <span class="badge bg-primary text-white font-monospace px-2.5 py-1.5 rounded" style="font-size: 0.85rem;">{{ $shipping->tracking_number }}</span>
                                        @else
                                            <span class="badge bg-secondary text-white px-2.5 py-1.5 rounded" style="font-size: 0.8rem;">Menunggu Resi</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-white">Rp {{ number_format($shipping->cost, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $orderStatus = strtolower($shipping->order->status ?? '');
                                            $orderBg = $orderStatus === 'completed' ? 'success' : 
                                                (($orderStatus === 'cancelled' || $orderStatus === 'failed') ? 'danger' : 
                                                (in_array($orderStatus, ['processing', 'preparing', 'shipped', 'delivered', 'confirmed']) ? 'info' : 'warning'));
                                        @endphp
                                        <span class="badge bg-{{ $orderBg }} text-white text-uppercase px-3 py-1.5 rounded-pill fs-7 fw-semibold">
                                            {{ $shipping->order->status ?? '-' }}
                                        </span>
                                    </td>
                                    <td><span class="text-muted small text-monospace">{{ $shipping->biteship_order_id ?? '-' }}</span></td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end align-items-center" style="gap: 6px;">
                                            @if(strtolower($shipping->courier_company) !== 'pickup' && !$shipping->biteship_order_id)
                                                <button type="submit" formAction="{{ route('admin.shippings.bookBiteship', $shipping->id) }}" class="btn btn-sm btn-outline-success px-2.5 py-1.5 d-flex align-items-center" 
                                                        style="font-size: 0.75rem; border-radius: 6px;" 
                                                        title="Request Pickup & Resi Biteship secara Otomatis">
                                                    <i class="fas fa-shipping-fast me-1.5"></i> Kirim Otomatis
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-light px-2.5 py-1.5 d-flex align-items-center" 
                                                    style="font-size: 0.75rem; border-radius: 6px; border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);" 
                                                    data-toggle="modal" 
                                                    data-target="#shippingModal{{ $shipping->id }}" 
                                                    title="Edit Pengiriman & Status">
                                                <i class="fas fa-edit me-1.5"></i> Edit / Status
                                            </button>
                                        </div>
                                    </td>
                                        <!-- Modal Edit Pengiriman & Status -->
                                        <div class="modal fade text-start" id="shippingModal{{ $shipping->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="w-100">
                                                    <div class="modal-content border-0 shadow">
                                                        <form method="POST" action="{{ route('admin.shippings.update', $shipping->id) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header bg-light">
                                                                <h5 class="modal-title fw-bold text-white"><i class="fas fa-truck me-1"></i> Edit Pengiriman: #{{ $shipping->order->invoice_number ?? '-' }}</h5>
                                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label fw-semibold text-white">Kurir Ekspedisi <span class="text-danger">*</span></label>
                                                                        <input type="text" name="courier_company" value="{{ $shipping->courier_company }}" class="form-control text-uppercase" required>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label fw-semibold text-white">Layanan Kurir <span class="text-danger">*</span></label>
                                                                        <input type="text" name="courier_type" value="{{ $shipping->courier_type }}" class="form-control" required>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label fw-semibold text-white">Nomor Resi (Tracking Number)</label>
                                                                        <input type="text" name="tracking_number" value="{{ $shipping->tracking_number }}" class="form-control">
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label fw-semibold text-white">Ongkos Kirim (Rp) <span class="text-danger">*</span></label>
                                                                        <input type="number" name="cost" value="{{ intval($shipping->cost) }}" class="form-control" min="0" required>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label fw-semibold text-white">Status Pesanan / Pengiriman <span class="text-danger">*</span></label>
                                                                        <select name="order_status" class="form-select form-control" required>
                                                                            <option value="pending" {{ ($shipping->order->status ?? '') === 'pending' ? 'selected' : '' }}>⏳ Pending (Menunggu Pembayaran)</option>
                                                                            <option value="confirmed" {{ ($shipping->order->status ?? '') === 'confirmed' ? 'selected' : '' }}>✅ Confirmed (Dibayar/Dikonfirmasi)</option>
                                                                            <option value="processing" {{ ($shipping->order->status ?? '') === 'processing' ? 'selected' : '' }}>📦 Processing (Sedang Diproses)</option>
                                                                            <option value="preparing" {{ ($shipping->order->status ?? '') === 'preparing' ? 'selected' : '' }}>🛠️ Preparing (Sedang Disiapkan)</option>
                                                                            <option value="shipped" {{ ($shipping->order->status ?? '') === 'shipped' ? 'selected' : '' }}>🚚 Shipped (Sedang Dikirim)</option>
                                                                            <option value="delivered" {{ ($shipping->order->status ?? '') === 'delivered' ? 'selected' : '' }}>🏠 Delivered (Telah Sampai)</option>
                                                                            <option value="completed" {{ ($shipping->order->status ?? '') === 'completed' ? 'selected' : '' }}>🎉 Completed (Selesai)</option>
                                                                            <option value="cancelled" {{ ($shipping->order->status ?? '') === 'cancelled' ? 'selected' : '' }}>❌ Cancelled (Dibatalkan)</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label class="form-label fw-semibold text-white">Biteship Order ID</label>
                                                                        <input type="text" name="biteship_order_id" value="{{ $shipping->biteship_order_id }}" class="form-control">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-sm btn-primary fw-bold px-3">Simpan Detail</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="mb-3"><i class="fas fa-truck fa-3x text-muted"></i></div>
                                        <p class="mb-0 fw-semibold">Tidak ditemukan data pengiriman yang cocok.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($shippings->hasPages())
                <div class="card-footer py-3" style="background-color: var(--dark-sidebar); border-top: 1px solid var(--border-glass);">
                    {{ $shippings->links() }}
                </div>
            @endif
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('check-all');
    const checkboxes = document.querySelectorAll('.shipping-checkbox');
    const btnBulk = document.getElementById('btn-bulk-biteship');
    const selectCountText = document.getElementById('bulk-select-count');

    function updateBulkButtonState() {
        const checkedCount = document.querySelectorAll('.shipping-checkbox:checked').length;
        if (checkedCount > 0) {
            btnBulk.disabled = false;
            selectCountText.innerText = checkedCount + ' terpilih';
            selectCountText.classList.remove('d-none');
        } else {
            btnBulk.disabled = true;
            selectCountText.classList.add('d-none');
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                if (!cb.disabled) {
                    cb.checked = checkAll.checked;
                }
            });
            updateBulkButtonState();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = Array.from(checkboxes).every(c => c.disabled || c.checked);
            if (checkAll) {
                checkAll.checked = allChecked;
            }
            updateBulkButtonState();
        });
    });
});
</script>
@endpush

<style>
    .fs-7 { font-size: 0.8rem; }
</style>
@endsection

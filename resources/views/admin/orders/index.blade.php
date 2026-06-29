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
                                        $orderBg = ($orderStatus === 'cancelled' || $orderStatus === 'failed') ? 'danger' : 'dark';
                                    @endphp
                                    <span class="badge bg-{{ $orderBg }} text-white text-uppercase px-3 py-1.5 rounded-pill fs-7 fw-semibold">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-light dropdown-toggle px-2.5 py-1.5 d-flex align-items-center" 
                                                type="button" 
                                                id="actionDropdown{{ $order->id }}" 
                                                data-toggle="dropdown" 
                                                aria-haspopup="true" 
                                                aria-expanded="false"
                                                style="font-size: 0.75rem; border-radius: 6px; border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);">
                                            <i class="fas fa-cog me-1.5"></i> Aksi
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionDropdown{{ $order->id }}" style="background-color: #1e1e2d; border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 5px 15px rgba(0,0,0,0.5); border-radius: 8px;">
                                            <a class="dropdown-item text-white py-2" href="{{ route('admin.orders.show', $order->id) }}">
                                                <i class="fas fa-eye me-2 text-primary"></i> Detail
                                            </a>
                                            
                                            @if(in_array(strtolower($order->payment_status), ['paid', 'settlement']) || in_array(strtolower($order->status), ['confirmed', 'processing', 'preparing', 'shipped', 'delivered', 'completed']))
                                                <a class="dropdown-item text-white py-2" href="{{ route('admin.orders.invoice', $order->id) }}">
                                                    <i class="fas fa-file-invoice me-2 text-info"></i> Invoice
                                                </a>
                                            @endif

                                            @php
                                                $isPickup = $order->shippingDetail && strtolower($order->shippingDetail->courier_company) === 'pickup';
                                            @endphp

                                            @if(!$isPickup)
                                                <!-- Delivery Order actions -->
                                                @if(in_array(strtolower($order->status), ['pending', 'confirmed']))
                                                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="processing">
                                                        <input type="hidden" name="payment_status" value="{{ $order->payment_status }}">
                                                        <button type="submit" class="dropdown-item text-white py-2" style="background: none; border: none; width: 100%; text-align: left;">
                                                            <i class="fas fa-box me-2 text-warning"></i> Proses Pesanan
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if(strtolower($order->status) === 'completed')
                                                    @if($order->shippingDetail && $order->shippingDetail->tracking_number)
                                                        <button type="button" class="dropdown-item text-white py-2 btn-track-package" 
                                                                data-id="{{ $order->shippingDetail->id }}"
                                                                data-invoice="{{ $order->invoice_number }}"
                                                                data-toggle="modal"
                                                                data-target="#trackModal">
                                                            <i class="fas fa-search-location me-2 text-success"></i> Lacak Pesanan
                                                        </button>
                                                    @endif
                                                @else
                                                    <a class="dropdown-item text-white py-2" href="{{ route('admin.shippings.index', ['search' => $order->invoice_number]) }}">
                                                        <i class="fas fa-truck me-2 text-success"></i> Buka Pengiriman
                                                    </a>
                                                @endif
                                            @else
                                                <!-- Pickup Order actions -->
                                                <a class="dropdown-item text-white py-2" href="{{ route('admin.pickups.index', ['search' => $order->invoice_number]) }}">
                                                    <i class="fas fa-store me-2 text-success"></i> Buka Pick Up
                                                </a>
                                            @endif

                                            @if(!in_array(strtolower($order->status), ['completed', 'cancelled']))
                                                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <input type="hidden" name="payment_status" value="{{ strtolower($order->payment_status) === 'paid' ? 'refunded' : 'failed' }}">
                                                    <button type="submit" class="dropdown-item text-danger py-2" style="background: none; border: none; width: 100%; text-align: left;" onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                                        <i class="fas fa-times-circle me-2"></i> Batalkan Pesanan
                                                    </button>
                                                </form>
                                            @endif
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

<!-- Modal Lacak Paket -->
<div class="modal fade" id="trackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="background-color: #1e1e2d; border: 1px solid rgba(255, 255, 255, 0.15);">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-white"><i class="fas fa-search-location me-1.5"></i> Lacak Pengiriman</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-white" id="track-modal-content">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Sedang mengambil data tracking...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.btn-track-package', function() {
        var id = $(this).data('id');
        var invoice = $(this).data('invoice');
        var modalContent = $('#track-modal-content');
        
        modalContent.html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Sedang mengambil data tracking untuk #${invoice}...</p>
            </div>
        `);
        
        $.ajax({
            url: '/admin/shippings/' + id + '/track',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    var html = `
                        <div class="mb-3 border-bottom pb-3" style="border-color: rgba(255,255,255,0.1) !important;">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">Ekspedisi</small>
                                    <strong class="text-white text-uppercase">${data.courier.company}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-1">No. Resi</small>
                                    <strong class="text-white">${data.courier.waybill_id}</strong>
                                </div>
                             </div>
                             <div class="row mt-3">
                                <div class="col-12">
                                    <small class="text-muted d-block mb-1">Status Terakhir</small>
                                    <span class="badge bg-primary text-uppercase px-2.5 py-1.5 rounded text-white fw-bold">${data.status === 'delivered' ? 'TELAH DITERIMA' : data.status.toUpperCase()}</span>
                                </div>
                            </div>
                        </div>
                        <div class="tracking-history-timeline" style="max-height: 300px; overflow-y: auto; padding-right: 5px;">
                    `;
                    
                    if (data.history && data.history.length > 0) {
                        data.history.forEach(function(item) {
                            var dateStr = new Date(item.updated_at).toLocaleString('id-ID', {
                                day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                            });
                            html += `
                                <div class="position-relative pb-3" style="border-left: 2px solid rgba(255,255,255,0.15); margin-left: 10px; padding-left: 20px;">
                                    <div class="position-absolute" style="left: -7px; top: 2px; width: 12px; height: 12px; border-radius: 50%; background-color: #4e73df;"></div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">${dateStr} WIB</small>
                                    <strong class="text-white d-block small mt-0.5">${item.status}</strong>
                                    <span class="text-muted small">${item.note}</span>
                                </div>
                            `;
                        });
                    } else {
                        html += `<p class="text-muted text-center py-3">Tidak ada riwayat pengiriman.</p>`;
                    }
                    
                    html += `</div>`;
                    modalContent.html(html);
                } else {
                    modalContent.html(`<div class="alert alert-danger border-0" style="background-color: rgba(231, 74, 59, 0.15); color: #e74a3b;">${response.message}</div>`);
                }
            },
            error: function() {
                modalContent.html('<div class="alert alert-danger border-0" style="background-color: rgba(231, 74, 59, 0.15); color: #e74a3b;">Gagal mengambil data tracking. Silakan coba lagi.</div>');
            }
        });
    });
});
</script>
@endpush

<style>
    .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
        color: #ffffff !important;
    }
</style>
@endsection

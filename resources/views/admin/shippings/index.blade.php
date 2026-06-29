@extends('admin.layouts.app')

@push('styles')
<style>
    .shipping-table {
        table-layout: auto;
        font-size: 0.78rem !important;
    }
    .shipping-table th, 
    .shipping-table td {
        padding: 0.5rem 0.4rem !important;
        vertical-align: middle !important;
    }
    .shipping-table td {
        white-space: nowrap !important;
    }
</style>
@endpush

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
        
        <div class="mb-3 d-flex align-items-center">
            <button type="button" id="btn-bulk-biteship" class="btn btn-success btn-sm d-flex align-items-center px-3 py-2" style="border-radius: 6px; margin-right: 10px;" disabled>
                <i class="fas fa-shipping-fast me-1.5"></i> Kirim Otomatis (Masal)
            </button>
            <button type="button" id="btn-bulk-complete" class="btn btn-primary btn-sm d-flex align-items-center px-3 py-2" style="border-radius: 6px;" disabled>
                <i class="fas fa-check-double me-1.5"></i> Selesaikan Pengiriman (Masal)
            </button>
            <span id="bulk-select-count" class="text-muted small ms-2 d-none">0 terpilih</span>
        </div>

        <!-- Table -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 shipping-table">
                        <thead class="table-light text-uppercase fs-7">
                            <tr>
                                <th class="ps-3" style="width: 45px;">
                                    <input type="checkbox" id="check-all" class="form-check-input position-static m-0">
                                </th>
                                <th>Invoice</th>
                                <th>Kurir</th>
                                <th>No. Resi</th>
                                <th>Ongkir</th>
                                <th>Status</th>
                                <th>Pengiriman</th>
                                <th style="width: 100px; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Biteship ID</th>
                                <th class="text-end pe-3" style="width: 280px; min-width: 280px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shippings as $shipping)
                                <tr>
                                    <td class="ps-3">
                                        <input type="checkbox" name="shipping_ids[]" value="{{ $shipping->id }}" 
                                               class="shipping-checkbox form-check-input position-static m-0"
                                               data-status="{{ strtolower($shipping->order->status ?? '') }}"
                                               data-biteship="{{ $shipping->biteship_order_id ? 'yes' : 'no' }}">
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
                                    <td>
                                        @php
                                            $shippingStatus = 'Belum Diatur';
                                            $shippingBg = 'secondary';
                                            if ($shipping->biteship_order_id) {
                                                $shippingStatus = 'Siap Dikirim';
                                                $shippingBg = 'info';
                                            }
                                            if (strtolower($shipping->order->status ?? '') === 'shipped') {
                                                $shippingStatus = 'Shipped';
                                                $shippingBg = 'primary';
                                            } elseif (in_array(strtolower($shipping->order->status ?? ''), ['delivered', 'completed'])) {
                                                $shippingStatus = 'Delivered';
                                                $shippingBg = 'success';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $shippingBg }} text-white text-uppercase px-3 py-1.5 rounded-pill fs-7 fw-semibold">
                                            {{ $shippingStatus }}
                                        </span>
                                    </td>
                                    <td style="width: 100px; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        @if($shipping->biteship_order_id)
                                            <span class="text-muted small text-monospace" title="{{ $shipping->biteship_order_id }}">
                                                {{ $shipping->biteship_order_id }}
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3" style="width: 280px; min-width: 280px;">
                                        <div class="d-flex justify-content-end align-items-center" style="gap: 3px;">
                                            @if(in_array(strtolower($shipping->order->status ?? ''), ['shipped', 'delivered']))
                                                <button type="button" class="btn btn-sm btn-success btn-complete-single px-2 py-1 d-flex align-items-center fw-bold text-white" 
                                                        style="font-size: 0.7rem; border-radius: 6px;" 
                                                        data-id="{{ $shipping->id }}"
                                                        title="Selesaikan Pengiriman">
                                                    <i class="fas fa-check me-1"></i> Selesai
                                                </button>
                                            @endif
                                            @if(strtolower($shipping->courier_company) !== 'pickup' && !$shipping->biteship_order_id)
                                                <button type="submit" formAction="{{ route('admin.shippings.bookBiteship', $shipping->id) }}" class="btn btn-sm btn-success px-2 py-1 d-flex align-items-center fw-bold" 
                                                        style="font-size: 0.7rem; border-radius: 6px;" 
                                                        title="Request Pickup & Resi Biteship secara Otomatis">
                                                    <i class="fas fa-shipping-fast me-1"></i> Kirim
                                                </button>
                                            @endif
                                            
                                            @if($shipping->biteship_order_id)
                                                <a href="{{ route('admin.shippings.label', $shipping->id) }}" class="btn btn-sm btn-info px-2 py-1 d-flex align-items-center fw-bold text-white" 
                                                   style="font-size: 0.7rem; border-radius: 6px;" 
                                                   title="Cetak Label PDF">
                                                    <i class="fas fa-print me-1"></i> Label
                                                </a>
                                            @endif

                                            @if($shipping->tracking_number)
                                                <button type="button" class="btn btn-sm btn-primary btn-track-package px-2 py-1 d-flex align-items-center fw-bold text-white" 
                                                        style="font-size: 0.7rem; border-radius: 6px !important; padding: 0.25rem 0.5rem !important;" 
                                                        data-id="{{ $shipping->id }}"
                                                        data-invoice="{{ $shipping->order->invoice_number ?? '-' }}"
                                                        data-toggle="modal"
                                                        data-target="#trackModal"
                                                        title="Lacak Pengiriman">
                                                    <i class="fas fa-search-location me-1"></i> Lacak
                                                </button>
                                            @endif


                                            <button type="button" class="btn btn-sm btn-outline-light px-2 py-1 d-flex align-items-center" 
                                                    style="font-size: 0.7rem; border-radius: 6px; border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);" 
                                                    data-toggle="modal" 
                                                    data-target="#shippingModal{{ $shipping->id }}" 
                                                    title="Atur Pengiriman & Status">
                                                <i class="fas fa-edit me-1"></i> Atur
                                            </button>
                                        </div>
                                    </td>

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

    <!-- Modals Edit Pengiriman & Status (Placed outside outer form to prevent nested form conflicts) -->
    @foreach($shippings as $shipping)
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
                                            <option value="shipped" {{ ($shipping->order->status ?? '') === 'shipped' ? 'selected' : '' }}>🚚 Shipped (Sedang Dikirim)</option>
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
    @endforeach
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

<form id="single-complete-form" action="{{ route('admin.shippings.completeBulk') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="shipping_ids[]" id="single-complete-id" value="">
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('check-all');
    const checkboxes = document.querySelectorAll('.shipping-checkbox');
    const btnBulk = document.getElementById('btn-bulk-biteship');
    const btnComplete = document.getElementById('btn-bulk-complete');
    const selectCountText = document.getElementById('bulk-select-count');
    const bulkForm = document.getElementById('bulk-biteship-form');

    function updateBulkButtonState() {
        const checkedBoxes = document.querySelectorAll('.shipping-checkbox:checked');
        const checkedCount = checkedBoxes.length;

        if (checkedCount > 0) {
            const selectedStatus = checkedBoxes[0].getAttribute('data-status');
            const hasBiteship = checkedBoxes[0].getAttribute('data-biteship');

            // Disable all checkboxes that do not have the same status
            checkboxes.forEach(cb => {
                if (!cb.checked) {
                    if (cb.getAttribute('data-status') !== selectedStatus) {
                        cb.disabled = true;
                        cb.nextElementSibling?.classList.add('opacity-50');
                    } else {
                        cb.disabled = false;
                    }
                }
            });

            // Enable action button based on the status/biteship presence
            if (hasBiteship === 'no' && ['confirmed', 'processing', 'preparing'].includes(selectedStatus)) {
                btnBulk.disabled = false;
                btnComplete.disabled = true;
            } else if (['shipped', 'delivered'].includes(selectedStatus) || hasBiteship === 'yes') {
                btnBulk.disabled = true;
                btnComplete.disabled = false;
            } else {
                btnBulk.disabled = true;
                btnComplete.disabled = true;
            }

            selectCountText.innerText = checkedCount + ' terpilih';
            selectCountText.classList.remove('d-none');
        } else {
            // Re-enable all checkboxes
            checkboxes.forEach(cb => {
                cb.disabled = false;
            });
            btnBulk.disabled = true;
            btnComplete.disabled = true;
            selectCountText.classList.add('d-none');
            if (checkAll) {
                checkAll.checked = false;
            }
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            const firstActive = Array.from(checkboxes).find(cb => !cb.disabled);
            if (firstActive && checkAll.checked) {
                const targetStatus = firstActive.getAttribute('data-status');
                checkboxes.forEach(cb => {
                    if (cb.getAttribute('data-status') === targetStatus) {
                        cb.checked = true;
                    }
                });
            } else {
                checkboxes.forEach(cb => {
                    cb.checked = false;
                });
            }
            updateBulkButtonState();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateBulkButtonState();
        });
    });

    if (btnBulk) {
        btnBulk.addEventListener('click', function(e) {
            e.preventDefault();
            bulkForm.action = "{{ route('admin.shippings.bookBiteshipBulk') }}";
            bulkForm.submit();
        });
    }

    if (btnComplete) {
        btnComplete.addEventListener('click', function(e) {
            e.preventDefault();
            bulkForm.action = "{{ route('admin.shippings.completeBulk') }}";
            bulkForm.submit();
        });
    }

    // Single Complete click handler
    $(document).on('click', '.btn-complete-single', function() {
        var id = $(this).data('id');
        $('#single-complete-id').val(id);
        $('#single-complete-form').submit();
    });

    // AJAX Tracking Script
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
                                    <small class="text-muted d-block mb-0.5">Ekspedisi</small>
                                    <strong class="text-white text-uppercase">${data.courier.company}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block mb-0.5">No. Resi</small>
                                    <strong class="text-white">${data.courier.waybill_id}</strong>
                                </div>
                             </div>
                             <div class="row mt-2.5">
                                <div class="col-12">
                                    <small class="text-muted d-block mb-1">Status Terakhir</small>
                                    <span class="badge bg-primary text-uppercase px-2.5 py-1.5 rounded text-white fw-bold">${data.status}</span>
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
                                <div class="position-relative ps-4 pb-3" style="border-left: 2px solid rgba(255,255,255,0.15);">
                                    <div class="position-absolute" style="left: -7px; top: 2px; width: 12px; height: 12px; border-radius: 50%; background-color: var(--primary);"></div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">${dateStr}</small>
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

    @if(request()->has('track'))
        setTimeout(function() {
            $('.btn-track-package').first().click();
        }, 300);
    @endif
});
</script>
@endpush

<style>
    .fs-7 { font-size: 0.8rem; }
    .modal-header .close {
        color: #fff;
        opacity: 0.8;
    }
    .modal-header .close:hover {
        opacity: 1;
    }
</style>
@endsection

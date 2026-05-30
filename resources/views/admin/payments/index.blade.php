@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-white font-weight-bold">Kelola Transaksi & Pembayaran</h1>
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
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-filter me-1"></i> Filter Pembayaran</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payments.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1">Cari ID Transaksi / Invoice / Pelanggan</label>
                        <input type="text" name="search" class="form-control" placeholder="Cari nomor invoice, ID transaksi Midtrans, atau nama..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label text-muted small mb-1">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label text-muted small mb-1">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}">
                    </div>
                    <div class="col-auto d-flex gap-1 ms-auto">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary px-3"><i class="fas fa-sync-alt"></i></a>
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
                            <th>Pelanggan</th>
                            <th>Tipe Pembayaran</th>
                            <th>Bank</th>
                            <th>Midtrans ID</th>
                            <th>Nominal</th>
                            <th>Status Bayar</th>
                            <th>Tanggal</th>
                            <th class="text-end pe-4" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            @php
                                $rawStatus = strtolower($payment->payment_status);
                                $displayStatus = $rawStatus === 'settlement' ? 'paid' : $rawStatus;
                                $isPaid = in_array($displayStatus, ['paid', 'success']);
                                $isFailed = in_array($displayStatus, ['failed', 'expire', 'expired', 'deny', 'cancel']);
                                $badgeColor = $isPaid ? 'success' : ($isFailed ? 'danger' : 'warning');
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold text-white">
                                    <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-white text-decoration-none">
                                        {{ $payment->order->invoice_number ?? '-' }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold text-white">{{ $payment->order->user->name ?? '-' }}</div>
                                    <span class="text-muted small">{{ $payment->order->user->email ?? '' }}</span>
                                </td>
                                <td class="text-white">{{ strtoupper(str_replace('_', ' ', $payment->payment_type ?? '-')) }}</td>
                                <td class="text-white">{{ strtoupper($payment->bank_name ?? '-') }}</td>
                                <td><small class="text-muted text-monospace">{{ $payment->midtrans_transaction_id ?? '-' }}</small></td>
                                <td class="fw-bold text-white">Rp {{ number_format($payment->gross_amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $badgeColor }} text-white text-uppercase px-3 py-1.5 rounded-pill fs-7 fw-semibold">
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                                <td>{{ $payment->created_at->format('d M Y, H:i') }}</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-outline-primary px-2.5 py-1.5 d-inline-flex align-items-center" 
                                            style="font-size: 0.75rem; border-radius: 6px;" 
                                            data-toggle="modal" 
                                            data-target="#paymentStatusModal{{ $payment->id }}" 
                                            title="Ubah Status">
                                        <i class="fas fa-edit me-1.5"></i> Status
                                    </button>

                                    <!-- Modal Ubah Status Pembayaran -->
                                    <div class="modal fade text-start" id="paymentStatusModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form method="POST" action="{{ route('admin.payments.updateStatus', $payment->id) }}" class="w-100">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-light">
                                                        <h5 class="modal-title fw-bold text-white"><i class="fas fa-edit me-1"></i> Ubah Status Bayar</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label for="payment_status_{{ $payment->id }}" class="form-label fw-semibold text-white">Status Pembayaran</label>
                                                            <select class="form-select form-control" name="payment_status" id="payment_status_{{ $payment->id }}" required>
                                                                <option value="unpaid" {{ $rawStatus === 'unpaid' ? 'selected' : '' }}>❌ Unpaid</option>
                                                                <option value="pending" {{ $rawStatus === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                                                <option value="paid" {{ $isPaid ? 'selected' : '' }}>✅ Paid</option>
                                                                <option value="failed" {{ $isFailed ? 'selected' : '' }}>❌ Failed</option>
                                                                <option value="expired" {{ $rawStatus === 'expired' ? 'selected' : '' }}>⚠️ Expired</option>
                                                                <option value="refunded" {{ $rawStatus === 'refunded' ? 'selected' : '' }}>💰 Refunded</option>
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <div class="mb-3"><i class="fas fa-receipt fa-3x text-muted"></i></div>
                                    <p class="mb-0 fw-semibold">Tidak ditemukan data pembayaran yang cocok.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($payments->hasPages())
            <div class="card-footer py-3" style="background-color: var(--dark-sidebar); border-top: 1px solid var(--border-glass);">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .fs-7 { font-size: 0.8rem; }
</style>
@endsection

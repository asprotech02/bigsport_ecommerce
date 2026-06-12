@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-white font-weight-bold">Kelola Transaksi & Pembayaran</h1>
        </div>
        <div>
            <form action="{{ route('admin.payments.sync') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary shadow-sm fw-bold d-flex align-items-center">
                    <i class="fas fa-sync me-1.5"></i> Sync Midtrans
                </button>
            </form>
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
                            <th class="text-end pe-4" width="150">Aksi</th>
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
                                    <a href="{{ route('admin.orders.show', $payment->order_id) }}" 
                                       class="btn btn-sm btn-outline-light px-2.5 py-1.5 d-inline-flex align-items-center" 
                                       style="font-size: 0.75rem; border-radius: 6px; border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);" 
                                       title="Detail Pesanan">
                                        <i class="fas fa-eye me-1.5"></i> Detail
                                    </a>
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

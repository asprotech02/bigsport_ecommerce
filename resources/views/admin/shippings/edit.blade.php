@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pengiriman: Invoice #{{ $shipping->order->invoice_number ?? '-' }}</h1>
        <a href="{{ route('admin.shippings.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert">
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.shippings.update', $shipping->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Kurir Ekspedisi (Courier Company) <span class="text-danger">*</span></label>
                        <input type="text" name="courier_company" value="{{ old('courier_company', $shipping->courier_company) }}" class="form-control text-uppercase" required>
                        <small class="text-muted">Misal: JNE, SICEPAT, JNT</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Layanan Kurir (Courier Type) <span class="text-danger">*</span></label>
                        <input type="text" name="courier_type" value="{{ old('courier_type', $shipping->courier_type) }}" class="form-control" required>
                        <small class="text-muted">Misal: REG, YES, BEST</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nomor Resi (Tracking Number)</label>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $shipping->tracking_number) }}" class="form-control">
                        <small class="text-muted">Isi nomor resi valid agar pembeli bisa melacak pesanan.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Ongkos Kirim (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="cost" value="{{ old('cost', intval($shipping->cost)) }}" class="form-control" min="0" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Status Pesanan / Pengiriman <span class="text-danger">*</span></label>
                        <select name="order_status" class="form-select" required>
                            <option value="pending" {{ old('order_status', $shipping->order->status ?? 'pending') === 'pending' ? 'selected' : '' }}>⏳ Pending (Menunggu Pembayaran)</option>
                            <option value="confirmed" {{ old('order_status', $shipping->order->status ?? 'confirmed') === 'confirmed' ? 'selected' : '' }}>✅ Confirmed (Dibayar)</option>
                            <option value="processing" {{ old('order_status', $shipping->order->status ?? 'processing') === 'processing' ? 'selected' : '' }}>📦 Processing (Sedang Disiapkan/Dikemas)</option>
                            <option value="completed" {{ old('order_status', $shipping->order->status ?? 'completed') === 'completed' ? 'selected' : '' }}>🚚 Completed (Selesai/Terkirim)</option>
                            <option value="cancelled" {{ old('order_status', $shipping->order->status ?? 'cancelled') === 'cancelled' ? 'selected' : '' }}>❌ Cancelled (Dibatalkan)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Biteship Order ID</label>
                        <input type="text" name="biteship_order_id" value="{{ old('biteship_order_id', $shipping->biteship_order_id) }}" class="form-control bg-light">
                        <small class="text-muted">Otomatis terisi jika menggunakan plugin API pengiriman. Biarkan jika manual.</small>
                    </div>
                </div>

                <div class="text-end border-top pt-3">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">
                        <i class="fas fa-save me-1"></i> Perbarui Detail Pengiriman
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

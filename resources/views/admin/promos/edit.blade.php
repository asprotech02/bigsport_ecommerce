@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Promo: {{ $promo->code }}</h1>
        <a href="{{ route('admin.promos.index') }}" class="btn btn-secondary shadow-sm">
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
            <form action="{{ route('admin.promos.update', $promo->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Kode Promo <span class="text-danger">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $promo->code) }}" class="form-control text-uppercase" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tipe Diskon <span class="text-danger">*</span></label>
                        <select name="type" class="form-select form-control" required>
                            <option value="fixed" {{ old('type', $promo->type) == 'fixed' ? 'selected' : '' }}>Potongan Nominal (Rp)</option>
                            <option value="percentage" {{ old('type', $promo->type) == 'percentage' ? 'selected' : '' }}>Potongan Persen (%)</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Nilai Diskon (Reward) <span class="text-danger">*</span></label>
                        <input type="number" name="reward" value="{{ old('reward', intval($promo->reward)) }}" class="form-control" min="0" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Syarat Min. Belanja <span class="text-danger">*</span></label>
                        <input type="number" name="min_order_amount" value="{{ old('min_order_amount', intval($promo->min_order_amount)) }}" class="form-control" min="0" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Maksimal Pemakaian <span class="text-danger">*</span></label>
                        <input type="number" name="max_usage" value="{{ old('max_usage', $promo->max_usage) }}" class="form-control" min="1" required>
                        <small class="text-muted">Promo ini telah dipakai {{ $promo->used_count }} kali.</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Batas Waktu (Expired At)</label>
                        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $promo->expires_at ? \Carbon\Carbon::parse($promo->expires_at)->format('Y-m-d\TH:i') : '') }}" class="form-control">
                    </div>
                </div>

                <div class="mb-4 form-check form-switch pt-2">
                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" {{ old('is_active', $promo->is_active) ? 'checked' : '' }} value="1">
                    <label class="form-check-label fw-semibold" for="isActive">Promo Aktif</label>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">
                        <i class="fas fa-save me-1"></i> Perbarui Promo
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

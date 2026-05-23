@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Banner</h1>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary shadow-sm">
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
            <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tipe Banner <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="slider" {{ old('type', $banner->type) == 'slider' ? 'selected' : '' }}>Slider (Gambar Lebar di Atas)</option>
                            <option value="promo" {{ old('type', $banner->type) == 'promo' ? 'selected' : '' }}>Promo (Banner Kecil di Tengah)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Urutan (Order)</label>
                        <input type="number" name="order" value="{{ old('order', $banner->order) }}" class="form-control" min="0">
                        <small class="text-muted">Semakin kecil angkanya, semakin awal tampil (0, 1, 2...).</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Judul Utama (Title)</label>
                        <input type="text" name="title" value="{{ old('title', $banner->title) }}" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Sub-judul (Subtitle)</label>
                        <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" class="form-control">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold d-block">File Gambar Banner</label>
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Preview" class="img-thumbnail border shadow-sm" style="height: 120px; object-fit: cover;">
                        </div>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-semibold">Tautan Tujuan (Link URL)</label>
                        <input type="url" name="link_url" value="{{ old('link_url', $banner->link_url) }}" class="form-control" placeholder="https://...">
                    </div>
                </div>

                <div class="mb-4 form-check form-switch pt-2">
                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} value="1">
                    <label class="form-check-label fw-semibold" for="isActive">Tampilkan Banner (Aktif)</label>
                </div>

                <div class="text-end border-top pt-3">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

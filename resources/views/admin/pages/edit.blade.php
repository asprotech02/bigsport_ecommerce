@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Halaman Statis</h1>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary shadow-sm">
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
            <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Judul Halaman <span class="text-danger">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $page->title) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Slug URL Khusus</label>
                        <input type="text" name="slug" value="{{ old('slug', $page->slug) }}" class="form-control">
                        <small class="text-muted">Ini menentukan URL akhir (misal: website.com/kebijakan-keamanan-data).</small>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Isi Konten (HTML / Teks) <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control" rows="15" required>{{ old('content', $page->content) }}</textarea>
                </div>

                <div class="mb-4 form-check form-switch pt-2">
                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" {{ old('is_active', $page->is_active) ? 'checked' : '' }} value="1">
                    <label class="form-check-label fw-semibold" for="isActive">Publikasikan Halaman Ini</label>
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

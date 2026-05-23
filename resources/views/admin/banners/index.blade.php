@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">CMS Banner (Halaman Depan)</h1>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah Banner Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="ps-4">Preview Banner</th>
                            <th>Tipe & Urutan</th>
                            <th>Judul & Teks</th>
                            <th>Link URL</th>
                            <th>Status</th>
                            <th class="text-end pe-4" width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                            <tr>
                                <td class="ps-4">
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner" class="rounded border" style="height: 60px; width: 120px; object-fit: cover;">
                                </td>
                                <td>
                                    <span class="badge bg-{{ $banner->type === 'slider' ? 'primary' : 'info' }}">
                                        {{ strtoupper($banner->type) }}
                                    </span>
                                    <div class="text-muted small mt-1">Urutan: {{ $banner->order }}</div>
                                </td>
                                <td>
                                    <span class="fw-bold d-block">{{ $banner->title ?? '-' }}</span>
                                    <small class="text-muted">{{ $banner->subtitle ?? '-' }}</small>
                                </td>
                                <td>
                                    @if($banner->link_url)
                                        <a href="{{ $banner->link_url }}" target="_blank" class="small text-truncate d-inline-block" style="max-width: 150px;">
                                            {{ $banner->link_url }}
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $banner->is_active ? 'success' : 'danger' }}">
                                        {{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center" style="gap: 4px;">
                                        <a href="{{ route('admin.banners.edit', $banner->id) }}" 
                                           class="btn btn-sm btn-outline-warning px-2 py-1" 
                                           title="Edit Banner"
                                           style="font-size: 0.72rem; border-radius: 4px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline mb-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hapus Banner" style="font-size: 0.72rem; border-radius: 4px;" onclick="return confirm('Hapus banner ini?')">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada banner terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($banners->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $banners->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

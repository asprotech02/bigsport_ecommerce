@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">CMS Halaman Statis</h1>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Buat Halaman Baru
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
                            <th class="ps-4">Judul Halaman</th>
                            <th>Slug URL</th>
                            <th>Status</th>
                            <th>Terakhir Diperbarui</th>
                            <th class="text-end pe-4" width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $page)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $page->title }}</td>
                                <td><span class="badge bg-light text-primary border px-2 py-1">{{ $page->slug }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $page->is_active ? 'success' : 'danger' }}">
                                        {{ $page->is_active ? 'Aktif' : 'Draft' }}
                                    </span>
                                </td>
                                <td><span class="text-muted small">{{ $page->updated_at->format('d M Y, H:i') }}</span></td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center" style="gap: 4px;">
                                        <a href="{{ route('admin.pages.edit', $page->id) }}" 
                                           class="btn btn-sm btn-outline-warning px-2 py-1" 
                                           title="Edit Halaman"
                                           style="font-size: 0.72rem; border-radius: 4px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" class="d-inline mb-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hapus Halaman" style="font-size: 0.72rem; border-radius: 4px;" onclick="return confirm('Hapus halaman ini secara permanen?')">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada halaman statis yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pages->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $pages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

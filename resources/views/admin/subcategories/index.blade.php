@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Kelola Subkategori</h1>
            <p class="text-muted small mb-0">Manajemen subkategori produk untuk detail pengelompokan katalog.</p>
        </div>
        <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary shadow-sm px-4">
            <i class="fas fa-plus-circle me-2"></i> Tambah Subkategori Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="ps-4" width="80">No</th>
                            <th>Kategori Utama</th>
                            <th>Nama Subkategori</th>
                            <th>Slug URL</th>
                            <th class="pe-4 text-end" width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subcategories as $subcategory)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $subcategory->category->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $subcategory->name }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border px-2 py-1 text-monospace small">
                                    {{ $subcategory->slug }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end align-items-center" style="gap: 4px;">
                                    <a href="{{ route('admin.subcategories.edit', $subcategory->id) }}" 
                                       class="btn btn-sm btn-outline-warning px-2 py-1" 
                                       title="Edit Subkategori"
                                       style="font-size: 0.72rem; border-radius: 4px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('admin.subcategories.destroy', $subcategory->id) }}" 
                                          method="POST" 
                                          class="d-inline mb-0"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus subkategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hapus Subkategori" style="font-size: 0.72rem; border-radius: 4px;">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fas fa-box-open fa-3x text-muted"></i></div>
                                <p class="mb-0 fw-semibold">Belum ada subkategori yang ditambahkan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .fs-7 { font-size: 0.8rem; }
</style>
@endsection

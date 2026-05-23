@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Kelola Produk</h1>
            <p class="text-muted small mb-0">Manajemen katalog produk, gambar galeri, dan SKU variasi.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary shadow-sm px-4">
            <i class="fas fa-plus-circle me-2"></i> Tambah Produk Baru
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
                <table class="table table-hover align-middle mb-0" style="min-width: 1000px;">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="ps-4" width="80">No</th>
                            <th width="100">Gambar</th>
                            <th>Produk</th>
                            <th>Kategori / Brand</th>
                            <th>Gender</th>
                            <th>Harga Terendah</th>
                            <th>Total Stok</th>
                            <th class="text-center">Featured</th>
                            <th class="pe-4 text-end" width="220">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                <div class="position-relative">
                                    <img src="{{ $product->image_url }}" 
                                         alt="{{ $product->name }}" 
                                         class="rounded shadow-sm border" 
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $product->name }}</div>
                                <span class="text-muted small text-monospace">Slug: {{ $product->slug }}</span>
                            </td>
                            <td>
                                <div class="mb-1">
                                    <span class="badge bg-light text-dark border">
                                        {{ $product->category->name ?? '-' }}
                                    </span>
                                    <i class="fas fa-angle-right text-muted small mx-1"></i>
                                    <span class="text-secondary small">{{ $product->subcategory->name ?? '-' }}</span>
                                </div>
                                <div class="text-primary small fw-semibold">
                                    <i class="fas fa-tag me-1 small"></i>{{ $product->brand->name ?? '-' }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $genderBadges = [
                                        'Laki-laki' => 'bg-info text-white',
                                        'Perempuan' => 'bg-danger text-white',
                                        'Anak-anak' => 'bg-warning text-white',
                                        'Unisex' => 'bg-dark text-white'
                                    ];
                                    $badgeClass = $genderBadges[$product->gender] ?? 'bg-secondary text-white';
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2 py-1.5 rounded-pill fs-7">
                                    {{ $product->gender }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-success">
                                    Rp {{ number_format($product->lowest_price, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $totalStock = $product->total_stock;
                                @endphp
                                @if($totalStock == 0)
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2.5 py-1.5 border border-danger border-opacity-25 rounded fs-7">
                                        Habis
                                    </span>
                                @elseif($totalStock <= 5)
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-2.5 py-1.5 border border-warning border-opacity-25 rounded fs-7">
                                        Kritis ({{ $totalStock }})
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1.5 border border-success border-opacity-25 rounded fs-7">
                                        {{ $totalStock }} pcs
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($product->is_featured)
                                    <span class="badge bg-warning text-dark border border-warning-subtle shadow-sm py-1.5 px-2.5 rounded-pill" title="Unggulan">
                                        <i class="fas fa-star text-orange me-1"></i> YES
                                    </span>
                                @else
                                    <span class="badge bg-light text-secondary border py-1.5 px-2.5 rounded-pill">
                                        NO
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end align-items-center" style="gap: 4px;">
                                    <!-- Tombol Detail / Lihat di Toko -->
                                    <a href="{{ route('product.detail', $product->slug) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-info px-2 py-1" 
                                       title="Lihat di Toko"
                                       style="font-size: 0.72rem; border-radius: 4px;">
                                        <i class="fas fa-external-link-alt"></i> Detail
                                    </a>

                                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                                       class="btn btn-sm btn-outline-warning px-2 py-1" 
                                       title="Edit Produk"
                                       style="font-size: 0.72rem; border-radius: 4px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                          method="POST" 
                                          class="d-inline mb-0"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini? Semua gambar dan variasi SKU-nya juga akan terhapus permanen.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Hapus Produk" style="font-size: 0.72rem; border-radius: 4px;">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fas fa-box-open fa-3x text-muted"></i></div>
                                <p class="mb-0 fw-semibold">Belum ada produk yang ditambahkan</p>
                                <p class="small text-muted mb-0">Klik tombol "Tambah Produk Baru" untuk menambahkan produk pertama Anda.</p>
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
    .text-orange { color: #fd7e14; }
</style>
@endsection
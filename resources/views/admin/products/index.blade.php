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
                                <div class="text-white fw-semibold" style="font-size: 0.9rem;">
                                    {{ $product->category->name ?? '-' }}
                                </div>
                                <div class="text-muted small d-flex align-items-center mt-1" style="font-size: 0.75rem; gap: 4px;">
                                    <span>{{ $product->subcategory->name ?? '-' }}</span>
                                    <span class="text-secondary opacity-50">•</span>
                                    <span class="fw-semibold" style="color: var(--primary-neon, #00b4d8);">{{ $product->brand->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $genderStyles = [
                                        'Laki-laki' => 'background-color: rgba(0, 180, 216, 0.12); color: #00b4d8; border: 1px solid rgba(0, 180, 216, 0.2);',
                                        'Perempuan' => 'background-color: rgba(217, 4, 41, 0.12); color: #ff4d6d; border: 1px solid rgba(217, 4, 41, 0.2);',
                                        'Anak-anak' => 'background-color: rgba(255, 183, 3, 0.12); color: #ffb703; border: 1px solid rgba(255, 183, 3, 0.2);',
                                        'Unisex' => 'background-color: rgba(46, 196, 182, 0.12); color: #2ec4b6; border: 1px solid rgba(46, 196, 182, 0.2);'
                                    ];
                                    $defaultStyle = 'background-color: rgba(108, 117, 125, 0.12); color: #6c757d; border: 1px solid rgba(108, 117, 125, 0.2);';
                                    $currentStyle = $genderStyles[$product->gender] ?? $defaultStyle;
                                @endphp
                                <span class="badge px-3 py-1.5 rounded-pill fs-7 fw-semibold" style="{{ $currentStyle }}">
                                    {{ $product->gender }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-white" style="font-size: 0.95rem;">
                                    Rp {{ number_format($product->lowest_price, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $totalStock = $product->total_stock;
                                @endphp
                                @if($totalStock == 0)
                                    <span class="badge px-2 py-1.5 rounded fw-semibold" style="background-color: rgba(217, 4, 41, 0.12); color: #ff4d6d; border: 1px solid rgba(217, 4, 41, 0.2); font-size: 0.75rem;">
                                        <i class="fas fa-exclamation-circle me-1"></i>Habis
                                    </span>
                                @elseif($totalStock <= 5)
                                    <span class="badge px-2 py-1.5 rounded fw-semibold" style="background-color: rgba(255, 183, 3, 0.12); color: #ffb703; border: 1px solid rgba(255, 183, 3, 0.2); font-size: 0.75rem;">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Kritis ({{ $totalStock }})
                                    </span>
                                @else
                                    <span class="text-white-50 fw-semibold" style="font-size: 0.9rem;">
                                        {{ $totalStock }} <span class="small text-muted">pcs</span>
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($product->is_featured)
                                    <i class="fas fa-star text-warning" style="font-size: 1.1rem;" title="Featured Produk"></i>
                                @else
                                    <i class="far fa-star text-muted opacity-40" style="font-size: 1rem;" title="Bukan Featured"></i>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end align-items-center" style="gap: 6px;">
                                    <a href="{{ route('product.detail', $product->slug) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-light px-2.5 py-1.5 d-flex align-items-center" 
                                       title="Lihat di Toko"
                                       style="font-size: 0.75rem; border-radius: 6px; border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);">
                                        <i class="fas fa-external-link-alt me-1.5"></i> Detail
                                    </a>
 
                                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                                       class="btn btn-sm btn-outline-light px-2.5 py-1.5 d-flex align-items-center" 
                                       title="Edit Produk"
                                       style="font-size: 0.75rem; border-radius: 6px; border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);">
                                        <i class="fas fa-edit me-1.5"></i> Edit
                                    </a>
 
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                          method="POST" 
                                          class="d-inline mb-0"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini? Semua gambar dan variasi SKU-nya juga akan terhapus permanen.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2.5 py-1.5 d-flex align-items-center" title="Hapus Produk" style="font-size: 0.75rem; border-radius: 6px;">
                                            <i class="fas fa-trash-alt me-1.5"></i> Hapus
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
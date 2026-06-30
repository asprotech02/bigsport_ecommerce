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
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert" style="background-color: rgba(46, 196, 182, 0.15); color: #2ec4b6; border: 1px solid rgba(46, 196, 182, 0.3);">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert" style="background-color: rgba(230, 57, 70, 0.15); color: #e63946; border: 1px solid rgba(230, 57, 70, 0.3);">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Bulk Delete Form -->
    <form id="bulk-delete-form" action="{{ route('admin.products.deleteBulk') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua produk terpilih secara masal? Semua gambar dan variasi SKU juga akan dihapus secara permanen.')">
        @csrf
    </form>

    <div class="mb-3 d-flex align-items-center">
        <button type="submit" id="btn-bulk-delete" form="bulk-delete-form" class="btn btn-danger btn-sm d-flex align-items-center px-3 py-2" style="border-radius: 6px;" disabled>
            <i class="fas fa-trash-alt me-1.5"></i> Hapus Terpilih (Masal)
        </button>
        <span id="bulk-select-count" class="text-muted small ms-2 d-none">0 terpilih</span>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="ps-4" style="width: 45px;">
                                <input type="checkbox" id="check-all" class="form-check-input position-static m-0">
                            </th>
                            <th class="ps-2" style="width: 60px;">No</th>
                            <th width="80">Gambar</th>
                            <th>Produk</th>
                            <th>Kategori / Brand</th>
                            <th>Gender</th>
                            <th>Harga Terendah</th>
                            <th>Total Stok</th>
                            <th class="text-center">Featured</th>
                            <th class="pe-4 text-end" width="130">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="ps-4">
                                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" 
                                       class="product-checkbox form-check-input position-static m-0"
                                       form="bulk-delete-form">
                            </td>
                            <td class="ps-2 fw-bold text-secondary">
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
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-light dropdown-toggle px-2.5 py-1.5 d-flex align-items-center" 
                                            type="button" 
                                            id="actionDropdown{{ $product->id }}" 
                                            data-toggle="dropdown" 
                                            aria-haspopup="true" 
                                            aria-expanded="false"
                                            style="font-size: 0.75rem; border-radius: 6px; border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);">
                                        <i class="fas fa-cog me-1.5"></i> Aksi
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionDropdown{{ $product->id }}" style="background-color: #1e1e2d; border: 1px solid rgba(255, 255, 255, 0.15); box-shadow: 0 5px 15px rgba(0,0,0,0.5); border-radius: 8px;">
                                        <a class="dropdown-item text-white py-2" href="{{ route('product.detail', $product->slug) }}" target="_blank">
                                            <i class="fas fa-external-link-alt me-2 text-primary"></i> Lihat di Toko
                                        </a>
                                        <a class="dropdown-item text-white py-2" href="{{ route('admin.products.edit', $product->id) }}">
                                            <i class="fas fa-edit me-2 text-info"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                              method="POST" 
                                              class="d-inline mb-0"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini? Semua gambar dan variasi SKU-nya juga akan terhapus permanen.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger py-2" style="background: none; border: none; width: 100%; text-align: left;">
                                                <i class="fas fa-trash-alt me-2 text-danger"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
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
    .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
        color: #ffffff !important;
    }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('check-all');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const btnBulkDelete = document.getElementById('btn-bulk-delete');
    const selectCountText = document.getElementById('bulk-select-count');

    function updateBulkButtonState() {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        const checkedCount = checkedBoxes.length;

        if (checkedCount > 0) {
            btnBulkDelete.disabled = false;
            selectCountText.innerText = checkedCount + ' terpilih';
            selectCountText.classList.remove('d-none');
        } else {
            btnBulkDelete.disabled = true;
            selectCountText.classList.add('d-none');
            if (checkAll) {
                checkAll.checked = false;
            }
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = checkAll.checked;
            });
            updateBulkButtonState();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateBulkButtonState();
            
            // Update checkAll state based on whether all checkboxes are checked
            if (checkAll) {
                const totalCheckboxes = checkboxes.length;
                const totalChecked = document.querySelectorAll('.product-checkbox:checked').length;
                checkAll.checked = (totalCheckboxes === totalChecked);
            }
        });
    });
});
</script>
@endpush
@endsection
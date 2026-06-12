@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-white font-weight-bold">Edit Produk: {{ $product->name }}</h1>
            <p class="text-muted small mb-0">Ubah detail produk, kelola gambar galeri, dan perbarui variasi SKU.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary shadow-sm px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Error Alert Global -->
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert" style="background: rgba(229, 9, 20, 0.15); border: 1px solid rgba(229, 9, 20, 0.3) !important; color: #ff8080;">
            <i class="fas fa-exclamation-triangle me-2"></i> <strong>Gagal menyimpan perubahan:</strong>
            <ul class="mb-0 mt-2 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Hidden Trackers for Deletion -->
        <input type="hidden" name="deleted_image_ids" id="deleted_image_ids" value="">
        <input type="hidden" name="deleted_sku_ids" id="deleted_sku_ids" value="">

        <div class="row">
            <!-- KOLOM KIRI: Informasi Utama Produk -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100" style="background: var(--dark-card); border: 1px solid var(--border-glass) !important;">
                    <div class="card-header py-3" style="background: transparent; border-bottom: 1px solid var(--border-glass);">
                        <h5 class="card-title mb-0 fw-bold" style="color: var(--primary-neon); font-size: 1.05rem; letter-spacing: 0.5px;">
                            <i class="fas fa-info-circle me-1"></i> Informasi Utama
                        </h5>
                    </div>
                    <div class="card-body">
                        
                        {{-- NAMA PRODUK --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-white-50">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', $product->name) }}" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            {{-- KATEGORI --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-white-50">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- SUBKATEGORI --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-white-50">Subkategori <span class="text-danger">*</span></label>
                                <select name="subcategory_id" id="subcategory_id" class="form-select @error('subcategory_id') is-invalid @enderror" required>
                                    @foreach($subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}" 
                                                data-category="{{ $subcategory->category_id }}"
                                                {{ old('subcategory_id', $product->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                            {{ $subcategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subcategory_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- BRAND --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-white-50">Brand <span class="text-danger">*</span></label>
                                <select name="brand_id" class="form-select @error('brand_id') is-invalid @enderror" required>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" 
                                                {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- GENDER --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-white-50">Gender <span class="text-danger">*</span></label>
                                <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="Laki-laki" {{ old('gender', $product->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('gender', $product->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    <option value="Unisex" {{ old('gender', $product->gender) == 'Unisex' ? 'selected' : '' }}>Unisex</option>
                                    <option value="Anak-anak" {{ old('gender', $product->gender) == 'Anak-anak' ? 'selected' : '' }}>Anak-anak</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- BERAT PRODUK --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-white-50">Berat Produk (gram) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" 
                                       name="weight_gram" 
                                       value="{{ old('weight_gram', $product->weight_gram) }}" 
                                       class="form-control @error('weight_gram') is-invalid @enderror" 
                                       min="1" 
                                       required>
                                <span class="input-group-text" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-glass); color: var(--text-muted);">gram</span>
                            </div>
                            @error('weight_gram')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- DESKRIPSI --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-white-50">Deskripsi Produk</label>
                            <textarea name="description" 
                                      rows="5" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Tuliskan spesifikasi lengkap..."
                            >{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- FEATURED --}}
                        <div class="form-check form-switch p-3 rounded" style="background: rgba(255, 255, 255, 0.01); border: 1px solid var(--border-glass);">
                            <input type="checkbox" 
                                   name="is_featured" 
                                   value="1" 
                                   class="form-check-input ms-0 me-2" 
                                   id="featured" 
                                   {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-white" for="featured">
                                Tandai sebagai Produk Unggulan (Featured)
                            </label>
                            <div class="text-muted small ms-4">Produk ini akan tampil secara prioritas di halaman utama.</div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: Upload Gambar & Varian SKU -->
            <div class="col-lg-6 mb-4">
                <div class="d-flex flex-column gap-4 h-100">
                    
                    {{-- KELOLA GAMBAR --}}
                    <div class="card border-0 shadow-sm" style="background: var(--dark-card); border: 1px solid var(--border-glass) !important;">
                        <div class="card-header py-3" style="background: transparent; border-bottom: 1px solid var(--border-glass);">
                            <h5 class="card-title mb-0 fw-bold" style="color: var(--primary-neon); font-size: 1.05rem; letter-spacing: 0.5px;">
                                <i class="fas fa-images me-1"></i> Galeri Foto Produk
                            </h5>
                        </div>
                        <div class="card-body">
                            
                            <!-- Foto Lama -->
                            <h6 class="fw-bold text-white-50 mb-3 small text-uppercase" style="letter-spacing: 0.5px;">Foto Saat Ini:</h6>
                            <div class="row row-cols-3 g-2 mb-4" id="old-images-grid">
                                @forelse($product->images as $image)
                                    <div class="col" id="image-card-{{ $image->id }}">
                                        <div class="card preview-card h-100 text-center border p-1 rounded position-relative {{ $image->is_primary ? 'is-primary-card' : '' }}">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top rounded" style="height: 90px; object-fit: cover;">
                                            <div class="card-body p-1.5">
                                                <div class="d-flex justify-content-center align-items-center mb-1">
                                                    <input class="cursor-pointer old-primary-radio" 
                                                           type="radio" 
                                                           name="primary_image_id" 
                                                           id="old_primary_{{ $image->id }}" 
                                                           value="{{ $image->id }}" 
                                                           {{ $image->is_primary ? 'checked' : '' }} 
                                                           onchange="selectOldPrimary({{ $image->id }})"
                                                           style="width: 16px; height: 16px; accent-color: var(--primary-neon); cursor: pointer; margin: 0;">
                                                    <label class="fs-7 cursor-pointer text-white-50 fw-semibold mb-0" for="old_primary_{{ $image->id }}" style="cursor: pointer; margin-left: 6px;">
                                                        Utama
                                                    </label>
                                                </div>
                                                <button type="button" class="btn btn-link text-danger text-decoration-none p-0 fs-7 fw-bold" onclick="markImageForDeletion({{ $image->id }})">
                                                    <i class="fas fa-trash-alt me-1"></i>Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center text-muted small py-3 rounded" style="background: rgba(255, 255, 255, 0.01); border: 1px dashed var(--border-glass);">
                                        Tidak ada foto produk
                                    </div>
                                @endforelse
                            </div>

                            <!-- Upload Foto Baru -->
                            <h6 class="fw-bold text-white-50 mb-2 small text-uppercase" style="letter-spacing: 0.5px;">Tambah Foto Baru:</h6>
                            <div class="mb-3 text-center rounded p-3 position-relative" style="cursor: pointer; background: rgba(255, 255, 255, 0.01); border: 2px dashed rgba(229, 9, 20, 0.25); transition: all 0.3s ease;" id="upload-zone">
                                <i class="fas fa-plus fa-2x text-white-50 mb-1"></i>
                                <h6 class="fw-semibold text-white small">Klik / Seret Foto Baru</h6>
                                <input type="file" 
                                       name="images[]" 
                                       id="image-input" 
                                       multiple 
                                       class="d-none">
                            </div>

                            <!-- Preview Foto Baru Grid -->
                            <div id="image-preview-grid" class="row row-cols-3 g-2 mt-2"></div>
                            <div class="text-muted small mt-2 d-none" id="primary-hint">
                                <i class="fas fa-info-circle me-1"></i> Klik radio button di atas untuk memilih **Foto Baru** ini sebagai gambar utama.
                            </div>

                        </div>
                    </div>

                    {{-- DYNAMIC SKU VARIATION --}}
                    <div class="card border-0 shadow-sm flex-fill" style="background: var(--dark-card); border: 1px solid var(--border-glass) !important;">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: transparent; border-bottom: 1px solid var(--border-glass);">
                            <h5 class="card-title mb-0 fw-bold" style="color: var(--primary-neon); font-size: 1.05rem; letter-spacing: 0.5px;">
                                <i class="fas fa-boxes me-1"></i> Variasi SKU & Stok
                            </h5>
                            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="btn-add-sku">
                                <i class="fas fa-plus me-1"></i> Tambah Varian Baru
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0" id="sku-table" style="color: var(--text-pure);">
                                    <thead class="fs-7 text-uppercase text-white-50" style="background: rgba(255, 255, 255, 0.02); border-bottom: 1px solid var(--border-glass);">
                                        <tr>
                                            <th class="ps-3" width="130">Ukuran (Size)*</th>
                                            <th width="110">Warna*</th>
                                            <th width="130">Harga Dasar*</th>
                                            <th width="130">Harga Diskon</th>
                                            <th width="90">Stok*</th>
                                            <th class="text-end pe-3" width="50">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sku-tbody">
                                        @if(old('skus'))
                                            @foreach(old('skus') as $index => $oldSku)
                                                <tr class="sku-row" data-index="{{ $index }}" style="border-bottom: 1px solid rgba(255, 255, 255, 0.03);">
                                                    <!-- Keep database ID if it exists -->
                                                    <input type="hidden" name="skus[{{ $index }}][id]" value="{{ $oldSku['id'] ?? '' }}">
                                                    <td class="ps-3">
                                                        <select name="skus[{{ $index }}][size]" data-value="{{ $oldSku['size'] }}" class="form-select form-select-sm sku-size-select" required>
                                                            <option value="{{ $oldSku['size'] }}">{{ $oldSku['size'] }}</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="skus[{{ $index }}][color]" value="{{ $oldSku['color'] }}" class="form-control form-control-sm" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="skus[{{ $index }}][base_price]" value="{{ $oldSku['base_price'] }}" class="form-control form-control-sm" min="0" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="skus[{{ $index }}][discount_price]" value="{{ $oldSku['discount_price'] }}" class="form-control form-control-sm" min="0">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="skus[{{ $index }}][stock]" value="{{ $oldSku['stock'] }}" class="form-control form-control-sm" min="0" required>
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <button type="button" class="btn btn-outline-danger btn-sm border-0 btn-delete-sku" onclick="deleteSkuRow(this, '{{ $oldSku['id'] ?? '' }}')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <!-- Render dari Database -->
                                            @foreach($product->skus as $sku)
                                                <tr class="sku-row" data-index="{{ $loop->index }}" data-sku-id="{{ $sku->id }}" style="border-bottom: 1px solid rgba(255, 255, 255, 0.03);">
                                                    <input type="hidden" name="skus[{{ $loop->index }}][id]" value="{{ $sku->id }}">
                                                    <td class="ps-3">
                                                        <select name="skus[{{ $loop->index }}][size]" data-value="{{ $sku->size }}" class="form-select form-select-sm sku-size-select" required>
                                                            <option value="{{ $sku->size }}">{{ $sku->size }}</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="skus[{{ $loop->index }}][color]" value="{{ $sku->color }}" class="form-control form-control-sm" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="skus[{{ $loop->index }}][base_price]" value="{{ intval($sku->base_price) }}" class="form-control form-control-sm" min="0" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="skus[{{ $loop->index }}][discount_price]" value="{{ $sku->discount_price ? intval($sku->discount_price) : '' }}" class="form-control form-control-sm" min="0">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="skus[{{ $loop->index }}][stock]" value="{{ $sku->stock }}" class="form-control form-control-sm" min="0" required>
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <button type="button" class="btn btn-outline-danger btn-sm border-0 btn-delete-sku" onclick="deleteSkuRow(this, '{{ $sku->id }}')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- FORM ACTIONS -->
        <div class="row mt-3">
            <div class="col-12 text-end mb-5">
                <hr style="border-top: 1px solid var(--border-glass);">
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary px-4 py-2 me-2">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm fw-bold">
                    <i class="fas fa-save me-1"></i> Perbarui Produk
                </button>
            </div>
        </div>

    </form>
</div>

<style>
    .fs-7 { font-size: 0.8rem; }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
    
    .preview-card {
        transition: all 0.25s ease-in-out;
        background: var(--dark-card) !important;
        border: 1px solid var(--border-glass) !important;
    }
    .preview-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(229, 9, 20, 0.15) !important;
    }
    .preview-card.is-primary-card {
        border-color: var(--primary-neon) !important;
        background-color: rgba(229, 9, 20, 0.05) !important;
        box-shadow: 0 0 10px rgba(229, 9, 20, 0.2) !important;
    }
</style>

<script>
// Filter Subcategory Berdasarkan Kategori
document.getElementById('category_id').addEventListener('change', function () {
    let categoryId = this.value;
    let subcategorySelect = document.getElementById('subcategory_id');
    let options = subcategorySelect.querySelectorAll('option');

    options.forEach(option => {
        if(option.value === '') {
            option.style.display = 'block';
            return;
        }

        if(option.dataset.category === categoryId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });

    subcategorySelect.value = '';
});

// Dynamic Size Mapping & Dropdown Logic
function getAvailableSizes(categoryName, subcategoryName, genderName) {
    categoryName = categoryName ? categoryName.toLowerCase() : '';
    subcategoryName = subcategoryName ? subcategoryName.toLowerCase() : '';
    genderName = genderName ? genderName.toLowerCase() : '';

    if (categoryName === 'sepatu') {
        if (genderName === 'anak-anak') {
            return ['28', '29', '30', '31', '32', '33', '34', '35'];
        } else if (genderName === 'perempuan') {
            return ['36', '37', '38', '39', '40', '41'];
        } else if (genderName === 'laki-laki') {
            return ['39', '40', '41', '42', '43', '44', '45'];
        } else { // Unisex
            return ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];
        }
    } else if (categoryName === 'pakaian') {
        if (genderName === 'anak-anak') {
            return ['XS (Anak)', 'S (Anak)', 'M (Anak)', 'L (Anak)', 'XL (Anak)'];
        } else {
            return ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];
        }
    } else if (categoryName === 'aksesoris') {
        if (subcategoryName === 'kaos kaki') {
            return ['All Size', 'S', 'M', 'L'];
        } else if (subcategoryName === 'topi') {
            return ['All Size', 'S/M', 'M/L'];
        } else {
            return ['All Size'];
        }
    }
    
    // Default fallback
    return ['XS', 'S', 'M', 'L', 'XL', 'XXL', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', 'All Size'];
}

function updateAllSizeDropdowns() {
    let categorySelect = document.getElementById('category_id');
    let categoryName = categorySelect.options[categorySelect.selectedIndex]?.text.trim();
    
    let subcategorySelect = document.getElementById('subcategory_id');
    let subcategoryName = subcategorySelect.options[subcategorySelect.selectedIndex]?.text.trim();
    
    let genderSelect = document.getElementById('gender');
    let genderName = genderSelect ? genderSelect.value : '';

    let sizes = getAvailableSizes(categoryName, subcategoryName, genderName);

    document.querySelectorAll('.sku-size-select').forEach(select => {
        let currentValue = select.dataset.value || select.value;
        select.innerHTML = '';

        // Add a prompt option if no Category/Gender is selected yet
        if (!categorySelect.value || !genderSelect.value) {
            let defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- Pilih Ukuran --';
            select.appendChild(defaultOpt);
        }
        
        // If currentValue is set and not in the list, add it to prevent data loss
        if (currentValue && !sizes.includes(currentValue)) {
            let opt = document.createElement('option');
            opt.value = currentValue;
            opt.textContent = currentValue;
            select.appendChild(opt);
        }

        sizes.forEach(size => {
            let opt = document.createElement('option');
            opt.value = size;
            opt.textContent = size;
            if (size === currentValue) {
                opt.selected = true;
            }
            select.appendChild(opt);
        });

        // Retain current value
        if (currentValue && sizes.includes(currentValue)) {
            select.value = currentValue;
        }
        select.dataset.value = select.value;
    });
}

// Track manually changed size value
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('sku-size-select')) {
        e.target.dataset.value = e.target.value;
    }
});

// Update sizes when inputs change
document.getElementById('category_id').addEventListener('change', updateAllSizeDropdowns);
document.getElementById('subcategory_id').addEventListener('change', updateAllSizeDropdowns);
document.getElementById('gender').addEventListener('change', updateAllSizeDropdowns);

// Dynamic SKU Variation Builder
let skuIndex = {{ old('skus') ? count(old('skus')) : $product->skus->count() }};
let deletedSkuIds = [];

document.getElementById('btn-add-sku').addEventListener('click', function() {
    let tbody = document.getElementById('sku-tbody');
    let tr = document.createElement('tr');
    tr.className = 'sku-row';
    tr.dataset.index = skuIndex;
    tr.innerHTML = `
        <td class="ps-3">
            <select name="skus[${skuIndex}][size]" class="form-select form-select-sm sku-size-select" required></select>
        </td>
        <td>
            <input type="text" name="skus[${skuIndex}][color]" class="form-control form-control-sm" placeholder="Hitam" required>
        </td>
        <td>
            <input type="number" name="skus[${skuIndex}][base_price]" class="form-control form-control-sm" placeholder="150000" min="0" required>
        </td>
        <td>
            <input type="number" name="skus[${skuIndex}][discount_price]" class="form-control form-control-sm" placeholder="120000" min="0">
        </td>
        <td>
            <input type="number" name="skus[${skuIndex}][stock]" class="form-control form-control-sm" placeholder="10" min="0" required>
        </td>
        <td class="text-end pe-3">
            <button type="button" class="btn btn-outline-danger btn-sm border-0 btn-delete-sku" onclick="deleteSkuRow(this)">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    skuIndex++;
    updateAllSizeDropdowns();
    toggleDeleteButtons();
});

function deleteSkuRow(button, skuId) {
    if (skuId) {
        deletedSkuIds.push(skuId);
        document.getElementById('deleted_sku_ids').value = deletedSkuIds.join(',');
    }
    
    let row = button.closest('tr');
    row.remove();
    toggleDeleteButtons();
}

function toggleDeleteButtons() {
    let rows = document.querySelectorAll('.sku-row');
    rows.forEach(row => {
        let btn = row.querySelector('.btn-delete-sku');
        if (rows.length === 1) {
            btn.setAttribute('disabled', 'true');
        } else {
            btn.removeAttribute('disabled');
        }
    });
}

// Jalankan saat load awal
toggleDeleteButtons();

// Trigger filter subcategory jika ada input lama (e.g. validasi error)
document.addEventListener("DOMContentLoaded", function() {
    let catId = document.getElementById('category_id').value;
    if (catId) {
        let subcatSelect = document.getElementById('subcategory_id');
        let currentSubcatVal = subcatSelect.value;
        let options = subcatSelect.querySelectorAll('option');
        options.forEach(option => {
            if (option.value === '') return;
            if (option.dataset.category === catId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
        subcatSelect.value = currentSubcatVal;
    }
    updateAllSizeDropdowns();
});

// Hapus Gambar Lama Tracker
let deletedImageIds = [];

function markImageForDeletion(imageId) {
    if (confirm('Apakah Anda yakin ingin menghapus foto produk ini?')) {
        deletedImageIds.push(imageId);
        document.getElementById('deleted_image_ids').value = deletedImageIds.join(',');
        
        let card = document.getElementById('image-card-' + imageId);
        if (card) {
            card.remove();
        }
    }
}

// Kelola selection primary image lama
function selectOldPrimary(imageId) {
    // Clear new images primary selection radios
    let newRadios = document.querySelectorAll('.new-primary-radio');
    newRadios.forEach(radio => {
        radio.checked = false;
    });
    
    // Clear styling classes
    let cards = document.querySelectorAll('.preview-card');
    cards.forEach(card => {
        card.classList.remove('is-primary-card');
    });
    
    // Highlight selected card
    let selectedCard = document.querySelector('#image-card-' + imageId + ' .preview-card');
    if (selectedCard) {
        selectedCard.classList.add('is-primary-card');
    }
}

// Interactive Multi-Image Upload Preview & Primary Selection
let uploadZone = document.getElementById('upload-zone');
let fileInput = document.getElementById('image-input');
let selectedFiles = [];

uploadZone.addEventListener('click', () => fileInput.click());

uploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadZone.style.borderColor = '#e50914';
    uploadZone.style.background = 'rgba(229, 9, 20, 0.04)';
});

uploadZone.addEventListener('dragleave', () => {
    uploadZone.style.borderColor = 'rgba(229, 9, 20, 0.25)';
    uploadZone.style.background = 'rgba(255, 255, 255, 0.01)';
});

uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadZone.style.borderColor = 'rgba(229, 9, 20, 0.25)';
    uploadZone.style.background = 'rgba(255, 255, 255, 0.01)';
    if (e.dataTransfer.files.length > 0) {
        handleFiles(e.dataTransfer.files);
    }
});

fileInput.addEventListener('change', function(event) {
    handleFiles(event.target.files);
});

function handleFiles(files) {
    Array.from(files).forEach(file => {
        if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
            selectedFiles.push(file);
        }
    });
    renderPreviews();
    updateInputFiles();
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    renderPreviews();
    updateInputFiles();
}

function updateInputFiles() {
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
}

function renderPreviews() {
    let previewGrid = document.getElementById('image-preview-grid');
    let hint = document.getElementById('primary-hint');
    previewGrid.innerHTML = '';
    
    if (selectedFiles.length > 0) {
        hint.classList.remove('d-none');
        
        let primaryIndexInput = document.querySelector('input[name="primary_new_image_index"]:checked');
        let primaryIndex = primaryIndexInput ? parseInt(primaryIndexInput.value) : -1;
        
        selectedFiles.forEach((file, index) => {
            let reader = new FileReader();
            
            reader.onload = function(e) {
                let col = document.createElement('div');
                col.className = 'col';
                col.innerHTML = `
                    <div class="card preview-card h-100 text-center border p-1 rounded position-relative" id="new-preview-card-${index}">
                        <button type="button" class="btn btn-danger btn-xs position-absolute" style="top: 5px; right: 5px; padding: 2px 6px; font-size: 0.75rem; border-radius: 4px; z-index: 10;" onclick="removeFile(${index})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <img src="${e.target.result}" class="card-img-top rounded" style="height: 90px; object-fit: cover;">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-center align-items-center mb-0">
                                <input class="cursor-pointer new-primary-radio" 
                                       type="radio" 
                                       name="primary_new_image_index" 
                                       id="new_primary_${index}" 
                                       value="${index}" 
                                       ${index === primaryIndex ? 'checked' : ''}
                                       onchange="selectNewPrimary(${index})"
                                       style="width: 16px; height: 16px; accent-color: var(--primary-neon); cursor: pointer; margin: 0;">
                                <label class="fs-7 cursor-pointer text-white-50 fw-semibold mb-0" for="new_primary_${index}" style="cursor: pointer; margin-left: 6px;">
                                    Utama
                                </label>
                            </div>
                        </div>
                    </div>
                  `;
                previewGrid.appendChild(col);
            }
            
            reader.readAsDataURL(file);
        });
    } else {
        hint.classList.add('d-none');
    }
}

function selectNewPrimary(activeIndex) {
    // Uncheck old images primary selection radios
    let oldRadios = document.querySelectorAll('.old-primary-radio');
    oldRadios.forEach(radio => {
        radio.checked = false;
    });
    
    // Clear all is-primary-card styles first
    let cards = document.querySelectorAll('.preview-card');
    cards.forEach(card => {
        card.classList.remove('is-primary-card');
    });
    
    // Highlight the active new card
    let activeCard = document.getElementById('new-preview-card-' + activeIndex);
    if (activeCard) {
        activeCard.classList.add('is-primary-card');
    }
}
</script>
@endsection
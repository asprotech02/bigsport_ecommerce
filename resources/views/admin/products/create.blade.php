@extends('admin.layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header">
            <h5 class="mb-0">Tambah Product</h5>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.products.store') }}"
                  method="POST">

                @csrf

                {{-- CATEGORY --}}
                <div class="mb-3">
                    <label>Category</label>

                    <select name="category_id"
                            id="category_id"
                            class="form-control"
                            required>

                        <option value="">-- Pilih Category --</option>

                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- SUBCATEGORY --}}
                <div class="mb-3">
                    <label>Subcategory</label>

                    <select name="subcategory_id"
                            id="subcategory_id"
                            class="form-control"
                            required>

                        <option value="">-- Pilih Subcategory --</option>

                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}"
                                    data-category="{{ $subcategory->category_id }}">
                                {{ $subcategory->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- BRAND --}}
                <div class="mb-3">
                    <label>Brand</label>

                    <select name="brand_id"
                            class="form-control"
                            required>

                        <option value="">-- Pilih Brand --</option>

                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">
                                {{ $brand->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- GENDER --}}
                <div class="mb-3">
                    <label>Gender</label>

                    <select name="gender"
                            class="form-control"
                            required>

                        <option value="">-- Pilih Gender --</option>

                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                        <option value="Unisex">Unisex</option>
                        <option value="Anak-anak">Anak-anak</option>

                    </select>
                </div>

                {{-- PRODUCT NAME --}}
                <div class="mb-3">
                    <label>Nama Product</label>

                    <input type="text"
                           name="name"
                           class="form-control"
                           required>
                </div>

                {{-- DESCRIPTION --}}
                <div class="mb-3">
                    <label>Description</label>

                    <textarea name="description"
                              rows="5"
                              class="form-control"></textarea>
                </div>

                {{-- BASE PRICE --}}
                <div class="mb-3">
                    <label>Base Price</label>

                    <input type="number"
                           name="base_price"
                           class="form-control"
                           required>
                </div>

                {{-- DISCOUNT PRICE --}}
                <div class="mb-3">
                    <label>Discount Price</label>

                    <input type="number"
                           name="discount_price"
                           class="form-control">
                </div>

                {{-- WEIGHT --}}
                <div class="mb-3">
                    <label>Weight (gram)</label>

                    <input type="number"
                           name="weight_gram"
                           class="form-control"
                           required>
                </div>

                {{-- FEATURED --}}
                <div class="mb-3">

                    <div class="form-check">

                        <input type="checkbox"
                               name="is_featured"
                               value="1"
                               class="form-check-input"
                               id="featured">

                        <label class="form-check-label"
                               for="featured">
                            Featured Product
                        </label>

                    </div>

                </div>

                {{-- BUTTON --}}
                <button class="btn btn-primary">
                    Simpan Product
                </button>

                <a href="{{ route('admin.products.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>

    </div>

</div>

{{-- FILTER SUBCATEGORY --}}
<script>
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
</script>

@endsection
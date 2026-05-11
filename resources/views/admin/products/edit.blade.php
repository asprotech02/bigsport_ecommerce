@extends('admin.layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header">
            <h5 class="mb-0">Edit Product</h5>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.products.update', $product->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                {{-- CATEGORY --}}
                <div class="mb-3">
                    <label>Category</label>

                    <select name="category_id"
                            id="category_id"
                            class="form-control"
                            required>

                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $product->category_id == $category->id ? 'selected' : '' }}>
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

                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}"
                                    data-category="{{ $subcategory->category_id }}"
                                {{ $product->subcategory_id == $subcategory->id ? 'selected' : '' }}>
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

                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}"
                                {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
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

                        <option value="Laki-laki"
                            {{ $product->gender == 'Laki-laki' ? 'selected' : '' }}>
                            Laki-laki
                        </option>

                        <option value="Perempuan"
                            {{ $product->gender == 'Perempuan' ? 'selected' : '' }}>
                            Perempuan
                        </option>

                        <option value="Unisex"
                            {{ $product->gender == 'Unisex' ? 'selected' : '' }}>
                            Unisex
                        </option>

                        <option value="Anak-anak"
                            {{ $product->gender == 'Anak-anak' ? 'selected' : '' }}>
                            Anak-anak
                        </option>

                    </select>
                </div>

                {{-- PRODUCT NAME --}}
                <div class="mb-3">
                    <label>Nama Product</label>

                    <input type="text"
                           name="name"
                           value="{{ $product->name }}"
                           class="form-control"
                           required>
                </div>

                {{-- DESCRIPTION --}}
                <div class="mb-3">
                    <label>Description</label>

                    <textarea name="description"
                              rows="5"
                              class="form-control">{{ $product->description }}</textarea>
                </div>

                {{-- BASE PRICE --}}
                <div class="mb-3">
                    <label>Base Price</label>

                    <input type="number"
                           name="base_price"
                           value="{{ $product->base_price }}"
                           class="form-control"
                           required>
                </div>

                {{-- DISCOUNT PRICE --}}
                <div class="mb-3">
                    <label>Discount Price</label>

                    <input type="number"
                           name="discount_price"
                           value="{{ $product->discount_price }}"
                           class="form-control">
                </div>

                {{-- WEIGHT --}}
                <div class="mb-3">
                    <label>Weight (gram)</label>

                    <input type="number"
                           name="weight_gram"
                           value="{{ $product->weight_gram }}"
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
                               id="featured"
                            {{ $product->is_featured ? 'checked' : '' }}>

                        <label class="form-check-label"
                               for="featured">
                            Featured Product
                        </label>

                    </div>

                </div>

                {{-- BUTTON --}}
                <button class="btn btn-primary">
                    Update Product
                </button>

                <a href="{{ route('admin.products.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>

    </div>

</div>

<script>
document.getElementById('category_id').addEventListener('change', function () {

    let categoryId = this.value;

    let subcategorySelect = document.getElementById('subcategory_id');

    let options = subcategorySelect.querySelectorAll('option');

    options.forEach(option => {

        if(option.dataset.category === categoryId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }

    });

});
</script>

@endsection
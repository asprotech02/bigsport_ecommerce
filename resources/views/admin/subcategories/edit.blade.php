@extends('admin.layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header">
            <h5>Edit Subcategory</h5>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.subcategories.update', $subcategory->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Category</label>

                    <select name="category_id"
                            class="form-control"
                            required>

                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $subcategory->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="mb-3">
                    <label>Nama Subcategory</label>

                    <input type="text"
                           name="name"
                           value="{{ $subcategory->name }}"
                           class="form-control"
                           required>
                </div>

                <button class="btn btn-primary">
                    Update
                </button>

                <a href="{{ route('admin.subcategories.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>

    </div>

</div>

@endsection
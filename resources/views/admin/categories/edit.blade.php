@extends('admin.layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header">
            <h5 class="mb-0">Edit Category</h5>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.categories.update', $category->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label>Nama Category</label>

                    <input type="text"
                           name="name"
                           value="{{ $category->name }}"
                           class="form-control"
                           required>
                </div>

                <button class="btn btn-primary">
                    Update
                </button>

                <a href="{{ route('admin.categories.index') }}"
                   class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>

    </div>

</div>

@endsection
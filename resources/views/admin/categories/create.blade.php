@extends('admin.layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-header">
            <h5 class="mb-0">Tambah Category</h5>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.categories.store') }}" method="POST">

                @csrf

                <div class="mb-3">
                    <label>Nama Category</label>

                    <input type="text"
                           name="name"
                           class="form-control"
                           required>
                </div>

                <button class="btn btn-primary">
                    Simpan
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
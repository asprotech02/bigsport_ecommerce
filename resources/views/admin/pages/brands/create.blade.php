@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h1 class="h3 text-gray-800">
        Tambah Brand
    </h1>

    <a href="{{ route('admin.brands.index') }}"
       class="btn btn-secondary">

        Kembali
    </a>

</div>

<div class="card shadow">

    <div class="card-body">

        <form action="{{ route('admin.brands.store') }}"
              method="POST">

            @csrf

            <div class="form-group mb-3">

                <label>Nama Brand</label>

                <input type="text"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       placeholder="Masukkan nama brand">

                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <button type="submit"
                    class="btn btn-primary">

                Simpan
            </button>

        </form>

    </div>

</div>

@endsection
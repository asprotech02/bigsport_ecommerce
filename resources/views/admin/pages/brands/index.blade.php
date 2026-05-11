@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h1 class="h3 text-gray-800">
        Brand
    </h1>

    <a href="{{ route('admin.brands.create') }}"
       class="btn btn-primary">

        <i class="fas fa-plus"></i>
        Tambah Brand
    </a>

</div>

<div class="card shadow">

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">

            <table class="table table-bordered">

                <thead>

                    <tr>
                        <th width="50">No</th>
                        <th>Nama Brand</th>
                        <th>Slug</th>
                        <th width="180">Aksi</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($brands as $brand)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $brand->name }}</td>

                            <td>{{ $brand->slug }}</td>

                            <td>

                                <a href="{{ route('admin.brands.edit', $brand->id) }}"
                                   class="btn btn-warning btn-sm">

                                    Edit
                                </a>

                                <form action="{{ route('admin.brands.destroy', $brand->id) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Hapus brand ini?')">

                                        Hapus
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="text-center">
                                Belum ada data
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{ $brands->links() }}

    </div>

</div>

@endsection
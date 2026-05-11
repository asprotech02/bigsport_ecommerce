@extends('admin.layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Category</h1>

        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Category
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered">

                    <thead>
                        <tr>
                            <th width="80">No</th>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $category->name }}</td>

                            <td>{{ $category->slug }}</td>

                            <td>
                                <a href="{{ route('admin.categories.edit', $category->id) }}"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin hapus?')">
                                        Delete
                                    </button>

                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                Data kosong
                            </td>
                        </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection
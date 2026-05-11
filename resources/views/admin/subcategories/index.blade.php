@extends('admin.layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Subcategory</h1>

        <a href="{{ route('admin.subcategories.create') }}"
           class="btn btn-primary">
            Tambah Subcategory
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow">

        <div class="card-body">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Category</th>
                        <th>Subcategory</th>
                        <th>Slug</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($subcategories as $subcategory)
                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $subcategory->category->name ?? '-' }}</td>

                        <td>{{ $subcategory->name }}</td>

                        <td>{{ $subcategory->slug }}</td>

                        <td>

                            <a href="{{ route('admin.subcategories.edit', $subcategory->id) }}"
                               class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <form action="{{ route('admin.subcategories.destroy', $subcategory->id) }}"
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
                        <td colspan="5" class="text-center">
                            Data kosong
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection

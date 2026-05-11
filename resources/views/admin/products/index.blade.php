@extends('admin.layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Products</h1>

        <a href="{{ route('admin.products.create') }}"
           class="btn btn-primary">
            Tambah Product
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
                        <th>Product</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Featured</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($products as $product)
                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $product->name }}</td>

                        <td>{{ $product->category->name ?? '-' }}</td>

                        <td>{{ $product->brand->name ?? '-' }}</td>

                        <td>
                            Rp {{ number_format($product->base_price) }}
                        </td>

                        <td>
                            @if($product->is_featured)
                                <span class="badge bg-success">
                                    YES
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    NO
                                </span>
                            @endif
                        </td>

                        <td>

                            <a href="{{ route('admin.products.edit', $product->id) }}"
                               class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <form action="{{ route('admin.products.destroy', $product->id) }}"
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
                        <td colspan="7" class="text-center">
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
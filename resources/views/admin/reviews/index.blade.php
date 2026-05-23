@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Moderasi Ulasan (Reviews)</h1>
    </div>

    <!-- Filter -->
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="row align-items-center">
                <div class="col-md-auto">
                    <label class="form-label fw-bold mb-0">Filter Rating:</label>
                </div>
                <div class="col-md-3">
                    <select name="rating" class="form-select">
                        <option value="">Semua Bintang</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Bintang</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Bintang</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Bintang</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Bintang</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Bintang</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-filter me-1"></i> Terapkan</button>
                    @if(request('rating'))
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary ms-2">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Content -->
    <div class="row">
        @forelse($reviews as $review)
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold me-3" style="width: 40px; height: 40px;">
                                {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $review->user->name ?? 'User Dihapus' }}</h6>
                                <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
                            </div>
                        </div>
                        <div class="text-warning">
                            @for($i = 0; $i < $review->rating; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                            @for($i = $review->rating; $i < 5; $i++)
                                <i class="far fa-star text-secondary"></i>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="mb-3 p-3 bg-light rounded border">
                        <small class="text-muted d-block mb-1">Produk:</small>
                        <a href="{{ route('admin.products.edit', $review->product_id) }}" class="fw-semibold text-decoration-none">
                            {{ $review->product->name ?? 'Produk Dihapus' }}
                        </a>
                    </div>
                    
                    <p class="mb-3">{{ $review->comment }}</p>
                    
                    @if($review->images->count() > 0)
                        <div class="d-flex gap-2 mb-3 overflow-auto">
                            @foreach($review->images as $img)
                                <a href="{{ asset('storage/' . $img->image_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="Review Image" class="rounded border" style="width: 70px; height: 70px; object-fit: cover;">
                                </a>
                            @endforeach
                        </div>
                    @endif
                    
                    <div class="text-end border-top pt-3">
                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus ulasan ini secara permanen?')">
                                <i class="fas fa-trash-alt me-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5 text-muted bg-white shadow-sm rounded border-0">
                <i class="fas fa-comments fs-1 mb-3 text-secondary opacity-50"></i>
                <h5>Belum ada ulasan produk.</h5>
            </div>
        </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $reviews->links() }}
    </div>
</div>
@endsection

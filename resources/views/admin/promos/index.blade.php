@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white font-weight-bold">Daftar Promo / Voucher</h1>
        <a href="{{ route('admin.promos.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Buat Promo Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="ps-4">Kode Promo</th>
                            <th>Tipe</th>
                            <th>Nilai (Reward)</th>
                            <th>Min. Belanja</th>
                            <th>Pemakaian</th>
                            <th>Kadaluarsa</th>
                            <th>Status</th>
                            <th class="text-end pe-4" width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($promos as $promo)
                            <tr>
                                <td class="ps-4 fw-bold text-white">{{ $promo->code }}</td>
                                <td>
                                    <span class="badge bg-{{ $promo->type === 'percentage' ? 'info' : 'secondary' }} text-white px-3 py-1.5 rounded-pill fs-7 fw-semibold">
                                        {{ ucfirst($promo->type) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $promo->type === 'percentage' ? intval($promo->reward) . '%' : 'Rp ' . number_format($promo->reward, 0, ',', '.') }}
                                </td>
                                <td>Rp {{ number_format($promo->min_order_amount, 0, ',', '.') }}</td>
                                <td>
                                    {{ $promo->used_count }} / {{ $promo->max_usage }}
                                </td>
                                <td>
                                    @if($promo->expires_at)
                                        {{ \Carbon\Carbon::parse($promo->expires_at)->format('d M Y') }}
                                        @if(\Carbon\Carbon::parse($promo->expires_at)->isPast())
                                            <span class="text-danger small d-block">Expired</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Tanpa Batas</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $promo->is_active ? 'success' : 'danger' }} text-white px-3 py-1.5 rounded-pill fs-7 fw-semibold">
                                        {{ $promo->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center" style="gap: 6px;">
                                        <a href="{{ route('admin.promos.edit', $promo->id) }}" 
                                           class="btn btn-sm btn-outline-warning px-2.5 py-1.5 d-flex align-items-center" 
                                           title="Edit Promo"
                                           style="font-size: 0.75rem; border-radius: 6px;">
                                            <i class="fas fa-edit me-1.5"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.promos.destroy', $promo->id) }}" method="POST" class="d-inline mb-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2.5 py-1.5 d-flex align-items-center" title="Hapus Promo" style="font-size: 0.75rem; border-radius: 6px;" onclick="return confirm('Apakah Anda yakin ingin menghapus promo ini?')">
                                                <i class="fas fa-trash-alt me-1.5"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Tidak ada data promo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($promos->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $promos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

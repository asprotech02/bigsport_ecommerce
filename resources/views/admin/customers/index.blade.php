@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white font-weight-bold">Daftar Pelanggan</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="ps-4">Pelanggan</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th>Tgl Daftar</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 42px; height: 42px; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            {{ strtoupper(substr($customer->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block text-white">{{ $customer->name }}</span>
                                            <small class="text-muted">{{ $customer->role }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone_number ?? '-' }}</td>
                                <td>{{ $customer->created_at->format('d M Y') }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" 
                                       class="btn btn-sm btn-outline-primary px-2.5 py-1.5 d-inline-flex align-items-center" 
                                       style="font-size: 0.75rem; border-radius: 6px;"
                                       title="Lihat Detail & Aktivitas">
                                        <i class="fas fa-user-circle me-1.5"></i> Detail User
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada pelanggan yang mendaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($customers, 'hasPages') && $customers->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
<style>
    .fs-7 { font-size: 0.8rem; }
</style>
@endsection

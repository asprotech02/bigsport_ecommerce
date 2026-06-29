@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white font-weight-bold">Daftar Pengguna</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="ps-4">Pengguna</th>
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
                                            <span class="badge bg-secondary text-uppercase mt-1" style="font-size: 9px; letter-spacing: 0.5px;">{{ $customer->role }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone_number ?? '-' }}</td>
                                <td>{{ $customer->created_at->format('d M Y') }}</td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end align-items-center" style="gap: 6px;">
                                        <a href="{{ route('admin.customers.show', $customer->id) }}" 
                                           class="btn btn-sm btn-outline-light px-2.5 py-1.5 d-inline-flex align-items-center" 
                                           style="font-size: 0.75rem; border-radius: 6px; border-color: rgba(255,255,255,0.15); color: rgba(255,255,255,0.8);"
                                           title="Lihat Detail & Aktivitas">
                                            <i class="fas fa-user-circle me-1.5"></i> Detail User
                                        </a>

                                        @if(auth()->id() !== $customer->id)
                                            <form id="delete-form-{{ $customer->id }}" action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline mb-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" 
                                                        onclick="confirmDeleteUser({{ $customer->id }})"
                                                        class="btn btn-sm btn-outline-danger px-2.5 py-1.5 d-inline-flex align-items-center" 
                                                        style="font-size: 0.75rem; border-radius: 6px;" 
                                                        title="Hapus Pengguna">
                                                    <i class="fas fa-trash-alt me-1.5"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada pengguna yang terdaftar.</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDeleteUser(userId) {
        Swal.fire({
            title: 'Hapus Pengguna?',
            text: "Apakah Anda yakin ingin menghapus pengguna ini? Semua data terkait seperti alamat, pesanan, dan ulasan juga akan ikut terhapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'YA, HAPUS',
            cancelButtonText: 'BATAL',
            customClass: {
                confirmButton: 'btn btn-danger fw-bold text-uppercase rounded-0 px-4 py-2 me-2',
                cancelButton: 'btn btn-outline-dark fw-bold text-uppercase rounded-0 px-4 py-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + userId).submit();
            }
        });
    }
</script>
@endpush
@endsection

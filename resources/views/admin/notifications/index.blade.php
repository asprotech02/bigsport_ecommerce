@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white font-weight-bold">Riwayat Pesan Notifikasi</h1>
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-paper-plane me-1"></i> Kirim Notifikasi Baru
        </a>
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
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Tipe</th>
                            <th>Penerima</th>
                            <th>Judul & Pesan</th>
                            <th>Status Baca</th>
                            <th>Tanggal Dikirim</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notif)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-{{ $notif->type === 'promo' ? 'info' : ($notif->type === 'system' ? 'warning text-dark' : 'primary') }} text-white px-3 py-1.5 rounded-pill fs-7 fw-semibold">
                                        {{ strtoupper($notif->type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-white">{{ $notif->user->name ?? 'Semua Pengguna' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block text-white">{{ $notif->title }}</span>
                                    <small class="text-muted text-wrap d-inline-block text-truncate" style="max-width: 300px;">
                                        {{ $notif->message }}
                                    </small>
                                </td>
                                <td>
                                    @if($notif->is_read)
                                        <span class="text-success small fw-semibold"><i class="fas fa-check-double me-1"></i> Dibaca</span>
                                    @else
                                        <span class="text-muted small"><i class="fas fa-check me-1"></i> Terkirim</span>
                                    @endif
                                </td>
                                <td>{{ $notif->created_at->format('d M Y, H:i') }}</td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('admin.notifications.destroy', $notif->id) }}" method="POST" class="d-inline mb-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2.5 py-1.5 d-inline-flex align-items-center" style="font-size: 0.75rem; border-radius: 6px;" title="Hapus" onclick="return confirm('Hapus pesan ini dari riwayat?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat notifikasi yang dikirim.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($notifications->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kirim Notifikasi Baru</h1>
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert">
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.notifications.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Target Penerima <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            <option value="" selected disabled>-- Pilih Penerima --</option>
                            <option value="all" class="fw-bold text-primary">📣 BROADCAST: Semua Pelanggan</option>
                            <optgroup label="Pelanggan Individu">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tipe Pesan <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="transaction" {{ old('type') == 'transaction' ? 'selected' : '' }}>Transaksi (Update Pesanan/Pengiriman)</option>
                            <option value="promo" {{ old('type') == 'promo' ? 'selected' : '' }}>Promo & Diskon (Voucher/Deals)</option>
                            <option value="system" {{ old('type') == 'system' ? 'selected' : '' }}>Sistem (Pengumuman Akun/Perbaikan)</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">Judul Notifikasi <span class="text-danger">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="Misal: Promo Akhir Tahun! Diskon 50% untuk Sepatu Lari" required maxlength="255">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-semibold">Isi Pesan <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="4" placeholder="Tulis deskripsi atau pesan lengkap di sini..." required>{{ old('message') }}</textarea>
                    </div>
                </div>

                <div class="text-end border-top pt-3">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm fw-bold">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Pesan Sekarang
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

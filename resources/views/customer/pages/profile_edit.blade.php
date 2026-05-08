@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh; display: flex; align-items: center;">
        <div class="container d-flex justify-content-center">
            
            <div class="auth-card w-100" style="max-width: 600px;">
                <h3 class="fw-bold mb-5 text-uppercase" style="letter-spacing: 0.5px;">EDIT DETAIL PENGGUNA</h3>

                {{-- 🌟 FIX 1: Arahkan action form ke rute update --}}
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH') {{-- Wajib pakai PATCH untuk update data --}}
                    
                    <div class="mb-4">
                        <label class="form-label auth-label">Nama Pengguna <span class="text-danger">*</span></label>
                        {{-- 🌟 FIX 2: Tambahkan atribut name & value dinamis --}}
                        <input type="text" name="name" class="form-control rounded-0 shadow-none auth-input" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label auth-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="birthday" class="form-control rounded-0 shadow-none auth-input" value="{{ old('birthday', $user->birthday) }}" required>
                    </div>

                    <div class="mb-5">
                        <label class="form-label auth-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select rounded-0 shadow-none auth-input" required>
                            <option value="L" {{ old('gender', $user->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $user->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 mb-4 rounded-0 fw-bold text-uppercase" style="padding: 12px; letter-spacing: 1px;">SIMPAN PERUBAHAN</button>
                    
                    {{-- Tombol Batal --}}
                    <!-- <div class="text-center">
                        <a href="{{ route('profile') }}" class="text-secondary text-decoration-none fw-bold" style="font-size: 13px;">
                            <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
                        </a>
                    </div> -->
                </form>
            </div>

        </div>
    </section>

    @include('customer.components.footer')
    @include('customer.components.chatbot')
@endsection
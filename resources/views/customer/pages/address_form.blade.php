@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh; display: flex; align-items: center;">
        <div class="container d-flex justify-content-center">
            <div class="auth-card w-100" style="max-width: 650px;">
                {{-- JUDUL DINAMIS --}}
                <h3 class="fw-bold mb-5 text-uppercase text-center" style="letter-spacing: 0.5px;">
                    {{ isset($address) ? 'Edit Alamat' : 'Tambah Alamat' }}
                </h3>

                {{-- ACTION DINAMIS --}}
                <form action="{{ isset($address) ? route('address.update', $address->id) : route('address.store') }}" method="POST" id="address-form">
                    @csrf
                    @if(isset($address))
                        @method('PUT')
                    @endif

                    @foreach(session('selected_cart_ids', []) as $id)
                        <input type="hidden" name="cart_ids[]" value="{{ $id }}">
                    @endforeach

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label auth-label">Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" name="receiver_name" class="form-control rounded-0 shadow-none auth-input" value="{{ old('receiver_name', $address->receiver_name ?? '') }}" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label auth-label">Nomor HP <span class="text-danger">*</span></label>
                            <input type="text" name="receiver_phone" class="form-control rounded-0 shadow-none auth-input" value="{{ old('receiver_phone', $address->receiver_phone ?? '') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label auth-label">Provinsi <span class="text-danger">*</span></label>
                            <select name="province_id" id="province_id" class="form-select rounded-0 shadow-none auth-input" style="height: 45px;" required>
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label auth-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                            <select name="city_id" id="city_id" class="form-select rounded-0 shadow-none auth-input" style="height: 45px;" required disabled>
                                <option value="">-- Pilih Kota/Kab. --</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label auth-label">Kecamatan <span class="text-danger">*</span></label>
                            <select name="district_id" id="district_id" class="form-select rounded-0 shadow-none auth-input" style="height: 45px;" required disabled>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label auth-label">Kelurahan/Desa <span class="text-danger">*</span></label>
                            <select name="village_id" id="village_id" class="form-select rounded-0 shadow-none auth-input" style="height: 45px;" required disabled>
                                <option value="">-- Pilih Kelurahan/Desa --</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label auth-label">Kode Pos <span class="text-danger">*</span></label>
                        <input type="number" name="postal_code" id="postal_code" class="form-control rounded-0 shadow-none auth-input" value="{{ old('postal_code', $address->postal_code ?? '') }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label auth-label">Detail Titik Alamat <span class="text-danger">*</span></label>
                        <textarea name="full_address" class="form-control rounded-0 shadow-none auth-input" style="height: 100px; resize: none;" required>{{ old('full_address', $address->full_address ?? '') }}</textarea>
                    </div>

                    <div class="form-check mb-5">
                        <input type="checkbox" name="is_default" id="is_default" class="form-check-input border-dark rounded-0 shadow-none cursor-pointer" style="width: 18px; height: 18px;" value="1" {{ (old('is_default', $address->is_default ?? 0) == 1) ? 'checked' : '' }}>
                        <label class="form-check-label ms-2 mt-1" for="is_default" style="font-size: 14px;">
                            Jadikan sebagai alamat utama
                        </label>
                    </div>

                    <button type="submit" class="btn btn-black w-100 py-3 text-uppercase fw-bold mb-3" style="letter-spacing: 1px;">
                        {{ isset($address) ? 'UPDATE ALAMAT' : 'SIMPAN ALAMAT' }}
                    </button>
                    
                    <div class="text-center">
                        <a href="{{ route('profile', ['tab' => 'alamat']) }}" class="text-secondary text-decoration-none fw-bold" style="font-size: 13px;">
                            <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @include('customer.components.footer')
    @include('customer.components.chatbot')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinceSelect = document.getElementById('province_id');
        const citySelect = document.getElementById('city_id');
        const districtSelect = document.getElementById('district_id');
        const villageSelect = document.getElementById('village_id');

        // Data dari Backend (Jika Mode Edit)
        const oldData = {
            province: "{{ $address->province_id ?? '' }}",
            city: "{{ $address->city_id ?? '' }}",
            district: "{{ $address->district_id ?? '' }}",
            village: "{{ $address->village_id ?? '' }}"
        };

        // --- 1. LOAD PROVINSI ---
        axios.get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
            .then(response => {
                let options = '<option value="">-- Pilih Provinsi --</option>';
                response.data.forEach(prov => {
                    options += `<option value="${prov.id}" data-name="${prov.name}" ${oldData.province == prov.id ? 'selected' : ''}>${prov.name}</option>`;
                });
                provinceSelect.innerHTML = options;
                
                // Jika edit, trigger load kota otomatis
                if(oldData.province) provinceSelect.dispatchEvent(new Event('change'));
            });

        // --- 2. LOAD KOTA ---
        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            if (!provinceId) return;

            citySelect.disabled = true;
            axios.get(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`)
                .then(response => {
                    let options = '<option value="">-- Pilih Kota/Kab. --</option>';
                    response.data.forEach(city => {
                        options += `<option value="${city.id}" data-name="${city.name}" ${oldData.city == city.id ? 'selected' : ''}>${city.name}</option>`;
                    });
                    citySelect.innerHTML = options;
                    citySelect.disabled = false;
                    if(oldData.city) citySelect.dispatchEvent(new Event('change'));
                });
        });

        // --- 3. LOAD KECAMATAN ---
        citySelect.addEventListener('change', function() {
            const cityId = this.value;
            if (!cityId) return;

            districtSelect.disabled = true;
            axios.get(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${cityId}.json`)
                .then(response => {
                    let options = '<option value="">-- Pilih Kecamatan --</option>';
                    response.data.forEach(dist => {
                        options += `<option value="${dist.id}" data-name="${dist.name}" ${oldData.district == dist.id ? 'selected' : ''}>${dist.name}</option>`;
                    });
                    districtSelect.innerHTML = options;
                    districtSelect.disabled = false;
                    if(oldData.district) districtSelect.dispatchEvent(new Event('change'));
                });
        });

        // --- 4. LOAD KELURAHAN ---
        districtSelect.addEventListener('change', function() {
            const districtId = this.value;
            if (!districtId) return;

            villageSelect.disabled = true;
            axios.get(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`)
                .then(response => {
                    let options = '<option value="">-- Pilih Kelurahan/Desa --</option>';
                    response.data.forEach(vill => {
                        options += `<option value="${vill.id}" data-name="${vill.name}" ${oldData.village == vill.id ? 'selected' : ''}>${vill.name}</option>`;
                    });
                    villageSelect.innerHTML = options;
                    villageSelect.disabled = false;
                });
        });

        // --- 5. HIDDEN INPUTS & BITESIP NAMES ---
        document.getElementById('address-form').addEventListener('submit', function(e) {
            const createHidden = (name, value) => {
                let input = document.querySelector(`input[name="${name}"]`) || document.createElement('input');
                input.type = 'hidden'; input.name = name; input.value = value;
                if (!input.parentElement) this.appendChild(input);
            };
            const getSelectedName = (sel) => sel.options[sel.selectedIndex]?.getAttribute('data-name');

            if(provinceSelect.value) createHidden('province_name', getSelectedName(provinceSelect));
            if(citySelect.value) createHidden('city_name', getSelectedName(citySelect));
            if(districtSelect.value) createHidden('district_name', getSelectedName(districtSelect));
            if(villageSelect.value) createHidden('village_name', getSelectedName(villageSelect));
        });
    });
</script>
@endpush
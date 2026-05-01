@extends('customer.layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        @include('customer.components.topbar')
        @include('customer.components.navbar')
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh; display: flex; align-items: center;">
        <div class="container d-flex justify-content-center">
            
            <div class="auth-card w-100" style="max-width: 650px;">
                <h3 class="fw-bold mb-5 text-uppercase text-center" style="letter-spacing: 0.5px;">Tambah Alamat</h3>

                <form action="{{ route('address.store') }}" method="POST" id="address-form">
                    @csrf
                    {{-- PERBAIKAN DISINI: Pakai @endforeach, bukan @endhtml --}}
                    @foreach(session('selected_cart_ids', []) as $id)
                        <input type="hidden" name="cart_ids[]" value="{{ $id }}">
                    @endforeach
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label auth-label">Nama Penerima <span class="text-danger">*</span></label>
                            <input type="text" name="receiver_name" class="form-control rounded-0 shadow-none auth-input" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label auth-label">Nomor HP <span class="text-danger">*</span></label>
                            <input type="text" name="receiver_phone" class="form-control rounded-0 shadow-none auth-input" required>
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
                        {{-- TAMBAHAN DROPDOWN KELURAHAN --}}
                        <div class="col-md-6 mb-4">
                            <label class="form-label auth-label">Kelurahan/Desa <span class="text-danger">*</span></label>
                            <select name="village_id" id="village_id" class="form-select rounded-0 shadow-none auth-input" style="height: 45px;" required disabled>
                                <option value="">-- Pilih Kelurahan/Desa --</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label auth-label">Kode Pos <span class="text-danger">*</span></label>
                        <input type="number" name="postal_code" id="postal_code" class="form-control rounded-0 shadow-none auth-input" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label auth-label">Detail Titik Alamat <span class="text-danger">*</span></label>
                        <textarea name="full_address" class="form-control rounded-0 shadow-none auth-input" style="height: 100px; resize: none;" required></textarea>
                    </div>

                    <div class="form-check mb-5">
                        <input type="checkbox" name="is_default" id="is_default" class="form-check-input border-dark rounded-0 shadow-none cursor-pointer" style="width: 18px; height: 18px;" value="1">
                        <label class="form-check-label ms-2 mt-1" for="is_default" style="font-size: 14px;">
                            Jadikan sebagai alamat utama
                        </label>
                    </div>

                    <button type="submit" class="btn btn-black w-100 py-3 text-uppercase fw-bold mb-3" style="letter-spacing: 1px;">SIMPAN ALAMAT</button>
                    
                    <div class="text-center">
                        <a href="{{ route('checkout') }}" class="text-secondary text-decoration-none fw-bold" style="font-size: 13px;">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Checkout
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @include('customer.components.footer')
    @include('customer.components.chatbot')

    <style>
        .form-check-input:checked { background-color: #000 !important; border-color: #000 !important; }
        .form-check-input:focus { border-color: #000 !important; box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.25) !important; }
        .auth-input:focus { border-color: #000; box-shadow: none; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('province_id');
            const citySelect = document.getElementById('city_id');
            const districtSelect = document.getElementById('district_id');
            const villageSelect = document.getElementById('village_id');

            // --- 1. LOAD PROVINSI ---
            axios.get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                .then(response => {
                    let options = '<option value="">-- Pilih Provinsi --</option>';
                    response.data.forEach(prov => {
                        options += `<option value="${prov.id}" data-name="${prov.name}">${prov.name}</option>`;
                    });
                    provinceSelect.innerHTML = options;
                });

            // --- 2. LOAD KOTA ---
            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                citySelect.innerHTML = '<option value="">Loading...</option>';
                citySelect.disabled = true;
                districtSelect.disabled = true;
                villageSelect.disabled = true;

                if (provinceId) {
                    axios.get(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`)
                        .then(response => {
                            let options = '<option value="">-- Pilih Kota/Kab. --</option>';
                            response.data.forEach(city => {
                                options += `<option value="${city.id}" data-name="${city.name}">${city.name}</option>`;
                            });
                            citySelect.innerHTML = options;
                            citySelect.disabled = false;
                        });
                }
            });

            // --- 3. LOAD KECAMATAN ---
            citySelect.addEventListener('change', function() {
                const cityId = this.value;
                districtSelect.innerHTML = '<option value="">Loading...</option>';
                districtSelect.disabled = true;
                villageSelect.disabled = true;

                if (cityId) {
                    axios.get(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${cityId}.json`)
                        .then(response => {
                            let options = '<option value="">-- Pilih Kecamatan --</option>';
                            response.data.forEach(dist => {
                                options += `<option value="${dist.id}" data-name="${dist.name}">${dist.name}</option>`;
                            });
                            districtSelect.innerHTML = options;
                            districtSelect.disabled = false;
                        });
                }
            });

            // --- 4. LOAD KELURAHAN ---
            districtSelect.addEventListener('change', function() {
                const districtId = this.value;
                villageSelect.innerHTML = '<option value="">Loading...</option>';
                villageSelect.disabled = true;

                if (districtId) {
                    axios.get(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${districtId}.json`)
                        .then(response => {
                            let options = '<option value="">-- Pilih Kelurahan/Desa --</option>';
                            response.data.forEach(vill => {
                                options += `<option value="${vill.id}" data-name="${vill.name}">${vill.name}</option>`;
                            });
                            villageSelect.innerHTML = options;
                            villageSelect.disabled = false;
                        });
                }
            });

            // --- 5. HIDDEN INPUTS UNTUK NAMA WILAYAH ---
            document.getElementById('address-form').addEventListener('submit', function(e) {
                const createHidden = (name, value) => {
                    let input = document.querySelector(`input[name="${name}"]`) || document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    if (!input.parentElement) this.appendChild(input);
                };

                const getSelectedName = (sel) => sel.options[sel.selectedIndex].getAttribute('data-name');

                if(provinceSelect.value) createHidden('province_name', getSelectedName(provinceSelect));
                if(citySelect.value) createHidden('city_name', getSelectedName(citySelect));
                if(districtSelect.value) createHidden('district_name', getSelectedName(districtSelect));
                if(villageSelect.value) createHidden('village_name', getSelectedName(villageSelect));
            });
        });
    </script>
@endsection
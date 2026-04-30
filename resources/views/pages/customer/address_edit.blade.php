@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        <x-customer.topbar />
        <x-customer.navbar />
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh; display: flex; align-items: center;">
        <div class="container d-flex justify-content-center">
            
            <div class="auth-card w-100" style="max-width: 650px;">
                <h3 class="fw-bold mb-5 text-uppercase text-center" style="letter-spacing: 0.5px;">Tambah Alamat</h3>

                <form action="{{ route('address.store') }}" method="POST" id="address-form">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label auth-label">Nama Penerima <span class="text-danger">*</span></label>
                            {{-- Ganti name jadi receiver_name --}}
                            <input type="text" name="receiver_name" class="form-control rounded-0 shadow-none auth-input" placeholder="Misal: John Doe" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label auth-label">Nomor HP <span class="text-danger">*</span></label>
                            {{-- Ganti name jadi receiver_phone --}}
                            <input type="text" name="receiver_phone" class="form-control rounded-0 shadow-none auth-input" placeholder="Misal: 08123456789" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label auth-label">Provinsi <span class="text-danger">*</span></label>
                        <select name="province_id" id="province_id" class="form-select rounded-0 shadow-none auth-input" style="height: 45px;" required>
                            <option value="">-- Pilih Provinsi --</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label auth-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                            <select name="city_id" id="city_id" class="form-select rounded-0 shadow-none auth-input" style="height: 45px;" required disabled>
                                <option value="">-- Pilih Kota/Kab. --</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label auth-label">Kecamatan <span class="text-danger">*</span></label>
                            <select name="district_id" id="district_id" class="form-select rounded-0 shadow-none auth-input" style="height: 45px;" required disabled>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label auth-label">Kode Pos <span class="text-danger">*</span></label>
                        <input type="number" name="postal_code" id="postal_code" class="form-control rounded-0 shadow-none auth-input" placeholder="Contoh: 15310" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label auth-label">Detail Titik Alamat <span class="text-danger">*</span></label>
                        {{-- Ganti name jadi full_address --}}
                        <textarea name="full_address" class="form-control rounded-0 shadow-none auth-input" style="height: 100px; resize: none;" placeholder="Nama jalan, Gedung, No. Rumah, RT/RW..." required></textarea>
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

    <x-customer.footer />
    <x-customer.chatbot />

    <style>
        .form-check-input:checked {
            background-color: #000 !important;
            border-color: #000 !important;
        }
        .form-check-input:focus {
            border-color: #000 !important;
            box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.25) !important;
        }
        .auth-input:focus {
            border-color: #000;
            box-shadow: none;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provinceSelect = document.getElementById('province_id');
            const citySelect = document.getElementById('city_id');
            const districtSelect = document.getElementById('district_id');

            // --- 1. LOAD PROVINSI SAAT HALAMAN DIBUKA ---
            axios.get('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                .then(response => {
                    let provinces = response.data;
                    let options = '<option value="">-- Pilih Provinsi --</option>';
                    provinces.forEach(prov => {
                        options += `<option value="${prov.id}" data-name="${prov.name}">${prov.name}</option>`;
                    });
                    provinceSelect.innerHTML = options;
                })
                .catch(error => {
                    console.error("Gagal load provinsi:", error);
                    provinceSelect.innerHTML = '<option value="">Gagal mengambil data</option>';
                    alert('Gagal memuat data provinsi. Pastikan koneksi internet stabil.');
                });

            // --- 2. LOAD KOTA SAAT PROVINSI DIPILIH ---
            provinceSelect.addEventListener('change', function() {
                const provinceId = this.value;
                citySelect.innerHTML = '<option value="">Loading Kota...</option>';
                districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                citySelect.disabled = true;
                districtSelect.disabled = true;

                if (provinceId) {
                    axios.get(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`)
                        .then(response => {
                            let cities = response.data;
                            let options = '<option value="">-- Pilih Kota/Kab. --</option>';
                            cities.forEach(city => {
                                options += `<option value="${city.id}" data-name="${city.name}">${city.name}</option>`;
                            });
                            citySelect.innerHTML = options;
                            citySelect.disabled = false;
                        })
                        .catch(error => {
                            console.error("Gagal load kota:", error);
                            citySelect.innerHTML = '<option value="">Gagal mengambil data</option>';
                        });
                } else {
                    citySelect.innerHTML = '<option value="">-- Pilih Kota/Kab. --</option>';
                }
            });

            // --- 3. LOAD KECAMATAN SAAT KOTA DIPILIH ---
            citySelect.addEventListener('change', function() {
                const cityId = this.value;
                districtSelect.innerHTML = '<option value="">Loading Kecamatan...</option>';
                districtSelect.disabled = true;

                if (cityId) {
                    axios.get(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${cityId}.json`)
                        .then(response => {
                            let districts = response.data;
                            let options = '<option value="">-- Pilih Kecamatan --</option>';
                            districts.forEach(district => {
                                options += `<option value="${district.id}" data-name="${district.name}">${district.name}</option>`;
                            });
                            districtSelect.innerHTML = options;
                            districtSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error("Gagal load kecamatan:", error);
                            districtSelect.innerHTML = '<option value="">Gagal mengambil data</option>';
                        });
                } else {
                    districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                }
            });

            // --- 4. SEBELUM SUBMIT FORM ---
            document.getElementById('address-form').addEventListener('submit', function(e) {
                const selectedProv = provinceSelect.options[provinceSelect.selectedIndex];
                const selectedCity = citySelect.options[citySelect.selectedIndex];
                const selectedDist = districtSelect.options[districtSelect.selectedIndex];

                const createHiddenInput = (name, value) => {
                    let existingInput = document.querySelector(`input[name="${name}"]`);
                    if (existingInput) {
                        existingInput.value = value;
                    } else {
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = name;
                        input.value = value;
                        this.appendChild(input);
                    }
                };

                if(selectedProv.value) createHiddenInput('province_name', selectedProv.getAttribute('data-name'));
                if(selectedCity.value) createHiddenInput('city_name', selectedCity.getAttribute('data-name'));
                if(selectedDist.value) createHiddenInput('district_name', selectedDist.getAttribute('data-name'));
            });
        });
    </script>
@endsection
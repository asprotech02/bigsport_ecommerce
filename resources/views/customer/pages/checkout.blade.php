@extends('customer.layouts.app')

{{-- 1. BUNGKUS CSS PAKAI PUSH STYLES --}}
@push('styles')
<style>
    .bg-light-gray { background-color: #f9f9f9; }
    .form-check-input:checked { background-color: #000 !important; border-color: #000 !important; }
    .sticky-summary { position: -webkit-sticky; position: sticky; top: 120px; z-index: 10; }
    .selectable-card { border: 1px solid #dee2e6 !important; transition: all 0.2s ease-in-out; cursor: pointer; position: relative; }
    .selectable-card:hover { border-color: #000 !important; }
    .selectable-card.active { border: 2px solid #000 !important; background-color: #fff !important; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .text-sale { color: #dc3545; }
    .ratio-1x1 { aspect-ratio: 1 / 1; overflow: hidden; }
    
    @keyframes shake {
        0% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        50% { transform: translateX(5px); }
        75% { transform: translateX(-5px); }
        100% { transform: translateX(0); }
    }
</style>
@endpush

@section('content')
<header class="sticky-header-custom border-bottom">
    @include('customer.components.topbar')
    @include('customer.components.navbar')
</header>

<section class="py-5 bg-white" style="min-height: 70vh;">
    <div class="container-fluid px-4 px-lg-5" style="max-width: 1400px;">
        <div class="text-center mb-5">
            <h3 class="fw-bold text-uppercase m-0" style="font-size: 30px; font-weight: 800; letter-spacing: 1px;">Checkout</h3>
        </div>

        <form action="#" method="POST" id="checkout-form">
            @csrf
            @foreach($cartItems as $item)
                <input type="hidden" name="cart_ids[]" class="checkout-cart-id" value="{{ $item->id }}">
            @endforeach
            <input type="hidden" id="base-subtotal" value="{{ $subtotal }}">
            <input type="hidden" name="shipping_cost" id="input-shipping-cost" value="0">
            <input type="hidden" name="courier_company" id="input-courier-company" value="">
            <input type="hidden" name="courier_type" id="input-courier-type" value="">
            
            {{-- Nilai statis toko utama --}}
            <input type="hidden" name="store_location" id="input-store-location" value="toko_utama">

            <div class="row g-5">
                <div class="col-12 col-lg-7 col-xl-8">
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 text-uppercase" style="font-size: 14px; letter-spacing: 1px;">Info Penerima</h5>
                            <button type="button" class="btn btn-link text-dark fw-bold text-decoration-none p-0 shadow-none" style="font-size: 13px; text-decoration: underline !important;" data-bs-toggle="modal" data-bs-target="#modalPilihAlamat">Ubah Alamat</button>
                        </div>
                        
                        <div id="selected-address-container">
                            @if($defaultAddress)
                                <div class="border p-4 rounded-0 bg-light-gray shadow-sm">
                                    <input type="hidden" name="address_id" id="address-id" value="{{ $defaultAddress->id }}">
                                    <p class="fw-bold mb-1" style="font-size: 15px;">
                                        {{ $defaultAddress->receiver_name }} 
                                        <span class="fw-normal text-secondary ms-2">({{ $defaultAddress->receiver_phone }})</span>
                                    </p>
                                    <p class="text-secondary mb-0 small" style="line-height: 1.6;">
                                        {{ $defaultAddress->full_address }},<br>
                                        {{ $defaultAddress->village_name }}, {{ $defaultAddress->district_name }}, {{ $defaultAddress->city_name }}, {{ $defaultAddress->province_name }}, {{ $defaultAddress->postal_code }}
                                    </p>
                                </div>
                            @else
                                <div class="border p-4 text-center">
                                    <p class="text-secondary mb-3">Anda belum memiliki alamat pengiriman.</p>
                                    <a href="{{ route('address_form') }}" class="btn btn-outline-dark btn-sm rounded-0 fw-bold">TAMBAH ALAMAT</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="fw-bold mb-3 text-uppercase" style="font-size: 14px; letter-spacing: 1px;">Produk Dipesan</h5>
                        <div class="border p-0 rounded-0">
                            @foreach($cartItems as $item)
                                @php
                                    $sku = $item->productSku;
                                    $product = $sku->product;
                                    $price = $sku->discount_price ?? $sku->base_price;
                                    $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                @endphp
                                <div class="d-flex align-items-start p-4 {{ !$loop->last ? 'border-bottom border-secondary-subtle' : '' }}">
                                    <div class="ratio-1x1" style="width: 100px; flex-shrink: 0;">
                                        <img src="{{ asset('storage/' . $primaryImage->image_path) }}" class="w-100 h-100 object-fit-cover bg-light">
                                    </div>
                                    <div class="ms-4 flex-grow-1">
                                        <p class="fw-bold mb-1 text-uppercase text-dark" style="font-size: 14px;">{{ $product->brand->name }}</p>
                                        <p class="fw-bold mb-1 text-uppercase" style="font-size: 15px;">{{ $product->name }}</p>
                                        
                                        <p class="text-secondary mb-2 small">Gender: {{ $product->gender }} | Ukuran: {{ $sku->size }} @if($sku->color) | Warna: {{ $sku->color }} @endif</p>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="fw-bold text-dark fs-6">Rp {{ number_format($price, 0, ',', '.') }}</span>
                                            <span class="text-dark fw-bold small">x{{ $item->quantity }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-bold mb-3 text-uppercase" style="font-size: 14px; letter-spacing: 1px;">Metode Pengiriman</h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="selectable-card p-3 mb-2 d-block rounded-0 delivery-type-card text-center active" for="tipe_delivery">
                                    <input type="radio" name="delivery_type" id="tipe_delivery" value="delivery" class="d-none delivery-type-radio" checked>
                                    <i class="bi bi-truck fs-4 d-block mb-1"></i>
                                    <span class="fw-bold d-block text-uppercase" style="font-size: 12px;">Kirim ke Rumah</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="selectable-card p-3 mb-2 d-block rounded-0 delivery-type-card text-center" for="tipe_pickup">
                                    <input type="radio" name="delivery_type" id="tipe_pickup" value="pickup" class="d-none delivery-type-radio">
                                    <i class="bi bi-shop fs-4 d-block mb-1"></i>
                                    <span class="fw-bold d-block text-uppercase" style="font-size: 12px;">Ambil di Toko</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5" id="courier-section">
                        <h5 class="fw-bold mb-3 text-uppercase text-secondary" style="font-size: 12px; letter-spacing: 1px;">Pilih Ekspedisi</h5>
                        <div id="shipping-options-container">
                            @if($defaultAddress)
                                <div class="border p-4 text-center text-secondary rounded-0 bg-light-gray shadow-sm small">
                                    <div class="spinner-border spinner-border-sm me-2 text-dark"></div> Menghitung ongkos kirim...
                                </div>
                            @else
                                <div class="border border-secondary-subtle p-3 text-center text-danger rounded-0 bg-light-gray shadow-sm small fw-bold">
                                    <i class="bi bi-exclamation-circle me-1"></i> Silakan tambah alamat pengiriman terlebih dahulu.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-5 d-none" id="pickup-section">
                        <h5 class="fw-bold mb-3 text-uppercase text-secondary" style="font-size: 12px; letter-spacing: 1px;">Lokasi Pengambilan</h5>
                        <div class="border p-4 rounded-0 bg-light-gray shadow-sm border-dark" style="border-width: 2px !important;">
                            <div class="d-flex align-items-center">
                                <div class="bg-black text-white p-3 me-3 text-center flex-shrink-0" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-shop fs-4"></i>
                                </div>
                                <div>
                                    <p class="fw-bold mb-1" style="font-size: 14px;">Bagindo Jaya Tangerang (Toko Utama)</p>
                                    <p class="text-secondary mb-0 small" style="line-height: 1.5;">
                                        Jl. HOS Cokroaminoto No.52, RT.001/RW.005, Larangan, Kec. Larangan, Kota Tangerang
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p class="text-secondary mt-2 small"><i class="bi bi-info-circle me-1"></i> Pembayaran dilakukan sekarang, tunjukkan QR Code ke kasir saat mengambil barang</p>
                    </div>
                </div>

                <div class="col-12 col-lg-5 col-xl-4 align-self-start">
                    <div class="bg-light-gray p-4 rounded-0 sticky-summary shadow-sm">
                        <h5 class="fw-bold mb-4 text-uppercase" style="letter-spacing: 1px;">Ringkasan Belanja</h5>
                        <div class="d-flex justify-content-between mb-3 small">
                            <span>Subtotal ({{ $cartItems->sum('quantity') }} Barang)</span>
                            <span class="fw-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 small">
                            <span>Ongkos Kirim</span>
                            <span class="fw-bold text-dark" id="display-shipping-cost">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 small">
                            <span>Diskon</span>
                            <span class="fw-bold text-sale" id="display-discount">- Rp 0</span>
                        </div>
                        <hr class="border-secondary-subtle">
                        <div class="d-flex justify-content-between mb-4 align-items-center">
                            <span class="fw-bold text-uppercase fs-6">Total Tagihan</span>
                            <span class="fw-bold fs-4 text-dark" id="display-grand-total">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="mb-4">
                            <label class="text-secondary mb-2 small fw-bold text-uppercase">Kode Promo</label>
                            <div class="input-group">
                                <input type="text" id="promo-input" class="form-control rounded-0 border-dark shadow-none">
                                <button id="apply-promo-btn" class="btn btn-black rounded-0 fw-bold px-3" type="button">PAKAI</button>
                            </div>
                            <small id="promo-message" class="d-block mt-1"></small>
                        </div>
                        
                        <div id="checkout-error-message" class="text-danger fw-bold text-center mb-3" style="display: none; font-size: 12px; letter-spacing: 0.5px;"></div>

                        <button type="submit" class="btn btn-black w-100 py-3 fw-bold text-uppercase shadow-sm">BAYAR SEKARANG</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<div class="modal fade" id="modalPilihAlamat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-0 border-dark">
            <div class="modal-header border-bottom border-dark rounded-0">
                <h5 class="modal-title fw-bold text-uppercase" style="font-size: 15px; letter-spacing: 1px;">Pilih Alamat Pemesan</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                @forelse($allAddresses as $address)
                    <div class="p-4 border-bottom border-secondary-subtle address-option {{ $defaultAddress && $defaultAddress->id == $address->id ? 'bg-light' : '' }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <p class="fw-bold mb-0" style="font-size: 15px;">{{ $address->receiver_name }}</p>
                            
                            <button type="button" class="btn btn-outline-dark btn-sm rounded-0 fw-bold px-3 py-1 btn-pilih-alamat {{ $defaultAddress && $defaultAddress->id == $address->id ? 'd-none' : '' }}" 
                                data-id="{{ $address->id }}"
                                data-name="{{ $address->receiver_name }}"
                                data-phone="{{ $address->receiver_phone }}"
                                data-street="{{ $address->full_address }}"
                                data-region="{{ $address->village_name }}, {{ $address->district_name }}, {{ $address->city_name }}, {{ $address->province_name }}, {{ $address->postal_code }}">PILIH</button>
                            
                            <span class="text-success fw-bold text-terpilih {{ $defaultAddress && $defaultAddress->id == $address->id ? '' : 'd-none' }}" style="font-size: 12px;"><i class="bi bi-check-circle-fill"></i> Terpilih</span>
                        </div>
                        <p class="text-secondary mb-1 small">{{ $address->receiver_phone }}</p>
                        <p class="text-secondary mb-0 small">
                            {{ $address->full_address }}, {{ $address->village_name }}, {{ $address->district_name }}, {{ $address->city_name }}, {{ $address->province_name }}, {{ $address->postal_code }}
                        </p>
                    </div>
                @empty
                    <div class="p-5 text-center"><p class="text-secondary">Belum ada alamat.</p></div>
                @endforelse
            </div>
            <div class="modal-footer border-top border-dark rounded-0">
                <a href="{{ route('address_form') }}" class="btn btn-black w-100 rounded-0 fw-bold text-uppercase py-2">Tambah Alamat Baru</a>
            </div>
        </div>
    </div>
</div>

@include('customer.components.footer')


{{-- 2. BUNGKUS JS PAKAI PUSH SCRIPTS --}}
@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;

    const displayShippingCost = document.getElementById('display-shipping-cost');
    const displayDiscount = document.getElementById('display-discount');
    const displayGrandTotal = document.getElementById('display-grand-total');
    const shippingContainer = document.getElementById('shipping-options-container');
    const courierSection = document.getElementById('courier-section');
    const pickupSection = document.getElementById('pickup-section');
    const baseSubtotal = parseInt(document.getElementById('base-subtotal').value);

    let currentDiscount = 0;

    const formatRupiah = (num) => "Rp " + num.toLocaleString('id-ID');

    function refreshTotals() {
        const shippingPrice = parseInt(document.getElementById('input-shipping-cost').value) || 0;
        const grandTotal = (baseSubtotal + shippingPrice) - currentDiscount;
        displayGrandTotal.innerText = formatRupiah(grandTotal > 0 ? grandTotal : 0);
    }

    // --- 1. HANDLING PILIH ALAMAT ---
    function applySelectedAddress(btn, isInitialLoad = false) {
        const id = btn.dataset.id;
        const name = btn.dataset.name;
        const phone = btn.dataset.phone;
        const street = btn.dataset.street;
        const region = btn.dataset.region;

        document.getElementById('selected-address-container').innerHTML = `
            <div class="border p-4 rounded-0 bg-light-gray shadow-sm">
                <input type="hidden" name="address_id" id="address-id" value="${id}">
                <p class="fw-bold mb-1" style="font-size: 15px;">${name} <span class="fw-normal text-secondary ms-2">(${phone})</span></p>
                <p class="text-secondary mb-0 small" style="line-height: 1.6;">
                    ${street},<br>${region}
                </p>
            </div>`;

        document.querySelectorAll('.address-option').forEach(opt => {
            opt.classList.remove('bg-light');
            opt.querySelector('.btn-pilih-alamat').classList.remove('d-none');
            opt.querySelector('.text-terpilih').classList.add('d-none');
        });

        const selectedOption = btn.closest('.address-option');
        selectedOption.classList.add('bg-light');
        btn.classList.add('d-none');
        selectedOption.querySelector('.text-terpilih').classList.remove('d-none');

        sessionStorage.setItem('checkout_saved_address_id', id);

        if (!isInitialLoad) {
            const modalEl = document.getElementById('modalPilihAlamat');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
        }

        fetchShippingRates(id);
    }

    document.querySelectorAll('.btn-pilih-alamat').forEach(btn => {
        btn.addEventListener('click', function() {
            applySelectedAddress(this, false);
        });
    });

    // --- 2. HANDLING TIPE PENGIRIMAN ---
    document.querySelectorAll('.delivery-type-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.delivery-type-card').forEach(c => c.classList.remove('active'));
            this.closest('.delivery-type-card').classList.add('active');

            if (this.value === 'pickup') {
                courierSection.classList.add('d-none');
                pickupSection.classList.remove('d-none');
                document.getElementById('input-shipping-cost').value = 0;
                displayShippingCost.innerHTML = '<span class="text-success fw-bold">Gratis</span>';
                refreshTotals();
            } else {
                courierSection.classList.remove('d-none');
                pickupSection.classList.add('d-none');
                const addrId = document.getElementById('address-id')?.value;
                if(addrId) fetchShippingRates(addrId);
            }
        });
    });

    // --- 3. HANDLING KURIR & AUTO-SELECT ---
    function fetchShippingRates(addressId) {
        shippingContainer.innerHTML = '<div class="border p-4 text-center small bg-light-gray shadow-sm"><div class="spinner-border spinner-border-sm me-2"></div>Menghitung...</div>';
        let cartIds = [];
        document.querySelectorAll('.checkout-cart-id').forEach(i => cartIds.push(i.value));

        axios.post('{{ route("checkout.check-shipping") }}', { address_id: addressId, cart_ids: cartIds })
            .then(response => {
                if (response.data.success && response.data.rates.length > 0) {
                    let html = '';
                    response.data.rates.forEach((rate, i) => {
                        html += `
                        <label class="selectable-card p-3 mb-2 d-block rounded-0 bg-light-gray shadow-sm shipping-option-card" for="courier_${i}">
                            <input type="radio" name="courier_service" id="courier_${i}" class="d-none courier-radio" value="${rate.company}_${rate.type}" data-price="${rate.price}" data-company="${rate.company}" data-type="${rate.type}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div><span class="fw-bold text-uppercase d-block text-dark small">${rate.company} - ${rate.type}</span>
                                <span class="text-secondary small">Estimasi ${rate.shipment_duration_range} hari</span></div>
                                <span class="fw-bold text-dark">${formatRupiah(rate.price)}</span>
                            </div>
                        </label>`;
                    });
                    shippingContainer.innerHTML = html;

                    const firstCourierRadio = shippingContainer.querySelector('.courier-radio');
                    if (firstCourierRadio) {
                        firstCourierRadio.checked = true;
                        firstCourierRadio.closest('.shipping-option-card').classList.add('active');
                        document.getElementById('input-shipping-cost').value = firstCourierRadio.dataset.price;
                        document.getElementById('input-courier-company').value = firstCourierRadio.dataset.company;
                        document.getElementById('input-courier-type').value = firstCourierRadio.dataset.type;
                        displayShippingCost.innerText = formatRupiah(parseInt(firstCourierRadio.dataset.price));
                        refreshTotals();
                    }

                    document.querySelectorAll('.courier-radio').forEach(r => {
                        r.addEventListener('change', function() {
                            document.querySelectorAll('.shipping-option-card').forEach(c => c.classList.remove('active'));
                            this.closest('.shipping-option-card').classList.add('active');
                            document.getElementById('input-shipping-cost').value = this.dataset.price;
                            document.getElementById('input-courier-company').value = this.dataset.company;
                            document.getElementById('input-courier-type').value = this.dataset.type;
                            displayShippingCost.innerText = formatRupiah(parseInt(this.dataset.price));
                            refreshTotals();
                        });
                    });
                } else {
                    console.error("Biteship Error Response:", response.data);
                    let errorMsg = response.data.message || 'Kurir tidak tersedia untuk wilayah ini.';
                    shippingContainer.innerHTML = `<div class="border border-secondary-subtle p-3 text-center text-danger rounded-0 bg-light-gray shadow-sm small fw-bold"><i class="bi bi-exclamation-circle me-1"></i> ${errorMsg}</div>`;
                }
            });
    }

    // --- 4. HANDLING PROMO CODE ---
    const promoBtn = document.getElementById('apply-promo-btn');
    if (promoBtn) {
        promoBtn.addEventListener('click', function() {
            const code = document.getElementById('promo-input').value;
            const msg = document.getElementById('promo-message');
            if(!code) return;

            promoBtn.disabled = true;
            promoBtn.innerHTML = '...';

            axios.post('{{ route("checkout.promo") }}', { promo_code: code, subtotal: baseSubtotal })
                .then(res => {
                    if(res.data.success) {
                        currentDiscount = parseInt(res.data.discount_amount);
                        displayDiscount.innerText = "- " + formatRupiah(currentDiscount);
                        msg.innerText = res.data.message;
                        msg.className = "text-success d-block mt-1 small";
                        refreshTotals();
                    } else {
                        msg.innerText = res.data.message;
                        msg.className = "text-danger d-block mt-1 small";
                        currentDiscount = 0;
                        displayDiscount.innerText = "- Rp 0";
                        refreshTotals();
                    }
                })
                .catch(err => {
                    msg.innerText = "Terjadi kesalahan sistem";
                    msg.className = "text-danger d-block mt-1 small";
                })
                .finally(() => { 
                    promoBtn.disabled = false; 
                    promoBtn.innerHTML = 'PAKAI';
                });
        });
    }

    // --- 5. HANDLING SUBMIT CHECKOUT & MIDTRANS SNAP ---
    const checkoutForm = document.getElementById('checkout-form');
    const errorMsgBox = document.getElementById('checkout-error-message');

    function showCheckoutError(message) {
        errorMsgBox.innerText = message;
        errorMsgBox.style.display = 'block';
        errorMsgBox.style.animation = 'shake 0.4s';
        setTimeout(() => errorMsgBox.style.animation = '', 400);
    }

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            errorMsgBox.style.display = 'none';

            // Validate courier selection if delivery_type is delivery
            const deliveryType = document.querySelector('input[name="delivery_type"]:checked')?.value;
            if (deliveryType === 'delivery') {
                const courierCompany = document.getElementById('input-courier-company').value;
                const courierType = document.getElementById('input-courier-type').value;
                if (!courierCompany || !courierType) {
                    showCheckoutError('Silakan pilih layanan kurir pengiriman terlebih dahulu.');
                    return;
                }
            }

            const btnSubmit = this.querySelector('button[type="submit"]');
            const originalText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> MEMPROSES...';
            btnSubmit.disabled = true;

            const formData = new FormData(this);
            const promoInput = document.getElementById('promo-input').value;
            if(promoInput) {
                formData.append('promo_code', promoInput);
            }

            axios.post('{{ route("checkout.process") }}', formData)
                .then(response => {
                    if (response.data.success) {
                        if(response.data.is_free) {
                            window.location.href = "{{ route('order_success') }}?order_id=" + response.data.invoice;
                            return;
                        }

                        window.snap.pay(response.data.snap_token, {
                            onSuccess: function(result){
                                sessionStorage.removeItem('checkout_saved_address_id');
                                window.location.href = "{{ route('order_success') }}?order_id=" + response.data.invoice;
                            },
                            onPending: function(result){
                                sessionStorage.removeItem('checkout_saved_address_id');
                                window.location.href = "{{ route('order') }}"; 
                            },
                            onError: function(result){
                                showCheckoutError('Pembayaran gagal atau ditolak. Silakan coba metode lain.');
                                btnSubmit.innerHTML = originalText;
                                btnSubmit.disabled = false;
                            },
                            onClose: function(){
                                sessionStorage.removeItem('checkout_saved_address_id');
                                window.location.href = "{{ route('order') }}"; 
                            }
                        });
                    } else {
                        showCheckoutError(response.data.message || 'Terjadi kesalahan sistem.');
                        btnSubmit.innerHTML = originalText;
                        btnSubmit.disabled = false;
                    }
                })
                .catch(error => {
                    console.error("FULL ERROR:", error);
                    let errorMsg = 'Gagal terhubung ke server. Periksa koneksi internet Anda.';

                    if (error.response) {
                        errorMsg = `Server Error (${error.response.status}): Cek tab Console (F12) untuk detailnya.`;
                        console.error("PESAN DARI LARAVEL:", error.response.data);
                    }

                    showCheckoutError(errorMsg);
                    btnSubmit.innerHTML = originalText;
                    btnSubmit.disabled = false;
                });
        });
    }

    // --- CEK MEMORI SAAT HALAMAN DI-REFRESH / BARU DIBUKA ---
    const savedAddressId = sessionStorage.getItem('checkout_saved_address_id');

    if (savedAddressId) {
        const savedBtn = document.querySelector(`.btn-pilih-alamat[data-id="${savedAddressId}"]`);
        
        if (savedBtn) {
            applySelectedAddress(savedBtn, true); 
        } else {
            const initAddr = document.getElementById('address-id')?.value;
            if(initAddr) fetchShippingRates(initAddr);
        }
    } else {
        const initAddr = document.getElementById('address-id')?.value;
        if(initAddr) fetchShippingRates(initAddr);
    }

});
</script>
@endpush
@endsection
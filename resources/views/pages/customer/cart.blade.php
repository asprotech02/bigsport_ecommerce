@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom border-bottom">
        <x-customer.topbar />
        <x-customer.navbar />
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh;">
        <div class="container-fluid px-4 px-lg-5" style="max-width: 1400px;">
            
            <div class="mb-5">
                <h3 class="fw-bold text-uppercase m-0 text-center" style="letter-spacing: 1.5px;">Keranjang Belanja</h3>
            </div>

            <div class="w-100">
                <form action="{{ route('checkout') }}" method="GET" id="checkout-form">
                
                    @if($cartItems->count() > 0)
                    <div class="d-none d-md-flex fw-bold border-bottom border-dark pb-3 mb-4 text-uppercase align-items-center" style="font-size: 13px; letter-spacing: 1px;">
                        <div class="col-1 text-center">
                            <input type="checkbox" id="select-all" class="form-check-input border-dark rounded-0 shadow-none cursor-pointer" style="width: 18px; height: 18px;">
                        </div>
                        <div class="col-4">Item</div>
                        <div class="col-3 text-center">Harga</div>
                        <div class="col-2 text-center">Jumlah</div>
                        <div class="col-2 text-end">Subtotal</div>
                    </div>
                    @endif

                    @forelse($cartItems as $item)
                        @php
                            $product = $item->productSku->product;
                            $isDiscount = $product->discount_price && $product->discount_price < $product->base_price;
                            $price = $isDiscount ? $product->discount_price : $product->base_price;
                            $subtotal = $price * $item->quantity;
                            
                            $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                            $imagePath = $primaryImage ? asset('storage/' . $primaryImage->image_path) : asset('assets/images/default.jpg');
                        @endphp

                        <div class="row align-items-center border-bottom pb-4 mb-4 m-0 cart-item-row" data-price="{{ $price }}">
                            
                            <div class="col-1 col-md-1 text-center px-0">
                                <input type="checkbox" name="cart_ids[]" value="{{ $item->id }}" class="form-check-input border-dark rounded-0 shadow-none cursor-pointer item-checkbox" style="width: 18px; height: 18px;">
                            </div>

                            <div class="col-11 col-md-4 d-flex mb-4 mb-md-0 px-0">
                                <div class="cart-img-wrapper me-3 me-md-4">
                                    <img src="{{ $imagePath }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover; display: block; background-color: #f8f9fa;">
                                </div>
                                <div>
                                    <div class="fw-bold text-uppercase mb-1" style="font-size: 14px;">{{ $product->brand->name ?? 'Brand' }}</div>
                                    <div class="fw-bold text-dark mb-1" style="font-size: 15px;">{{ $product->name }}</div>
                                    <div class="text-secondary" style="font-size: 12px;">{{ $product->gender }}</div>
                                    <div class="text-secondary mb-2" style="font-size: 12px;">Ukuran: {{ $item->productSku->size }}</div>
                                    
                                    <button type="button" onclick="deleteItem('{{ $item->id }}')" class="btn btn-link p-0 text-danger fw-bold text-decoration-none text-uppercase shadow-none" style="font-size: 11px; letter-spacing: 1px;">Hapus</button>
                                </div>
                            </div>

                            <div class="col-12 col-md-3 mb-3 mb-md-0 d-flex justify-content-between justify-content-md-center align-items-center px-0">
                                <span class="d-md-none fw-bold text-secondary text-uppercase" style="font-size: 12px;">Harga</span>
                                <div class="text-md-center">
                                    @if($isDiscount)
                                        <div class="d-flex flex-column align-items-md-center">
                                            <span class="fw-bold text-danger" style="font-size: 15px;">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                                            <div class="d-flex gap-2 align-items-center" style="font-size: 12px;">
                                                <span class="text-secondary text-decoration-line-through">Rp {{ number_format($product->base_price, 0, ',', '.') }}</span>
                                                <span class="badge bg-danger rounded-0" style="font-size: 10px;">{{ round((($product->base_price - $product->discount_price) / $product->base_price) * 100) }}%</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="fw-bold" style="font-size: 15px;">Rp {{ number_format($product->base_price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-md-2 mb-3 mb-md-0 d-flex justify-content-between justify-content-md-center align-items-center px-0">
                                <span class="d-md-none fw-bold text-secondary text-uppercase" style="font-size: 12px;">Jumlah</span>
                                <div class="border border-dark d-flex align-items-center" style="height: 35px;">
                                    {{-- Tambahkan keyword 'this' agar JS tau tombol mana yang diklik --}}
                                    <button type="button" class="btn border-0 rounded-0 fw-bold px-3 py-0 shadow-none text-dark btn-minus" onclick="updateQty('{{ $item->id }}', -1, this)">-</button>
                                    <input type="text" value="{{ $item->quantity }}" class="border-0 text-center fw-bold text-dark p-0 qty-input" style="width: 30px; outline: none; background: transparent;" readonly>
                                    <button type="button" class="btn border-0 rounded-0 fw-bold px-3 py-0 shadow-none text-dark btn-plus" onclick="updateQty('{{ $item->id }}', 1, this)">+</button>
                                </div>
                            </div>

                            <div class="col-12 col-md-2 d-flex justify-content-between justify-content-md-end align-items-center px-0">
                                <span class="d-md-none fw-bold text-secondary text-uppercase" style="font-size: 12px;">Subtotal</span>
                                {{-- Tambahkan class 'item-subtotal' --}}
                                <span class="fw-bold fs-6 item-subtotal">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>

                        </div>
                    @empty
                        <div class="text-center py-5 mb-4 border-bottom w-100">
                            <i class="bi bi-cart-x text-secondary mb-3 d-block" style="font-size: 3rem; opacity: 0.5;"></i>
                            <h5 class="fw-bold text-uppercase mb-2">Keranjang Anda Kosong</h5>
                            <p class="text-secondary mb-4" style="font-size: 14px;">Belum ada produk yang Anda sukai</p>
                            <a href="{{ route('product.index') }}" class="btn btn-black px-4 py-2 fw-bold text-uppercase" style="font-size: 12px; letter-spacing: 1px;">Belanja Sekarang</a>
                        </div>
                    @endforelse

                    @if($cartItems->count() > 0)
                        <div class="row justify-content-end mt-5 m-0">
                            <div class="col-12 col-md-5 col-lg-4 px-0">
                                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-dark">
                                    <span class="fw-bold text-uppercase" style="font-size: 14px; letter-spacing: 1px;">Total Terpilih</span>
                                    <span class="fw-bold fs-4" id="grand-total">Rp 0</span>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" id="btn-checkout" class="btn btn-black px-4 py-3 fw-bold text-uppercase d-flex align-items-center justify-content-center gap-2 w-100 disabled" style="letter-spacing: 1px; font-size: 13px;">
                                        LANJUT CHECKOUT
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </form>

                {{-- Form Delete (Biar aman hapus item tetep pakai cara biasa/reload) --}}
                <form id="delete-form" action="{{ route('cart.destroy', 'ID_PLACEHOLDER') }}" method="POST" style="display:none;">
                    @csrf @method('DELETE')
                </form>

            </div>
        </div>
    </section>

    <x-customer.footer />
    <x-customer.chatbot />
    
    <style>
        /* Mengubah warna checkbox saat dicentang menjadi hitam */
        .form-check-input:checked {
            background-color: #000 !important;
            border-color: #000 !important;
        }

        /* Mengubah warna garis luar saat checkbox diklik/fokus */
        .form-check-input:focus {
            border-color: #000 !important;
            /* box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.25) !important; */
        }
    </style>
    {{-- Import Axios untuk AJAX --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Setup CSRF Token Laravel untuk Axios
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if(csrfToken) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        } else {
            console.warn("CSRF meta tag tidak ditemukan. Tambahkan <meta name='csrf-token' content='{{ csrf_token() }}'> di head layout.app lo.");
        }

        // Deklarasi global biar bisa dipanggil tombol onclick
        let calculateTotalGlobal;

        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const grandTotalEl = document.getElementById('grand-total');
            const btnCheckout = document.getElementById('btn-checkout');

            calculateTotalGlobal = function() {
                let total = 0;
                let checkedCount = 0;
                
                document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                    const row = cb.closest('.cart-item-row');
                    const price = parseInt(row.dataset.price);
                    const qty = parseInt(row.querySelector('.qty-input').value);
                    total += price * qty;
                    checkedCount++;
                });

                grandTotalEl.innerText = 'Rp ' + total.toLocaleString('id-ID');
                
                if(checkedCount > 0) {
                    btnCheckout.classList.remove('disabled');
                } else {
                    btnCheckout.classList.add('disabled');
                }
            }

            if(selectAll) {
                selectAll.addEventListener('change', function() {
                    itemCheckboxes.forEach(cb => cb.checked = this.checked);
                    calculateTotalGlobal();
                });
            }

            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const allChecked = document.querySelectorAll('.item-checkbox:checked').length === itemCheckboxes.length;
                    selectAll.checked = allChecked;
                    calculateTotalGlobal();
                });
            });

            calculateTotalGlobal();
        });

        // FUNGSI AJAX UPDATE QTY TANPA RELOAD
        function updateQty(id, delta, btnElement) {
            const row = btnElement.closest('.cart-item-row');
            const input = row.querySelector('.qty-input');
            const subtotalText = row.querySelector('.item-subtotal');
            
            let currentQty = parseInt(input.value);
            let newQty = currentQty + delta;
            
            if(newQty >= 1) {
                // Kasih efek redup biar keliatan lagi loading
                input.style.opacity = '0.5';

                axios.patch(`/cart/${id}`, {
                    quantity: newQty
                })
                .then(response => {
                    if(response.data.success) {
                        // 1. Update angka jumlah di kotak
                        input.value = response.data.new_qty;
                        // 2. Update harga subtotal di pinggir kanan
                        subtotalText.innerText = response.data.item_subtotal;
                        // 3. Update total akhir belanjaan
                        if(typeof calculateTotalGlobal === 'function') {
                            calculateTotalGlobal();
                        }
                    }
                })
                .catch(error => {
                    console.error("Error AJAX:", error);
                    alert('Gagal update qty. Pastikan CSRF Token aman.');
                })
                .finally(() => {
                    // Balikin terang lagi
                    input.style.opacity = '1';
                });
            }
        }

        // Hapus item (tetap pakai form submit biasa biar barisnya hilang sempurna)
        function deleteItem(id) {
            if(confirm('Hapus produk dari keranjang?')) {
                const form = document.getElementById('delete-form');
                form.action = form.action.replace('ID_PLACEHOLDER', id);
                form.submit();
            }
        }
    </script>
@endsection
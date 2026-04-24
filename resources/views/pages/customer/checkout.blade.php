@extends('layouts.app')

@section('content')
    <header class="sticky-header-custom">
        <x-customer.topbar />
        <x-customer.navbar />
    </header>

    <section class="py-5 bg-white" style="min-height: 70vh;">
        <div class="container">
            
            <div class="text-center mb-5">
                <h3 class="fw-bold text-uppercase m-0" style="font-size: 30px; font-weight: 800px; letter-spacing: 1px;">Checkout</h3>
            </div>

            <form action="#" method="POST">
                @csrf
                <div class="row g-5">
                    
                    <div class="col-12 col-lg-7 col-xl-8">
                        
                        <div class="mb-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold m-0 text-uppercase" style="font-size: 14px; letter-spacing: 1px;">Alamat Pengiriman</h5>
                                <a href="{{ route('address_edit') }}" class="text-dark fw-bold text-decoration-none" style="font-size: 13px; text-decoration: underline !important;">Ubah Alamat</a>
                            </div>
                            <div class="border p-4 rounded-0">
                                <p class="fw-bold mb-1" style="font-size: 15px;">John Doe <span class="fw-normal text-secondary ms-2">(0812-3456-7890)</span></p>
                                <p class="text-secondary mb-0" style="font-size: 14px; line-height: 1.6;">
                                    Jl. Sudirman No. 123, Komplek Bumi Indah Blok C2,<br>
                                    Kecamatan Setiabudi, Jakarta Selatan,<br>
                                    DKI Jakarta, 12920
                                </p>
                            </div>
                        </div>

                        <div class="mb-5">
                            <h5 class="fw-bold mb-3 text-uppercase" style="font-size: 14px; letter-spacing: 1px;">Produk Dipesan</h5>
                            <div class="border p-4 rounded-0">
                                
                                <div class="d-flex align-items-start mb-4 pb-4 border-bottom border-secondary-subtle">
                                    <div class="ratio ratio-1x1" style="width: 120px; flex-shrink: 0;">
                                        <img src="https://images.unsplash.com/photo-1518002171953-a080ee817e1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Produk" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div class="ms-4 flex-grow-1">
                                        <p class="fw-bold mb-1 text-uppercase" style="font-size: 15px;">ADISTAR CONTROL 5 UNISEX SNEAKERS</p>
                                        <p class="text-secondary mb-2" style="font-size: 13px;">Warna: Abu-abu <span class="mx-1">|</span> Ukuran: 40</p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span class="fw-bold text-dark fs-6">Rp 1.900.000</span>
                                            <span class="text-dark fw-bold" style="font-size: 13px;">x 1</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start">
                                    <div class="ratio ratio-1x1" style="width: 120px; flex-shrink: 0;">
                                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Produk" class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div class="ms-4 flex-grow-1">
                                        <p class="fw-bold mb-1 text-uppercase" style="font-size: 15px;">SAMBA CLASSIC INDOOR</p>
                                        <p class="text-secondary mb-2" style="font-size: 13px;">Warna: Hitam <span class="mx-1">|</span> Ukuran: 41</p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span class="fw-bold text-dark fs-6">Rp 1.500.000</span>
                                            <span class="text-dark fw-bold" style="font-size: 13px;">x 1</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="mb-5">
                            <h5 class="fw-bold mb-3 text-uppercase" style="font-size: 14px; letter-spacing: 1px;">Metode Pengiriman</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="shipping" id="shipReguler" checked>
                                    <label class="btn btn-outline-dark rounded-0 w-100 text-start p-3 selection-box-clean" for="shipReguler">
                                        <div class="fw-bold mb-1" style="font-size: 14px;">Reguler (JNE/Sicepat)</div>
                                        <div class="text-secondary" style="font-size: 12px;">Estimasi 2-3 Hari Kerja</div>
                                        <div class="fw-bold mt-2" style="font-size: 14px;">Rp 25.000</div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="shipping" id="shipNextDay">
                                    <label class="btn btn-outline-dark rounded-0 w-100 text-start p-3 selection-box-clean" for="shipNextDay">
                                        <div class="fw-bold mb-1" style="font-size: 14px;">Next Day (JNE YES)</div>
                                        <div class="text-secondary" style="font-size: 12px;">Tiba Esok Hari</div>
                                        <div class="fw-bold mt-2" style="font-size: 14px;">Rp 40.000</div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="mb-5">
                            <h5 class="fw-bold mb-3 text-uppercase" style="font-size: 14px; letter-spacing: 1px;">Metode Pembayaran</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="payment" id="payTransfer" checked>
                                    <label class="btn btn-outline-dark rounded-0 w-100 text-center p-3 selection-box" for="payTransfer">
                                        <i class="bi bi-bank fs-4 mb-2 d-block"></i>
                                        <div class="fw-bold" style="font-size: 13px;">Transfer Bank</div>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="payment" id="payEwallet">
                                    <label class="btn btn-outline-dark rounded-0 w-100 text-center p-3 selection-box" for="payEwallet">
                                        <i class="bi bi-phone fs-4 mb-2 d-block"></i>
                                        <div class="fw-bold" style="font-size: 13px;">QRIS</div>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="payment" id="payCC">
                                    <label class="btn btn-outline-dark rounded-0 w-100 text-center p-3 selection-box" for="payCC">
                                        <i class="bi bi-credit-card fs-4 mb-2 d-block"></i>
                                        <div class="fw-bold" style="font-size: 13px;">Kartu Kredit</div>
                                    </label>
                                </div>
                            </div>
                        </div> -->

                    </div>

                    <div class="col-12 col-lg-5 col-xl-4">
                        
                        <div class="bg-light-gray p-4 rounded-0">
                            <h5 class="fw-bold mb-4 text-uppercase">Ringkasan Belanja</h5>
                            
                            <div class="d-flex justify-content-between mb-3 summary-text">
                                <span>Total Harga (2 Barang)</span>
                                <span class="fw-semibold">Rp 3.400.000</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-3 summary-text">
                                <span>Total Ongkos Kirim</span>
                                <span class="fw-semibold">Rp 25.000</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-4 summary-text">
                                <span>Diskon Promo</span>
                                <span class="fw-semibold text-sale">- Rp 100.000</span>
                            </div>
                            
                            <hr class="border-secondary mb-4">
                            
                            <div class="d-flex justify-content-between mb-4 align-items-center">
                                <span class="fw-bold text-uppercase fs-6">Total</span>
                                <span class="fw-bold fs-5">Rp 3.325.000</span>
                            </div>

                            <div class="mb-4">
                                <label class="text-secondary mb-2" style="font-size: 11px;">Masukan Kode Diskon</label>
                                <div class="input-group">
                                    <input type="text" class="form-control rounded-0 border-dark shadow-none" placeholder="Kode Diskon" style="font-size: 13px;">
                                    <button class="btn btn-black rounded-0 fw-bold px-3 text-uppercase" type="button" style="font-size: 12px;">Gunakan</button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-black w-100 py-3 fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 14px;">
                                BAYAR SEKARANG
                            </button>
                            
                            <p class="text-center text-muted mt-3 mb-0" style="font-size: 11px;">
                                Dengan melakukan pembayaran, Anda menyetujui Syarat & Ketentuan kami.
                            </p>
                        </div>
                    </div>

                </div>
            </form>
            
        </div>
    </section>

    <x-customer.footer />
    <x-customer.chatbot />
@endsection
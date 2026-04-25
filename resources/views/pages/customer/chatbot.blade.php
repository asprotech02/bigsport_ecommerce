@extends('layouts.app')

@section('content')

    <style>
        /* Sumbu/Ekor untuk chat User (Kanan) */
        .chat-bubble-right {
            position: relative;
            background-color: #1c1c1c;
            border-radius: 12px;
            border-top-right-radius: 0; /* Sudut kanan atas dibuat siku agar ekor menempel pas */
        }
        .chat-bubble-right::after {
            content: '';
            position: absolute;
            top: 0;
            right: -10px; /* Menjorok ke kanan */
            width: 0;
            height: 0;
            border-top: 15px solid #1c1c1c; /* Warna ekor sama dengan background chat */
            border-right: 10px solid transparent; /* Membentuk potongan miring */
        }

        /* Sumbu/Ekor untuk chat Admin (Kiri) */
        .chat-bubble-left {
            position: relative;
            background-color: #ffffff;
            border: 1px solid #333;
            border-radius: 12px;
            border-top-left-radius: 0; /* Sudut kiri atas dibuat siku */
        }
        /* Ekor bagian luar (untuk border hitam) */
        .chat-bubble-left::before {
            content: '';
            position: absolute;
            top: -1px;
            left: -11px;
            width: 0;
            height: 0;
            border-top: 15px solid #333;
            border-left: 10px solid transparent;
        }
        /* Ekor bagian dalam (warna putih) */
        .chat-bubble-left::after {
            content: '';
            position: absolute;
            top: 0;
            left: -9px;
            width: 0;
            height: 0;
            border-top: 14px solid #ffffff;
            border-left: 9px solid transparent;
        }
    </style>

    <div class="position-fixed top-0 start-0 w-100 h-100 d-flex flex-column bg-white" style="z-index: 9999; overflow: hidden;">
        
        <div class="border-bottom border-dark p-3 p-md-4 d-flex align-items-center flex-shrink-0" style="background-color: #000000;">
            <a href="{{ url()->previous() }}" class="btn border-0 text-white hover-opacity p-0 me-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                <i class="bi bi-arrow-left fs-4"></i>
            </a>
            <div class="bg-white text-black d-flex justify-content-center align-items-center flex-shrink-0" style="width: 50px; height: 50px; border-radius: 10px;">
                <i class="bi bi-headset fs-5"></i>
            </div>
            <div class="ms-3">
                <h6 class="fw-bold mb-1 text-white text-uppercase" style="letter-spacing: 1px;">Customer Service</h6>
                <span class="text-success fw-bold" style="font-size: 11px;"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Online</span>
            </div>
        </div>

        <div class="p-4 p-md-5 flex-grow-1 overflow-y-auto" style="background-color: #f8f9fa; padding-right: 2rem !important; padding-left: 2rem !important;">
            
            <div class="d-flex mb-4">
                <div class="chat-bubble-left p-3 shadow-sm" style="max-width: 60%;">
                    <p class="mb-1 text-dark" style="font-size: 13px; line-height: 1.5;">Halo, Kak! Ada yang bisa kami bantu terkait pesanan atau ketersediaan sepatu Samba?</p>
                    <div class="text-star">
                        <span class="text-secondary" style="font-size: 10px;">09:45 AM</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <div class="chat-bubble-right p-3 shadow-sm text-white" style="max-width: 60%;">
                    <p class="mb-1" style="font-size: 13px; line-height: 1.5;">Halo, saya mau tanya apakah Sepatu Samba Seris Denim V1 ukuran 42 masih ready stock?</p>
                    <div class="text-end">
                        <span class="text-white-50" style="font-size: 10px;">09:47 AM</span>
                    </div>
                </div>
            </div>

            <div class="d-flex mb-4">
                <div class="chat-bubble-left p-3 shadow-sm" style="max-width: 60%;">
                    <p class="mb-1 text-dark" style="font-size: 13px; line-height: 1.5;">Halo kembali, Kak! Untuk seri Samba Denim V1 ukuran 42 saat ini masih tersedia 2 pasang. Silakan langsung di-checkout sebelum kehabisan ya.</p>
                    <div class="text-star">
                        <span class="text-secondary" style="font-size: 10px;">09:50 AM</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <div class="chat-bubble-right p-3 shadow-sm text-white" style="max-width: 60%;">
                    <p class="mb-1" style="font-size: 13px; line-height: 1.5;">Baik, terima kasih infonya. Kalau saya pesan sekarang pakai JNE YES, kira-kira sampainya kapan ya?</p>
                    <div class="text-end">
                        <span class="text-white-50" style="font-size: 10px;">09:51 AM</span>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mb-4">
                <div class="chat-bubble-right p-3 shadow-sm text-white" style="max-width: 60%;">
                    <p class="mb-1" style="font-size: 13px; line-height: 1.5;">Baik, terima kasih infonya. Kalau saya pesan sekarang pakai JNE YES, kira-kira sampainya kapan ya?</p>
                    <div class="text-end">
                        <span class="text-white-50" style="font-size: 10px;">09:51 AM</span>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mb-4">
                <div class="chat-bubble-right p-3 shadow-sm text-white" style="max-width: 60%;">
                    <p class="mb-1" style="font-size: 13px; line-height: 1.5;">Baik, terima kasih infonya. Kalau saya pesan sekarang pakai JNE YES, kira-kira sampainya kapan ya?</p>
                    <div class="text-end">
                        <span class="text-white-50" style="font-size: 10px;">09:51 AM</span>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mb-4">
                <div class="chat-bubble-right p-3 shadow-sm text-white" style="max-width: 60%;">
                    <p class="mb-1" style="font-size: 13px; line-height: 1.5;">Baik, terima kasih infonya. Kalau saya pesan sekarang pakai JNE YES, kira-kira sampainya kapan ya?</p>
                    <div class="text-end">
                        <span class="text-white-50" style="font-size: 10px;">09:51 AM</span>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mb-4">
                <div class="chat-bubble-right p-3 shadow-sm text-white" style="max-width: 60%;">
                    <p class="mb-1" style="font-size: 13px; line-height: 1.5;">Baik, terima kasih infonya. Kalau saya pesan sekarang pakai JNE YES, kira-kira sampainya kapan ya?</p>
                    <div class="text-end">
                        <span class="text-white-50" style="font-size: 10px;">09:51 AM</span>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mb-4">
                <div class="chat-bubble-right p-3 shadow-sm text-white" style="max-width: 60%;">
                    <p class="mb-1" style="font-size: 13px; line-height: 1.5;">Baik, terima kasih infonya. Kalau saya pesan sekarang pakai JNE YES, kira-kira sampainya kapan ya?</p>
                    <div class="text-end">
                        <span class="text-white-50" style="font-size: 10px;">09:51 AM</span>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mb-4">
                <div class="chat-bubble-right p-3 shadow-sm text-white" style="max-width: 60%;">
                    <p class="mb-1" style="font-size: 13px; line-height: 1.5;">Baik, terima kasih infonya. Kalau saya pesan sekarang pakai JNE YES, kira-kira sampainya kapan ya?</p>
                    <div class="text-end">
                        <span class="text-white-50" style="font-size: 10px;">09:51 AM</span>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mb-4">
                <div class="chat-bubble-right p-3 shadow-sm text-white" style="max-width: 60%;">
                    <p class="mb-1" style="font-size: 13px; line-height: 1.5;">Baik, terima kasih infonya. Kalau saya pesan sekarang pakai JNE YES, kira-kira sampainya kapan ya?</p>
                    <div class="text-end">
                        <span class="text-white-50" style="font-size: 10px;">09:51 AM</span>
                    </div>
                </div>
            </div>
            
        </div>

        <div class="border-top border-dark p-3 p-md-4 flex-shrink-0" style="background-color: #000000;">
            <form action="#" class="d-flex gap-2 gap-md-3">
                <button type="button" class="btn rounded-0 d-flex justify-content-center align-items-center text-white hover-opacity" style="width: 55px; height: 55px; flex-shrink: 0; background-color: #1a1a1a; border: 1px solid #333;" title="Lampirkan File">
                    <i class="bi bi-plus-lg fs-3"></i>
                </button>
                <input type="text" class="form-control rounded-0 shadow-none flex-grow-1 text-white" placeholder="Ketik pesan Anda di sini..." style="font-size: 15px; padding: 10px 20px; background-color: #1a1a1a; border: 1px solid #333;">
                <button type="submit" class="btn m-0 d-flex justify-content-center align-items-center hover-opacity" style="width: 65px; height: 55px; padding: 0; flex-shrink: 0; background-color: #1a1a1a; border: 1px solid #333;">
                    <i class="bi bi-send-fill fs-4 text-white"></i>
                </button>
            </form>
        </div>
        
    </div>
@endsection
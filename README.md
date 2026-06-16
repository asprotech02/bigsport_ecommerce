# 🛒 Ecommerce Bagindo Jaya

## Deskripsi
Project website e-commerce berbasis Laravel dengan desain minimalis, monokrom, dan fokus pada pengalaman belanja modern. Sistem ini sudah terintegrasi dengan Payment Gateway (Midtrans) dan Logistik/Ongkir (Biteship).

---

## 🛠️ Cara Install

1. Clone repository
```bash
git clone https://github.com/Wisnuazi000/ecommerce_bigsport.git
cd ecommerce_bigsport
Install dependency

Bash
composer install
npm install
Copy file environment

Bash
cp .env.example .env
Generate key

Bash
php artisan key:generate
Setup database

Buat database: ecommerce_bigsport

Sesuaikan konfigurasi database di file .env

Migrasi database & Seeding (jika ada)

Bash
php artisan migrate
Jalankan project

Bash
php artisan serve
npm run dev
🎯 Panduan Testing untuk Partner (End-to-End Test)
Untuk memastikan semua alur berjalan lancar dari sisi user hingga dashboard admin/API, silakan ikuti skenario pengujian berikut:

Langkah Pengujian di Aplikasi:
Buka Aplikasi: Akses http://localhost:8000 di browser.

Registrasi: Buat akun baru di halaman Register.

Belanja: Buka Katalog, pilih produk, tentukan ukuran (pastikan stok tersedia), lalu klik "Tambah ke Keranjang".

Checkout: Masuk ke Keranjang, klik "Lanjut Checkout", lalu isi alamat pengiriman (API Biteship akan menghitung ongkir secara otomatis).

Pembayaran: Pilih kurir, klik "Bayar Sekarang". Pop-up Midtrans akan muncul. Pilih metode pembayaran (misal: BCA VA).

Simulasi Bayar: Untuk testing pembayaran, gunakan Midtrans Simulator dan masukkan nomor VA yang didapat. (Pastikan Ngrok sudah menyala dan terhubung ke Midtrans, baca panduan Ngrok di bawah).

Lacak Pesanan: Setelah bayar, Anda akan dialihkan ke Riwayat Pesanan (/order). Klik tombol Lacak Pesanan untuk melihat pergerakan resi paket secara real-time via Biteship.

🔐 Kredensial Dashboard (Midtrans & Biteship)
Untuk mengecek apakah transaksi atau request resi masuk ke sistem pihak ketiga, silakan login ke dashboard mereka menggunakan akun development ini:

Email: wisnuazi404@gmail.com

Password: Wisnuazi12345@#

(Gunakan kredensial di atas untuk login ke Dashboard Midtrans maupun Dashboard Biteship)

🚀 Alur Sistem (User Journey Flow) Detail
Registrasi & Login: Data masuk database, validasi autentikasi berjalan.

Eksplorasi Produk: Validasi stok aktif; tombol nonaktif jika stok 0.

Cart (AJAX): Badge keranjang update real-time tanpa reload. Pengecekan sisa stok berjalan di keranjang.

Checkout & Logistik: Terjemahan alamat Emsifa ke district_id Biteship -> Hit API Ongkir.

Create Order: Data masuk tabel orders status unpaid -> Hit API Biteship untuk generate resi/waybill -> Muncul Midtrans Snap.

Webhook Pembayaran: Midtrans ngirim notifikasi ke Ngrok lokal -> Status order otomatis jadi paid -> Stok product_skus berkurang.

Tracking & Update: Sistem hit API Tracking Biteship untuk lacak posisi paket. Status order di database otomatis update mengikuti status pengiriman logistik.

🌐 Panduan Webhook Lokal (Wajib untuk Testing Pembayaran)
Karena aplikasi berjalan di localhost, Midtrans membutuhkan "jembatan" (Ngrok) untuk mengirimkan sinyal status pembayaran sukses ke laptop Anda.

1. Cara Menjalankan Ngrok Bawaan Laragon
Buka Laragon, klik Start All.

Klik kanan di area kosong (atau tombol Menu) -> www -> Share -> klik ecommerce_bigsport (atau folder proyek Anda).

Jendela Command Prompt hitam akan muncul. Tunggu statusnya menjadi Forwarding.

Copy link acak yang berakhiran .ngrok-free.app (Contoh: [https://a1b2-3c4d.ngrok-free.app](https://a1b2-3c4d.ngrok-free.app)).

JANGAN TUTUP jendela CMD tersebut selama testing.

2. Pasang Link Ngrok di Midtrans
Login ke Dashboard Midtrans pakai kredensial di atas.

Pastikan posisi toggle di kiri atas adalah Sandbox.

Masuk ke Settings -> Configuration.

Di kolom Payment Notification URL, paste link Ngrok tadi dan tambahkan path API-nya.

Format: https://[link-ngrok-anda].ngrok-free.app/api/midtrans-callback

Klik Save.

💡 PENTING: Karena Ngrok versi gratis link-nya selalu berubah setiap di-restart, Anda WAJIB memperbarui Payment Notification URL di Midtrans setiap kali memulai sesi testing baru.

💻 Struktur Fitur & Teknologi
Teknologi:

Laravel

Bootstrap 5

Blade Template

Fitur Saat Ini:

Homepage

Product Listing & Detail

Cart (AJAX)

Checkout & API Ongkir (Biteship)

Payment Gateway (Midtrans)

Order History & Tracking (Biteship)

Auth (Login/Register)

🤝 Aturan Kolaborasi
JANGAN push langsung ke branch main.

Gunakan penamaan branch berikut:

Bash
feature/nama-fitur
Gunakan penamaan commit message yang jelas:

Bash
feat: add checkout page
fix: cart calculation bug
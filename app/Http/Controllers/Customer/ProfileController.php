<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Address;
use App\Models\UserNotification; // Tetap pertahankan model bawaan lu
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;   // 🌟 INI OBAT ERROR 500 NYA, BRO!
use Barryvdh\DomPDF\Facade\Pdf;


class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)->orderByDesc('is_default')->get();
        
        // 🌟 FIX: Pastikan memuat relasi shippingDetail agar resi bisa dibaca di View[cite: 2]
        $orders = Order::with(['items.sku.product.images', 'items.sku.product.brand', 'shippingDetail'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('customer.pages.profile', compact('user', 'orders', 'addresses'));
    }

    public function getTracking($id)
    {
        // 🌟 FIX: Panggil shippingDetail[cite: 2]
        $order = Order::with('shippingDetail')->where('user_id', Auth::id())->findOrFail($id);
        $shipping = $order->shippingDetail;

        if (!$shipping || !$shipping->tracking_number || !$shipping->courier_company) {
            return response()->json([
                'success' => false,
                'message' => 'Resi belum tersedia untuk pesanan ini.'
            ]);
        }

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'authorization' => env('BITESHIP_API_KEY')
            ])->get("https://api.biteship.com/v1/trackings/{$shipping->tracking_number}/couriers/{$shipping->courier_company}");

            $result = $response->json();

            if ($response->successful() && isset($result['success']) && $result['success'] == true) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'courier' => [
                            'company' => strtoupper($result['courier']['company'] ?? $shipping->courier_company),
                            'waybill_id' => $result['waybill_id'] ?? $shipping->tracking_number
                        ],
                        'status' => $result['status'] ?? 'Diproses',
                        'history' => $result['history'] ?? []
                    ]
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Biteship: ' . ($result['error'] ?? 'Data tracking tidak ditemukan.')]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'System Error: ' . $e->getMessage()]);
        }
    }

    /**
     * 2. Fungsi Cetak Invoice PDF
     */
    public function printInvoice($id)
    {
        // 🌟 FIX: Hapus relasi yang tidak dipakai/salah nama. 
        // Kita cukup butuh tabel relasi 'items' dan 'user' saja karena data lainnya sudah berupa snapshot.
        $order = Order::with(['items', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Pastikan hanya status tertentu yang bisa cetak invoice
        if (!in_array($order->status, ['processing', 'shipped', 'completed'])) {
            return redirect()->back()->with('error', 'Invoice belum tersedia untuk pesanan ini.');
        }

        // Load view PDF
        $pdf = Pdf::loadView('customer.pages.invoice_pdf', compact('order'));
        
        // Return download PDF dengan nama file dinamis
        return $pdf->download('INVOICE-' . $order->invoice_number . '.pdf');
    }

    /**
     * Memproses update data profil pengguna
     */
    public function updateProfile(\Illuminate\Http\Request $request)
    {
        // 1. Validasi inputan form
        $request->validate([
            'name'     => 'required|string|max:255',
            'birthday' => 'required|date',
            'gender'   => 'required|in:L,P',
        ]);

        // 2. Ambil data user yang sedang login
        $user = \Illuminate\Support\Facades\Auth::user();

        // 3. Update data ke database
        $user->update([
            'name'     => $request->name,
            'birthday' => $request->birthday,
            'gender'   => $request->gender,
        ]);

        // 4. Balikin ke halaman profil bawaan SPA lu
        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
    }


    /**
     * Memproses penggantian password dari dalam halaman profil (User Login)
     */
    public function updatePassword(\Illuminate\Http\Request $request)
    {
        // 1. Validasi inputan
        $request->validate([
            'old_password' => ['required', function ($attribute, $value, $fail) {
                if (!\Illuminate\Support\Facades\Hash::check($value, auth()->user()->password)) {
                    $fail('Password lama yang Anda masukkan salah');
                }
            }],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ], [
            'password.required' => 'Password baru wajib diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // 2. Update Password ke Database
        auth()->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);

        // 3. (Opsional) Keluarkan pesan sukses & kembalikan ke profil
        return redirect()->route('profile')->with('success', 'Password berhasil diperbarui!');
    }


    public function notifications()
    {
        // Ambil notifikasi user yang login, urutkan dari yang paling baru
        $notifications = \App\Models\UserNotification::where('user_id', auth()->id())
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('customer.pages.notification', compact('notifications'));
    }


    public function cancelOrderManual(Request $request, $id)
    {
        // Pastikan request adalah AJAX / JSON
        if (!$request->wantsJson() && !$request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Invalid Request Protocol']);
        }

        // Cari order milik user yang sedang login
        $order = Order::with('items.sku')
            ->where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.']);
        }

        // Pastikan pesanan memang dalam posisi layak dibatalkan
        if ($order->status == 'cancelled' || $order->payment_status == 'expired') {
            return response()->json(['success' => false, 'message' => 'Pesanan sudah dibatalkan sebelumnya.']);
        }

        try {
            DB::transaction(function () use ($order, $request) {
                // 1. Ubah status order menjadi cancelled DAN simpan alasannya
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed',
                    'cancel_reason' => $request->reason // 🌟 SIMPAN ALASAN PEMBATALAN DI SINI
                ]);

                // 2. Lepaskan kunci reserved_stock pada produk SKU
                foreach ($order->items as $item) {
                    $sku = $item->sku;
                    if ($sku) {
                        // Hanya kurangi reserved_stock, JANGAN tambah stock utama
                        $sku->decrement('reserved_stock', $item->quantity);
                    }
                }
            });

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memproses pembatalan di server.']);
        }
    }


/**
     * 1. Mengubah status menjadi Pesanan Diterima (Selesai)
     */
    public function completeOrder($id)
    {
        $order = Order::where('user_id', auth()->id())->where('id', $id)->firstOrFail();
        
        // Membersihkan spasi jika ada typo saat ketik manual di database
        $cleanStatus = trim(strtolower($order->status));

        if ($cleanStatus !== 'processing') {
            // Akan memunculkan status asli dari DB agar kita tahu apa yang salah
            return response()->json(['success' => false, 'message' => 'Gagal! Status di database saat ini: "' . $order->status . '"']);
        }

        $order->update(['status' => 'completed']);

        // 🌟 REALTIME NOTIFIKASI: Pesanan Selesai
        $notif = \App\Models\UserNotification::create([
            'user_id' => $order->user_id,
            'type'    => 'transaksi',
            'title'   => 'Pesanan Selesai 📦',
            'message' => "Terima kasih! Pesanan #{$order->invoice_number} telah selesai. Jangan lupa beri ulasan produk pilihanmu ya!"
        ]);
        
        broadcast(new \App\Events\RealTimeNotification($notif));

        return response()->json(['success' => true]);
    }
    
    /**
     * 2. Fitur Beli Lagi (Copy items kembali ke Keranjang)
     */
    public function buyAgain($id)
    {
        $order = Order::with('items')->where('user_id', auth()->id())->where('id', $id)->firstOrFail();

        foreach ($order->items as $item) {
            // Masukkan kembali produk ke tabel carts
            \App\Models\Cart::updateOrCreate(
                [
                    'user_id'        => auth()->id(),
                    'product_sku_id' => $item->product_sku_id,
                ],
                [
                    'quantity' => DB::raw("quantity + {$item->quantity}")
                ]
            );
        }

        return redirect()->route('cart')->with('success', 'Produk berhasil dimasukkan kembali ke keranjang belanjamu!');
    }

    /**
     * 3. Menyimpan ulasan produk dari pembeli
     */
   /**
     * 3. Menyimpan ulasan produk dari pembeli
     */
    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'product_sku_id' => 'required',
            'rating'         => 'required|integer|min:1|max:5',
            'comment'        => 'required|string|max:1000',
        ]);

        // Cek agar user tidak spam ulasan untuk produk yang sama berkali-kali
        $existingReview = DB::table('reviews')
            ->where('user_id', auth()->id())
            ->where('product_sku_id', $request->product_sku_id)
            ->first();

        if ($existingReview) {
            return response()->json(['success' => false, 'message' => 'Anda sudah memberikan ulasan untuk produk ini.']);
        }

        // Simpan ulasan ke tabel reviews (TANPA order_id)
        DB::table('reviews')->insert([
            'user_id'        => auth()->id(),
            'product_sku_id' => $request->product_sku_id,
            'rating'         => $request->rating,
            'comment'        => $request->comment,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Terima kasih atas ulasan Anda! ⭐']);
    }


    /**
     * Menampilkan Halaman Khusus Lacak Pesanan
     */
    public function trackOrderPage($id)
    {
        $order = Order::with('shippingDetail')->where('user_id', Auth::id())->findOrFail($id);
        $shipping = $order->shippingDetail;

        $trackingData = null;
        $errorMessage = null;

        if (!$shipping || empty($shipping->tracking_number) || empty($shipping->courier_company)) {
            $errorMessage = 'Resi belum tersedia atau pesanan menggunakan metode Ambil di Toko.';
        } else {
            try {
                $response = Http::withoutVerifying()->withHeaders([
                    'authorization' => env('BITESHIP_API_KEY')
                ])->get("https://api.biteship.com/v1/trackings/{$shipping->tracking_number}/couriers/{$shipping->courier_company}");

                $result = $response->json();

                if ($response->successful() && isset($result['success']) && $result['success'] == true) {
                    
                    // 🌟 LOGIKA TRANSLATE STATUS BITESHIP KE INDONESIA 🌟
                    $historyIndo = [];
                    if (!empty($result['history'])) {
                        foreach ($result['history'] as $item) {
                            
                            // 1. Translate Status Utama
                            $statusIndo = match(strtoupper($item['status'])) {
                                'PLACED'       => 'PESANAN DIBUAT',
                                'CONFIRMED'    => 'MENUNGGU KURIR',
                                'ALLOCATED'    => 'KURIR DIALOKASIKAN',
                                'PICKING_UP'   => 'PROSES PENJEMPUTAN',
                                'PICKED'       => 'PAKET DIAMBIL',
                                'DROPPING_OFF' => 'DALAM PENGIRIMAN',
                                'DELIVERED'    => 'TELAH DITERIMA',
                                'REJECTED'     => 'PENGIRIMAN DITOLAK',
                                'CANCELLED'    => 'PENGIRIMAN DIBATALKAN',
                                'RETURNED'     => 'PAKET DIKEMBALIKAN',
                                default        => strtoupper($item['status'])
                            };

                            // 2. Translate Keterangan (Note)
                            $note = $item['note'] ?? '';
                            $replacements = [
                                'Courier order is confirmed' => 'Pesanan kurir telah dikonfirmasi',
                                'has been notified to pick up' => 'telah dinotifikasi untuk melakukan penjemputan',
                                'Pickup Number' => 'Nomor Penjemputan',
                                'Courier is allocated and ready to pick up' => 'Kurir telah dialokasikan dan bersiap menjemput paket',
                                'Courier is on the way to pick up location' => 'Kurir sedang dalam perjalanan menuju lokasi penjemputan',
                                'Item has been picked and ready to be shipped' => 'Paket telah diambil oleh kurir dan siap dikirim',
                                'Item is on the way to customer' => 'Paket sedang dalam perjalanan menuju alamat pembeli',
                                'Item has been delivered' => 'Paket telah berhasil dikirim dan diterima',
                                'Delivered' => 'Terkirim'
                            ];
                            $noteIndo = str_ireplace(array_keys($replacements), array_values($replacements), $note);

                            $historyIndo[] = [
                                'updated_at' => $item['updated_at'],
                                'status'     => $statusIndo,
                                'note'       => $noteIndo
                            ];
                        }
                    }

                    $trackingData = [
                        'courier_company' => strtoupper($result['courier']['company'] ?? $shipping->courier_company),
                        'waybill_id' => $result['waybill_id'] ?? $shipping->tracking_number,
                        'status' => $result['status'] ?? 'Diproses',
                        'history' => $historyIndo // Masukkan data yang sudah di-translate
                    ];
                } else {
                    $errorMessage = 'Biteship Error: ' . ($result['error'] ?? 'Data tracking belum diupdate oleh kurir.');
                }
            } catch (\Exception $e) {
                $errorMessage = 'System Error: Gagal terhubung ke server ekspedisi.';
            }
        }

        return view('customer.pages.track_order', compact('order', 'trackingData', 'errorMessage'));
    }
}
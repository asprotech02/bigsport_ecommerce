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
use Illuminate\Support\Facades\Log;


class ProfileController extends Controller
{
    public function index()
    {
        // 🌟 Sinkronisasi status pembayaran dengan Midtrans secara otomatis di localhost
        Order::syncAllUnpaid();

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
                // Let's translate history!
                $translateHistory = function ($history) use ($order) {
                    $historyIndo = [];
                    $hasDelivered = false;

                    if (!empty($history)) {
                        foreach ($history as $item) {
                            $statusUpper = strtoupper($item['status'] ?? '');
                            if ($statusUpper === 'DELIVERED') {
                                $hasDelivered = true;
                            }

                            $statusIndo = match($statusUpper) {
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
                                default        => $statusUpper
                            };

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

                    if (in_array(strtolower($order->status), ['completed', 'delivered']) && !$hasDelivered) {
                        array_unshift($historyIndo, [
                            'updated_at' => $order->updated_at->toIso8601String(),
                            'status'     => 'TELAH DITERIMA',
                            'note'       => 'Paket telah diterima oleh customer.'
                        ]);
                    }

                    return $historyIndo;
                };

                $history = $translateHistory($result['history'] ?? []);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'courier' => [
                            'company' => strtoupper($result['courier']['company'] ?? $shipping->courier_company),
                            'waybill_id' => $result['waybill_id'] ?? $shipping->tracking_number
                        ],
                        'status' => in_array(strtolower($order->status), ['completed', 'delivered']) ? 'delivered' : ($result['status'] ?? 'Diproses'),
                        'history' => $history
                    ]
                ]);
            }

            // Fallback if biteship fails but order is completed or delivered
            if (in_array(strtolower($order->status), ['completed', 'delivered'])) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'courier' => [
                            'company' => strtoupper($shipping->courier_company),
                            'waybill_id' => $shipping->tracking_number
                        ],
                        'status' => 'delivered',
                        'history' => [
                            [
                                'updated_at' => $order->updated_at->toIso8601String(),
                                'status'     => 'TELAH DITERIMA',
                                'note'       => 'Paket telah diterima oleh customer.'
                            ]
                        ]
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


    public function notifications(Request $request)
    {
        $type = $request->query('type');
        
        $query = \App\Models\UserNotification::where('user_id', auth()->id());
        
        if ($type === 'transaksi') {
            $query->whereIn('type', ['transaksi', 'transaction']);
        } elseif ($type === 'promo') {
            $query->where('type', 'promo');
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->get();

        return view('customer.pages.notification', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = \App\Models\UserNotification::where('user_id', auth()->id())->findOrFail($id);
        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        $unreadCount = \App\Models\UserNotification::where('user_id', auth()->id())
                                                    ->where('is_read', false)
                                                    ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAllAsRead()
    {
        \App\Models\UserNotification::where('user_id', auth()->id())
                                    ->where('is_read', false)
                                    ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'unread_count' => 0
        ]);
    }


    public function cancelOrderManual(Request $request, $id)
{
    $order = Order::with('items.sku')
        ->where('user_id', auth()->id())
        ->where('id', $id)
        ->first();

    if (!$order) {
        return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.']);
    }

    if ($order->status == 'cancelled') {
        return response()->json(['success' => false, 'message' => 'Pesanan sudah dibatalkan.']);
    }

    try {
        DB::transaction(function () use ($order, $request) {
            // 1. Update status pesanan
            $isPaid = ($order->payment_status == 'paid');
            $order->update([
                'status' => 'cancelled',
                'payment_status' => $isPaid ? 'refunded' : 'failed',
                'cancel_reason' => $request->reason
            ]);

            // 2. LOGIKA STOK REAL LIFE (Sangat Aman)
            foreach ($order->items as $item) {
                $sku = $item->sku;
                if ($sku) {
                    if ($isPaid) {
                        // KASUS: Pesanan sudah dibayar, stok utama sudah terpotong
                        // Aksi: Kembalikan stok ke etalase (Increment Stok Utama)
                        $sku->increment('stock', $item->quantity);
                    } else {
                        // KASUS: Pesanan belum dibayar, baru reserved
                        // Aksi: Kurangi angka reserved_stock, tapi pastikan tidak pernah di bawah 0
                        $currentReserved = $sku->reserved_stock;
                        $amountToDecrement = min($item->quantity, $currentReserved);
                        
                        if ($amountToDecrement > 0) {
                            $sku->decrement('reserved_stock', $amountToDecrement);
                        }
                    }
                }
            }
        });

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        Log::error("Error Pembatalan Stok: " . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal memproses pembatalan.']);
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

        if ($order->payment_status !== 'paid') {
            return response()->json(['success' => false, 'message' => 'Gagal! Pesanan belum dibayar.']);
        }

        if (in_array($cleanStatus, ['completed', 'cancelled'])) {
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
        
        try {
            broadcast(new \App\Events\RealTimeNotification($notif));
        } catch (\Exception $e) {
            \Log::warning("RealTimeNotification broadcast failed: " . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }
    
    /**
     * 2. Fitur Beli Lagi (Copy items kembali ke Keranjang)
     */
    public function buyAgain($id)
{
    // 1. Ambil data order lama dengan relasi item dan sku
    $order = Order::with('items.sku.product')->where('user_id', auth()->id())->where('id', $id)->firstOrFail();

    foreach ($order->items as $item) {
        $sku = $item->sku;

        // 2. Validasi: Apakah produk masih ada dan aktif?
        if (!$sku || !$sku->product) {
            continue; // Skip jika produk/sku sudah dihapus
        }

        // 3. Logika Stok: Jangan masukkan jumlah lama, tapi cek stok terbaru
        // Pembeli maksimal beli sejumlah stok yang tersedia SEKARANG
        $stockAvailable = $sku->stock - $sku->reserved_stock;
        
        if ($stockAvailable <= 0) {
            // Opsional: Beri notifikasi bahwa produk ini stoknya habis
            continue; 
        }

        // 4. Masukkan ke Keranjang dengan penanganan duplikat
        \App\Models\Cart::updateOrCreate(
            [
                'user_id'        => auth()->id(),
                'product_sku_id' => $item->product_sku_id,
            ],
            [
                // Tambahkan jumlah, tapi tidak boleh melebihi stok yang ada
                'quantity' => DB::raw("LEAST(quantity + {$item->quantity}, {$stockAvailable})")
            ]
        );
    }

    return redirect()->route('cart')->with('success', 'Produk yang masih tersedia telah dimasukkan ke keranjang!');
}

   // Hapus parameter $id dari function signature
public function storeReview(Request $request)
{
    $request->validate([
        'order_item_id' => 'required|exists:order_items,id',
        'rating'        => 'required|integer|min:1|max:5',
        'comment'       => 'required|string|max:1000',
    ]);

    // Ambil order_item agar kita bisa tahu product_id nya
    $orderItem = \App\Models\OrderItem::with('sku')->find($request->order_item_id);

    // Cek duplikasi
    $existing = DB::table('reviews')
        ->where('user_id', auth()->id())
        ->where('order_item_id', $request->order_item_id)
        ->exists();

    if ($existing) {
        return response()->json(['success' => false, 'message' => 'Anda sudah mengulas produk ini.']);
    }

    DB::table('reviews')->insert([
        'user_id'    => auth()->id(),
        'product_id' => $orderItem->sku->product_id, // Ambil dari SKU
        'order_item_id' => $request->order_item_id,
        'rating'     => $request->rating,
        'comment'    => $request->comment,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['success' => true, 'message' => 'Terima kasih atas ulasannya! ⭐']);
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

        // --- SERVER-SIDE GEOCODING (NOMINATIM) DENGAN TIMEOUT 1.5 DETIK ---
        $destLat = -6.1783; // Default: Tangerang Kota
        $destLng = 106.6319;

        if ($shipping && !empty($order->shipping_address)) {
            $addressParts = explode(' | ', $order->shipping_address);
            $cleanAddress = $addressParts[1] ?? $order->shipping_address;
            $cleanAddress = trim(str_replace(["\r", "\n", "'", '"'], ' ', $cleanAddress));
            
            try {
                // Coba 1: Alamat Lengkap
                $geoResponse = Http::timeout(1.5)
                    ->withHeaders([
                        'User-Agent' => 'BagindoJayaEcommerceApp/1.0 (achma.wisnu@gmail.com)'
                    ])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'format' => 'json',
                        'q' => $cleanAddress,
                        'limit' => 1
                    ]);

                if ($geoResponse->successful()) {
                    $geoData = $geoResponse->json();
                    if (!empty($geoData) && isset($geoData[0]['lat'])) {
                        $destLat = (float) $geoData[0]['lat'];
                        $destLng = (float) $geoData[0]['lon'];
                    } else {
                        // Coba 2: Sederhanakan jika gagal
                        $parts = explode(',', $cleanAddress);
                        if (count($parts) > 2) {
                            $simpler = implode(',', array_slice($parts, -3));
                            $geoResponse2 = Http::timeout(1.2)
                                ->withHeaders(['User-Agent' => 'BagindoJayaEcommerceApp/1.0 (achma.wisnu@gmail.com)'])
                                ->get('https://nominatim.openstreetmap.org/search', [
                                    'format' => 'json',
                                    'q' => $simpler,
                                    'limit' => 1
                                ]);
                            if ($geoResponse2->successful()) {
                                $geoData2 = $geoResponse2->json();
                                if (!empty($geoData2) && isset($geoData2[0]['lat'])) {
                                    $destLat = (float) $geoData2[0]['lat'];
                                    $destLng = (float) $geoData2[0]['lon'];
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Server-side geocoding failed: " . $e->getMessage());
            }
        }

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
                    $errorText = $result['error'] ?? '';
                    if (strpos(strtolower($errorText), 'not found') !== false || strpos(strtolower($errorText), 'tidak ditemukan') !== false) {
                        // Resi baru dibuat, belum di-pickup kurir
                        $trackingData = [
                            'courier_company' => strtoupper($shipping->courier_company),
                            'waybill_id' => $shipping->tracking_number,
                            'status' => 'DIPROSES',
                            'history' => [
                                [
                                    'updated_at' => $order->updated_at->toIso8601String(),
                                    'status' => 'PESANAN DIPROSES',
                                    'note' => 'Nomor resi pengiriman telah diterbitkan oleh penjual. Menunggu paket dijemput oleh kurir ekspedisi.'
                                ]
                            ]
                        ];
                    } else {
                        $errorMessage = 'Biteship Error: ' . ($result['error'] ?? 'Data tracking belum diupdate oleh kurir.');
                    }
                }
            } catch (\Exception $e) {
                $errorMessage = 'System Error: Gagal terhubung ke server ekspedisi.';
            }
        }

        // Fallback if biteship fails but order is completed or delivered
        if ((!$trackingData || $errorMessage) && in_array(strtolower($order->status), ['completed', 'delivered'])) {
            $trackingData = [
                'courier_company' => strtoupper($shipping->courier_company ?? 'KURIER'),
                'waybill_id' => $shipping->tracking_number ?? '-',
                'status' => 'delivered',
                'history' => [
                    [
                        'updated_at' => $order->updated_at->toIso8601String(),
                        'status'     => 'TELAH DITERIMA',
                        'note'       => 'Paket telah diterima oleh customer.'
                    ]
                ]
            ];
            $errorMessage = null;
        }

        if ($trackingData && !empty($trackingData['history'])) {
            $hasDelivered = false;
            foreach ($trackingData['history'] as $item) {
                if (in_array(strtoupper($item['status']), ['DELIVERED', 'TELAH DITERIMA'])) {
                    $hasDelivered = true;
                }
            }
            if (in_array(strtolower($order->status), ['completed', 'delivered']) && !$hasDelivered) {
                array_unshift($trackingData['history'], [
                    'updated_at' => $order->updated_at->toIso8601String(),
                    'status'     => 'TELAH DITERIMA',
                    'note'       => 'Paket telah diterima oleh customer.'
                ]);
            }
        }

        return view('customer.pages.track_order', compact('order', 'trackingData', 'errorMessage', 'destLat', 'destLng'));
    }
}
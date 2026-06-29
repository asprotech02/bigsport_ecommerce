<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingDetail;
use App\Models\Order;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    // Display list of shipping details with filters (excluding pickups)
    public function index(Request $request)
    {
        $query = ShippingDetail::with('order.user')
            ->where('courier_company', '!=', 'pickup')
            ->whereHas('order', function($oq) {
                $oq->whereIn('status', ['confirmed', 'processing', 'preparing', 'shipped', 'delivered']);
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function($oq) use ($search) {
                      $oq->where('invoice_number', 'like', "%{$search}%")
                         ->orWhereHas('user', function($uq) use ($search) {
                              $uq->where('name', 'like', "%{$search}%");
                          });
                  });
            });
        }

        if ($request->filled('courier')) {
            $query->where('courier_company', $request->courier);
        }

        $shippings = $query->latest()->paginate(20)->withQueryString();
        $couriers = ShippingDetail::where('courier_company', '!=', 'pickup')->distinct()->pluck('courier_company');

        return view('admin.shippings.index', compact('shippings', 'couriers'));
    }

    // Display list of pickup details with filters
    public function indexPickup(Request $request)
    {
        $query = ShippingDetail::with('order.user')
            ->where('courier_company', 'pickup')
            ->whereHas('order', function($oq) {
                $oq->whereIn('status', ['confirmed', 'processing', 'preparing', 'delivered']);
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('order', function($oq) use ($search) {
                    $oq->where('invoice_number', 'like', "%{$search}%")
                       ->orWhereHas('user', function($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%");
                        });
                });
            });
        }

        $pickups = $query->latest()->paginate(20)->withQueryString();

        return view('admin.pickups.index', compact('pickups'));
    }

    // Show edit form for a shipping detail
    public function edit($id)
    {
        $shipping = ShippingDetail::findOrFail($id);
        return view('admin.shippings.edit', compact('shipping'));
    }

    // Update shipping detail
    public function update(Request $request, $id)
    {
        $request->validate([
            'biteship_order_id' => 'nullable|string|max:255',
            'courier_company'   => 'required|string|max:255',
            'courier_type'      => 'required|string|max:255',
            'tracking_number'   => 'nullable|string|max:255',
            'cost'              => 'required|numeric|min:0',
            'order_status'      => 'required|in:pending,confirmed,processing,preparing,shipped,delivered,completed,cancelled',
        ]);

        $shipping = ShippingDetail::with('order')->findOrFail($id);
        $oldTracking = $shipping->tracking_number;
        $newTracking = $request->tracking_number;
        $oldStatus = $shipping->order->status;
        $newStatus = $request->order_status;

        // Force 'shipped' if tracking number (resi) is provided/issued
        if (!empty($newTracking)) {
            $newStatus = 'shipped';
        }

        DB::transaction(function () use ($shipping, $request, $oldTracking, $newTracking, $oldStatus, $newStatus) {
            $shipping->update([
                'biteship_order_id' => $request->biteship_order_id,
                'courier_company'   => $request->courier_company,
                'courier_type'      => $request->courier_type,
                'tracking_number'   => $request->tracking_number,
                'cost'              => $request->cost,
            ]);

            // Update order status if changed
            if ($shipping->order) {
                $shipping->order->update(['status' => $newStatus]);
            }

            // Automated Flow-based Customer Notifications
            $notifTitle = 'Update Status Pengiriman 📦';
            $notifMsg = "Informasi pengiriman pesanan Anda #{$shipping->order->invoice_number} telah di-update.";

            if ($shipping->courier_company === 'pickup') {
                if ($newStatus === 'preparing' || $newStatus === 'processing') {
                    $notifTitle = 'Pesanan Sedang Disiapkan 📦';
                    $notifMsg = "Admin sedang menyiapkan barang pesanan Anda #{$shipping->order->invoice_number}. Mohon tunggu update ketika siap diambil.";
                } elseif ($newStatus === 'delivered') {
                    $notifTitle = 'Pesanan Siap Diambil 🏪';
                    $notifMsg = "Hore! Pesanan Anda #{$shipping->order->invoice_number} sudah siap untuk diambil di Toko Utama Bagindo Jaya.";
                } elseif ($newStatus === 'completed') {
                    $notifTitle = 'Pesanan Selesai 🎉';
                    $notifMsg = "Pesanan Anda #{$shipping->order->invoice_number} telah diambil dan diselesaikan. Terima kasih telah berbelanja di Bagindo Jaya!";
                }
            } else {
                // Condition 1: Admin menyiapkan barang
                if ($newStatus === 'preparing' || $newStatus === 'processing') {
                    $notifTitle = 'Pesanan Sedang Disiapkan 📦';
                    $notifMsg = "Admin sedang menyiapkan barang untuk pesanan Anda #{$shipping->order->invoice_number}. Mohon tunggu update pengiriman selanjutnya.";
                } 
                // Condition 2: Resi diinput & status shipped
                elseif ($newStatus === 'shipped' || ($newTracking && $newTracking !== $oldTracking)) {
                    $notifTitle = 'Pesanan Sedang Dikirim 🚚';
                    $notifMsg = "Pesanan Anda #{$shipping->order->invoice_number} sedang dikirim oleh kurir " . strtoupper($request->courier_company) . " dengan nomor resi: " . ($newTracking ?? '-') . ". Silakan lacak secara berkala!";
                }
                // Condition 3: Delivered
                elseif ($newStatus === 'delivered') {
                    $notifTitle = 'Pesanan Telah Sampai 🏠';
                    $notifMsg = "Kabar gembira! Pesanan Anda #{$shipping->order->invoice_number} telah sampai di alamat tujuan.";
                }
                // Condition 4: Completed
                elseif ($newStatus === 'completed') {
                    $notifTitle = 'Pesanan Selesai 🎉';
                    $notifMsg = "Pesanan Anda #{$shipping->order->invoice_number} telah diselesaikan. Terima kasih telah berbelanja di Bagindo Jaya!";
                }
            }

            UserNotification::create([
                'user_id' => $shipping->order->user_id,
                'type'    => 'order_status',
                'title'   => $notifTitle,
                'message' => $notifMsg,
                'is_read' => 0,
            ]);
        });

        if ($shipping->courier_company === 'pickup') {
            return redirect()->route('admin.pickups.index')
                ->with('success', 'Detail pengambilan toko dan status pesanan berhasil diperbarui.');
        }

        return redirect()->route('admin.shippings.index')
            ->with('success', 'Detail pengiriman dan status pesanan berhasil diperbarui.');
    }

    // Core Biteship order booking logic
    private function executeBiteshipBooking(ShippingDetail $shipping)
    {
        $order = $shipping->order;

        if (!$order) {
            throw new \Exception('Pesanan tidak ditemukan.');
        }

        if ($shipping->biteship_order_id) {
            throw new \Exception('Pesanan ini sudah dibooking di Biteship.');
        }

        if (empty($shipping->courier_company) || empty($shipping->courier_type)) {
            throw new \Exception('Informasi layanan kurir atau tipe kurir tidak lengkap/kosong.');
        }

        if (strtolower($shipping->courier_company) === 'pickup') {
            throw new \Exception('Pesanan dengan kurir Toko/Ambil Sendiri tidak dapat dibooking di Biteship.');
        }

        $address = \App\Models\Address::find($order->address_id);
        if (!$address) {
            throw new \Exception('Alamat pengiriman pelanggan tidak ditemukan.');
        }

        if (empty($address->district_id)) {
            throw new \Exception('Kecamatan tujuan pengiriman tidak valid / tidak teridentifikasi di Biteship.');
        }

        // Compile items array
        $items = [];
        foreach ($order->items as $orderItem) {
            $items[] = [
                'name' => $orderItem->product_name,
                'description' => "Size: " . $orderItem->product_size,
                'value' => (int) $orderItem->price_at_purchase,
                'weight' => 500, // 500 grams default weight per item
                'quantity' => (int) $orderItem->quantity
            ];
        }

        // Biteship Create Order Payload
        $payload = [
            'shipper_contact_name' => 'Bagindo Jaya Store',
            'shipper_contact_phone' => '081234567890',
            'shipper_contact_email' => 'admin@bagindojaya.com',
            'shipper_organization' => 'Bagindo Jaya Indonesia',
            'origin_contact_name' => 'Bagindo Jaya Store',
            'origin_contact_phone' => '081234567890',
            'origin_address' => 'Jl. HOS Cokroaminoto No.52, Larangan',
            'origin_note' => 'Toko Bagindo Jaya Larangan',
            'origin_postal_code' => 15154,
            'origin_area_id' => 'IDNP3IDNC445IDND5606', // Larangan, Tangerang area ID
            
            'destination_contact_name' => $address->receiver_name,
            'destination_contact_phone' => $address->receiver_phone,
            'destination_contact_email' => $order->user->email ?? 'customer@email.com',
            'destination_address' => $address->full_address . ', ' . ($address->village_name ?? '') . ', ' . ($address->district_name ?? '') . ', ' . ($address->city_name ?? '') . ', ' . ($address->province_name ?? '') . ' ' . ($address->postal_code ?? ''),
            'destination_postal_code' => (int) $address->postal_code,
            'destination_area_id' => $address->district_id,

            'courier_company' => strtolower($shipping->courier_company),
            'courier_type' => strtolower($shipping->courier_type),
            'delivery_type' => 'now', // Send immediately
            'items' => $items
        ];

        $apiKey = env('BITESHIP_API_KEY');
        if (empty($apiKey)) {
            throw new \Exception('BITESHIP_API_KEY tidak diatur di file .env.');
        }

        try {
            $response = Http::withHeaders([
                'authorization' => $apiKey,
                'content-type' => 'application/json'
            ])->post('https://api.biteship.com/v1/orders', $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['id'])) {
                $biteshipOrderId = $result['id'];
                $waybillId = $result['courier']['waybill_id'] ?? null;

                DB::transaction(function () use ($shipping, $order, $biteshipOrderId, $waybillId) {
                    $shipping->update([
                        'biteship_order_id' => $biteshipOrderId,
                        'tracking_number' => $waybillId
                    ]);

                    // If resi/waybill is generated/issued, status becomes 'shipped'
                    $newStatus = 'shipped';
                    $order->update(['status' => $newStatus]);

                    $notifTitle = 'Pesanan Sedang Dikirim 🚚';
                    $notifMsg = "Pesanan Anda #{$order->invoice_number} sedang dikirim oleh kurir " . strtoupper($shipping->courier_company) . " dengan nomor resi: " . ($waybillId ?? '-') . ".";

                    UserNotification::create([
                        'user_id' => $order->user_id,
                        'type' => 'order_status',
                        'title' => $notifTitle,
                        'message' => $notifMsg,
                        'is_read' => 0
                    ]);
                });

                $successMsg = 'Booking Biteship berhasil!';
                if ($waybillId) {
                    $successMsg .= " Nomor resi kurir otomatis terisi: {$waybillId}.";
                } else {
                    $successMsg .= ' Resi akan di-update otomatis oleh Biteship Webhook.';
                }

                return [
                    'success' => true,
                    'message' => $successMsg
                ];
            }

            $errorMsg = $result['error'] ?? ($result['message'] ?? 'Gagal membuat order di Biteship.');
            Log::error('Biteship API Error Response: ' . json_encode($result));

            // Sandbox fallback if API key is test key
            if (str_starts_with($apiKey, 'biteship_test.')) {
                $biteshipOrderId = 'sandbox_biteship_' . bin2hex(random_bytes(8));
                $waybillId = 'WYB-MOCK' . mt_rand(100000000, 999999999);

                DB::transaction(function () use ($shipping, $order, $biteshipOrderId, $waybillId) {
                    $shipping->update([
                        'biteship_order_id' => $biteshipOrderId,
                        'tracking_number' => $waybillId
                    ]);

                    $order->update(['status' => 'shipped']);

                    UserNotification::create([
                        'user_id' => $order->user_id,
                        'type' => 'order_status',
                        'title' => 'Pesanan Sedang Dikirim 🚚',
                        'message' => "Pesanan Anda #{$order->invoice_number} sedang dikirim oleh kurir " . strtoupper($shipping->courier_company) . " dengan nomor resi: {$waybillId} (Sandbox Mode).",
                        'is_read' => 0
                    ]);
                });

                return [
                    'success' => true,
                    'message' => "Booking Biteship berhasil (Sandbox Fallback)! Nomor resi: {$waybillId}."
                ];
            }

            throw new \Exception('Biteship API Error: ' . $errorMsg);

        } catch (\Exception $e) {
            Log::error('Biteship Request Exception: ' . $e->getMessage());

            // Sandbox fallback on connection error/exceptions
            if (str_starts_with($apiKey, 'biteship_test.')) {
                $biteshipOrderId = 'sandbox_biteship_' . bin2hex(random_bytes(8));
                $waybillId = 'WYB-MOCK' . mt_rand(100000000, 999999999);

                DB::transaction(function () use ($shipping, $order, $biteshipOrderId, $waybillId) {
                    $shipping->update([
                        'biteship_order_id' => $biteshipOrderId,
                        'tracking_number' => $waybillId
                    ]);

                    $order->update(['status' => 'shipped']);

                    UserNotification::create([
                        'user_id' => $order->user_id,
                        'type' => 'order_status',
                        'title' => 'Pesanan Sedang Dikirim 🚚',
                        'message' => "Pesanan Anda #{$order->invoice_number} sedang dikirim oleh kurir " . strtoupper($shipping->courier_company) . " dengan nomor resi: {$waybillId} (Sandbox Mode).",
                        'is_read' => 0
                    ]);
                });

                return [
                    'success' => true,
                    'message' => "Booking Biteship berhasil (Sandbox Fallback)! Nomor resi: {$waybillId}."
                ];
            }

            throw new \Exception('Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // Book Biteship (Single Order)
    public function bookBiteship($id)
    {
        try {
            $shipping = ShippingDetail::with(['order.user', 'order.items.sku.product'])->findOrFail($id);
            $result = $this->executeBiteshipBooking($shipping);
            return redirect()->back()->with('success', $result['message']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // Book Biteship (Bulk Orders)
    public function bookBiteshipBulk(Request $request)
    {
        $shippingIds = $request->input('shipping_ids', []);
        if (empty($shippingIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu pesanan untuk diproses.');
        }

        $shippings = ShippingDetail::with(['order.user', 'order.items.sku.product'])
            ->whereIn('id', $shippingIds)
            ->whereNull('biteship_order_id')
            ->where('courier_company', '!=', 'pickup')
            ->get();

        if ($shippings->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada pesanan valid yang dapat diproses.');
        }

        $successCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($shippings as $shipping) {
            try {
                $this->executeBiteshipBooking($shipping);
                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
                $errors[] = "#" . ($shipping->order->invoice_number ?? $shipping->id) . ": " . $e->getMessage();
            }
        }

        $message = "Selesai memproses booking Biteship secara massal. ";
        $message .= "Berhasil: {$successCount} pesanan. ";
        if ($failedCount > 0) {
            $message .= "Gagal: {$failedCount} pesanan. ";
            $errorDetail = "Detail kesalahan: " . implode('; ', $errors);
            
            return redirect()->back()
                ->with('success', $message)
                ->with('error', $errorDetail);
        }

        return redirect()->back()->with('success', $message);
    }

    // Complete Shipping (Bulk Orders)
    public function completeBulk(Request $request)
    {
        $shippingIds = $request->input('shipping_ids', []);
        if (empty($shippingIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu pesanan untuk diselesaikan.');
        }

        $shippings = ShippingDetail::with('order')
            ->whereIn('id', $shippingIds)
            ->get();

        if ($shippings->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada pesanan valid yang dapat diselesaikan.');
        }

        $successCount = 0;

        DB::transaction(function () use ($shippings, &$successCount) {
            foreach ($shippings as $shipping) {
                if ($shipping->order && $shipping->order->status !== 'completed') {
                    $shipping->order->update(['status' => 'completed']);

                    UserNotification::create([
                        'user_id' => $shipping->order->user_id,
                        'type'    => 'order_status',
                        'title'   => 'Pesanan Selesai 🎉',
                        'message' => "Pesanan Anda #{$shipping->order->invoice_number} telah diselesaikan. Terima kasih telah berbelanja di Bagindo Jaya!",
                        'is_read' => 0,
                    ]);
                    $successCount++;
                }
            }
        });

        return redirect()->back()->with('success', "Berhasil menyelesaikan {$successCount} pesanan pengiriman.");
    }

    // Print shipping label PDF
    public function printLabel($id)
    {
        $shipping = ShippingDetail::with(['order.items.sku.product', 'order.user'])->findOrFail($id);
        $order = $shipping->order;
        $address = \App\Models\Address::find($order->address_id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.shippings.label_pdf', compact('shipping', 'order', 'address'));
        return $pdf->download('SHIPPING-LABEL-' . $order->invoice_number . '.pdf');
    }

    // Track shipment from Biteship API with sandbox fallback
    public function trackShipment($id)
    {
        $shipping = ShippingDetail::with('order')->findOrFail($id);
        if (!$shipping->tracking_number || !$shipping->courier_company) {
            return response()->json(['success' => false, 'message' => 'Nomor resi atau kurir tidak valid.']);
        }

        try {
            $apiKey = env('BITESHIP_API_KEY');
            
            // Helper function to translate history items
            $translateHistory = function ($history) use ($shipping) {
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

                // If the order status is completed in the system, prepend "TELAH DITERIMA" to the top if not already present
                if ($shipping->order && strtolower($shipping->order->status) === 'completed' && !$hasDelivered) {
                    array_unshift($historyIndo, [
                        'updated_at' => $shipping->order->updated_at->toIso8601String(),
                        'status'     => 'TELAH DITERIMA',
                        'note'       => 'Paket telah diterima oleh customer.'
                    ]);
                }

                return $historyIndo;
            };

            // Check if test key/sandbox mode
            if (str_starts_with($apiKey, 'biteship_test.')) {
                $rawHistory = [
                    [
                        'updated_at' => now()->toIso8601String(),
                        'status' => 'shipped',
                        'note' => 'Paket sedang dikirim oleh kurir (Sandbox Mode).'
                    ]
                ];
                $history = $translateHistory($rawHistory);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'courier' => [
                            'company' => strtoupper($shipping->courier_company),
                            'waybill_id' => $shipping->tracking_number
                        ],
                        'status' => $shipping->order && strtolower($shipping->order->status) === 'completed' ? 'delivered' : 'shipped',
                        'history' => $history
                    ]
                ]);
            }

            $response = Http::withoutVerifying()->withHeaders([
                'authorization' => $apiKey
            ])->get("https://api.biteship.com/v1/trackings/{$shipping->tracking_number}/couriers/{$shipping->courier_company}");

            $result = $response->json();

            if ($response->successful() && isset($result['success']) && $result['success'] == true) {
                $history = $translateHistory($result['history'] ?? []);
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'courier' => [
                            'company' => strtoupper($result['courier']['company'] ?? $shipping->courier_company),
                            'waybill_id' => $result['waybill_id'] ?? $shipping->tracking_number
                        ],
                        'status' => $shipping->order && strtolower($shipping->order->status) === 'completed' ? 'delivered' : ($result['status'] ?? 'Diproses'),
                        'history' => $history
                    ]
                ]);
            }

            // Fallback if biteship fails but order is completed in system
            if ($shipping->order && strtolower($shipping->order->status) === 'completed') {
                $history = $translateHistory([]);
                return response()->json([
                    'success' => true,
                    'data' => [
                        'courier' => [
                            'company' => strtoupper($shipping->courier_company),
                            'waybill_id' => $shipping->tracking_number
                        ],
                        'status' => 'delivered',
                        'history' => $history
                    ]
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Biteship: ' . ($result['error'] ?? 'Data tracking tidak ditemukan.')]);
        } catch (\Exception $e) {
            // Fallback if exception occurs but order is completed in system
            if ($shipping->order && strtolower($shipping->order->status) === 'completed') {
                $history = [
                    [
                        'updated_at' => $shipping->order->updated_at->toIso8601String(),
                        'status'     => 'TELAH DITERIMA',
                        'note'       => 'Paket telah diterima oleh customer.'
                    ]
                ];
                return response()->json([
                    'success' => true,
                    'data' => [
                        'courier' => [
                            'company' => strtoupper($shipping->courier_company),
                            'waybill_id' => $shipping->tracking_number
                        ],
                        'status' => 'delivered',
                        'history' => $history
                    ]
                ]);
            }
            return response()->json(['success' => false, 'message' => 'System Error: ' . $e->getMessage()]);
        }
    }
}

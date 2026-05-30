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
    // Display list of shipping details with filters
    public function index(Request $request)
    {
        $query = ShippingDetail::with('order.user');

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
        $couriers = ShippingDetail::distinct()->pluck('courier_company');

        return view('admin.shippings.index', compact('shippings', 'couriers'));
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
                $notifMsg = "Pesanan Anda #{$shipping->order->invoice_number} telah diselesaikan. Terima kasih telah berbelanja di BigSport!";
            }

            UserNotification::create([
                'user_id' => $shipping->order->user_id,
                'type'    => 'order_status',
                'title'   => $notifTitle,
                'message' => $notifMsg,
                'is_read' => 0,
            ]);
        });

        return redirect()->route('admin.shippings.index')
            ->with('success', 'Detail pengiriman dan status pesanan berhasil diperbarui.');
    }

    // Book Biteship 자동으로 주문생성 및 픽업 요청
    public function bookBiteship($id)
    {
        $shipping = ShippingDetail::with(['order.user', 'order.items.sku.product'])->findOrFail($id);
        $order = $shipping->order;

        if (!$order) {
            return redirect()->back()->with('error', 'Pesanan tidak ditemukan.');
        }

        if ($shipping->biteship_order_id) {
            return redirect()->back()->with('error', 'Pesanan ini sudah dibooking di Biteship.');
        }

        if (empty($shipping->courier_company) || empty($shipping->courier_type)) {
            return redirect()->back()->with('error', 'Gagal memproses: Informasi layanan kurir atau tipe kurir tidak lengkap/kosong. Silakan edit pengiriman untuk melengkapi data kurir terlebih dahulu.');
        }

        if (strtolower($shipping->courier_company) === 'pickup') {
            return redirect()->back()->with('error', 'Pesanan dengan kurir Toko/Ambil Sendiri tidak dapat dibooking di Biteship.');
        }

        // Get address model
        $address = \App\Models\Address::find($order->address_id);
        if (!$address) {
            return redirect()->back()->with('error', 'Alamat pengiriman pelanggan tidak ditemukan.');
        }

        if (empty($address->district_id)) {
            return redirect()->back()->with('error', 'Kecamatan tujuan pengiriman tidak valid / tidak teridentifikasi di Biteship.');
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
            'shipper_contact_name' => 'BigSport Store',
            'shipper_contact_phone' => '081234567890',
            'shipper_contact_email' => 'admin@bigsport.com',
            'shipper_organization' => 'BigSport Indonesia',
            'origin_contact_name' => 'BigSport Store',
            'origin_contact_phone' => '081234567890',
            'origin_address' => 'Jl. HOS Cokroaminoto No.52, Larangan',
            'origin_note' => 'Toko BigSport Larangan',
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

        try {
            $apiKey = env('BITESHIP_API_KEY');
            if (empty($apiKey)) {
                return redirect()->back()->with('error', 'BITESHIP_API_KEY tidak diatur di file .env.');
            }

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

                    // Automatically transition status to processing or shipped
                    $newStatus = $waybillId ? 'shipped' : 'processing';
                    $order->update(['status' => $newStatus]);

                    // Send customer notification
                    $notifTitle = 'Pesanan Sedang Diproses Biteship 🚚';
                    $notifMsg = "Yay! Pengiriman untuk pesanan Anda #{$order->invoice_number} berhasil dibooking otomatis ke Biteship (ID: {$biteshipOrderId}).";
                    
                    if ($waybillId) {
                        $notifTitle = 'Pesanan Sedang Dikirim 🚚';
                        $notifMsg = "Pesanan Anda #{$order->invoice_number} sedang dikirim oleh kurir " . strtoupper($shipping->courier_company) . " dengan nomor resi: {$waybillId}.";
                    }

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

                return redirect()->back()->with('success', $successMsg);
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

                return redirect()->back()->with('success', "Booking Biteship berhasil (Sandbox Fallback)! Nomor resi kurir otomatis terisi: {$waybillId}.");
            }

            return redirect()->back()->with('error', 'Biteship API Error: ' . $errorMsg);

        } catch (\Exception $e) {
            Log::error('Biteship Request Exception: ' . $e->getMessage());

            // Sandbox fallback on connection error/exceptions
            if (isset($apiKey) && str_starts_with($apiKey, 'biteship_test.')) {
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

                return redirect()->back()->with('success', "Booking Biteship berhasil (Sandbox Fallback)! Nomor resi kurir otomatis terisi: {$waybillId}.");
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductSku;
use App\Models\Payment;
use App\Models\UserNotification;
use App\Models\ShippingDetail;
use App\Models\Address;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        Log::info('--- WEBHOOK MIDTRANS MASUK ---');
        Log::info($request->all());

        $serverKey = config('midtrans.server_key');
        if (!$serverKey) {
            Log::error('GAGAL: Server Key Midtrans di .env kosong!');
            return response()->json(['message' => 'Server Key missing'], 500);
        }

        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            
            DB::beginTransaction();
            try {
                $order = Order::where('invoice_number', $request->order_id)->lockForUpdate()->first();

                if (!$order) {
                    throw new \Exception("Order tidak ditemukan di sistem.");
                }

                $transactionStatus = $request->transaction_status;
                $paymentType = $request->payment_type;
                $bankName = null;

                if ($paymentType == 'bank_transfer' && isset($request->va_numbers[0]['bank'])) {
                    $bankName = strtoupper($request->va_numbers[0]['bank']);
                    $paymentType = 'bank_transfer (' . $bankName . ')';
                } elseif ($paymentType == 'echannel') {
                    $bankName = 'MANDIRI';
                    $paymentType = 'bank_transfer (MANDIRI)';
                } elseif ($paymentType == 'cstore' && isset($request->store)) {
                    $storeName = strtoupper($request->store);
                    $bankName = $storeName;
                    $paymentType = 'cstore (' . $storeName . ')';
                }

                // IDEMPOTENCY CHECK
                if (in_array($order->payment_status, ['paid', 'failed', 'expired', 'refunded'])) {
                    Log::info("IDEMPOTENCY: Order {$order->invoice_number} sudah diproses ({$order->payment_status}). Webhook diabaikan.");
                    DB::commit();
                    return response()->json(['message' => 'Sudah diproses sebelumnya']);
                }

                Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'midtrans_transaction_id' => $request->transaction_id,
                        'payment_type' => $paymentType,
                        'payment_status' => $transactionStatus,
                        'bank_name' => $bankName,
                        'gross_amount' => $request->gross_amount,
                    ]
                );

                $orderItems = OrderItem::where('order_id', $order->id)->get();

                // 🌟 1. PEMBAYARAN LUNAS (SUKSES)
                if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed',
                        'payment_type' => $paymentType
                    ]);

                    UserNotification::create([
                        'user_id' => $order->user_id,
                        'type'    => 'transaksi',
                        'title'   => 'Pembayaran Berhasil',
                        'message' => "Pembayaran pesanan #{$order->invoice_number} sebesar Rp " . number_format($order->grand_total, 0, ',', '.') . " telah kami terima. Pesanan Anda akan segera dikemas."
                    ]);

                    // RESOLUSI STOK (SUKSES)
                    foreach ($orderItems as $item) {
                        ProductSku::where('id', $item->product_sku_id)
                            ->lockForUpdate()
                            ->update([
                                'stock' => DB::raw("stock - {$item->quantity}"),
                                'reserved_stock' => DB::raw("reserved_stock - {$item->quantity}")
                            ]);
                    }

                    // 🌟 BITESHIP: BUAT WAYBILL SETELAH PEMBAYARAN SUKSES
                    $this->createBiteshipOrder($order, $orderItems);

                    Log::info("SUKSES: Order {$request->order_id} LUNAS. Stok berhasil diselesaikan.");
                }
                
                // 🌟 2. KEDALUWARSA / GAGAL / DITOLAK
                elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                    $newPaymentStatus = ($transactionStatus == 'expire') ? 'expired' : 'failed';
                    $order->update([
                        'payment_status' => $newPaymentStatus,
                        'status' => 'cancelled',
                        'payment_type' => $paymentType
                    ]);

                    UserNotification::create([
                        'user_id' => $order->user_id,
                        'type'    => 'transaksi',
                        'title'   => 'Pesanan Dibatalkan',
                        'message' => "Pesanan #{$order->invoice_number} telah dibatalkan karena melewati batas waktu pembayaran atau dibatalkan oleh sistem."
                    ]);

                    // RESOLUSI STOK (GAGAL)
                    foreach ($orderItems as $item) {
                        ProductSku::where('id', $item->product_sku_id)
                            ->lockForUpdate()
                            ->update([
                                'reserved_stock' => DB::raw("reserved_stock - {$item->quantity}")
                            ]);
                    }
                    Log::info("BATAL: Order {$request->order_id} {$transactionStatus}. Reserved Stock dikembalikan ke toko.");
                }
                
                // 🌟 3. PENDING (MENUNGGU PEMBAYARAN)
                elseif ($transactionStatus == 'pending') {
                    $order->update([
                        'payment_status' => 'pending',
                        'payment_type' => $paymentType
                    ]);
                }

                DB::commit();
                return response()->json(['message' => 'Callback diproses dengan sukses']);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("WEBHOOK ERROR: " . $e->getMessage());
                return response()->json(['message' => 'Terjadi kesalahan sistem'], 500);
            }

        } else {
            Log::error('Signature Key Midtrans Tidak Valid untuk Order: ' . $request->order_id);
            return response()->json(['message' => 'Invalid signature'], 403);
        }
    }

    /**
     * 🌟 Buat order pengiriman di Biteship SETELAH pembayaran sukses
     */
    private function createBiteshipOrder(Order $order, $orderItems)
    {
        try {
            $shippingDetail = ShippingDetail::where('order_id', $order->id)->first();

            // Hanya proses jika bukan pickup
            if (!$shippingDetail || $shippingDetail->courier_company === 'pickup') {
                return;
            }

            // Ambil alamat user
            $address = Address::find($order->address_id);
            if (!$address) {
                Log::warning("Biteship: Address tidak ditemukan untuk Order #{$order->invoice_number}");
                return;
            }

            // Map order items ke format Biteship
            $items = [];
            foreach ($orderItems as $item) {
                $items[] = [
                    'name'        => $item->product_name,
                    'description' => "Size: " . $item->product_size,
                    'value'       => (int) $item->price_at_purchase,
                    'weight'      => 500,
                    'quantity'    => (int) $item->quantity,
                ];
            }

            // Bersihkan nomor telepon
            $cleanSenderPhone = '08123456789';
            $cleanReceiverPhone = preg_replace('/[^0-9]/', '', $address->receiver_phone);
            if (strlen($cleanReceiverPhone) < 10) {
                $cleanReceiverPhone = '08' . str_pad($cleanReceiverPhone, 8, '0', STR_PAD_RIGHT);
            }

            // PERBAIKAN: Susun ulang alamat lengkap untuk dikirim ke kurir
            $destinationParts = array_filter([
                $address->full_address,
                $address->village_name ?? null,   // tambah _name
                $address->district_name ?? null,  // tambah _name
                $address->city_name ?? null,      // tambah _name
                $address->province_name ?? null,  // tambah _name
                $address->postal_code ?? null
            ]);
            $completeDestinationAddress = implode(', ', $destinationParts);

            // Payload ke Biteship
            $biteshipPayload = [
                'shipper_contact_name'  => 'Big Sport Tangerang',
                'shipper_contact_phone' => $cleanSenderPhone,
                'origin_contact_name'   => 'Big Sport Tangerang',
                'origin_contact_phone'  => $cleanSenderPhone,
                'origin_address'        => 'Jl. HOS Cokroaminoto No.52, Larangan, Tangerang',
                'origin_postal_code'    => 15710,
                'destination_contact_name'  => $address->receiver_name,
                'destination_contact_phone' => $cleanReceiverPhone,
                'destination_address'       => $completeDestinationAddress,
                'destination_postal_code'   => (int) $address->postal_code,
                'courier_company' => $shippingDetail->courier_company,
                'courier_type'    => $shippingDetail->courier_type,
                'items'           => $items,
                'delivery_type'   => 'now',
                'order_note'      => 'Order ' . $order->invoice_number
            ];

            $biteshipResponse = Http::timeout(10)->withHeaders([
                'authorization' => env('BITESHIP_API_KEY'),
                'content-type'  => 'application/json'
            ])->post('https://api.biteship.com/v1/orders', $biteshipPayload);

            if ($biteshipResponse->successful()) {
                $biteshipData = $biteshipResponse->json();
                
                $shippingDetail->update([
                    'biteship_order_id' => $biteshipData['id'],
                    'tracking_number'   => $biteshipData['courier']['waybill_id'] ?? null
                ]);

                Log::info("BITESHIP SUKSES: Order #{$order->invoice_number} -> Waybill " . ($biteshipData['courier']['waybill_id'] ?? 'Belum tersedia'));
            } else {
                $errorBiteship = $biteshipResponse->json()['error'] ?? 'Gagal membuat pesanan kurir.';
                Log::error("BITESHIP GAGAL untuk Order #{$order->invoice_number}: " . json_encode($errorBiteship));
            }

        } catch (\Exception $e) {
            // Jangan throw error, cukup log. Webhook Midtrans tetap harus return 200
            Log::error("BITESHIP EXCEPTION untuk Order #{$order->invoice_number}: " . $e->getMessage());
        }
    }
}
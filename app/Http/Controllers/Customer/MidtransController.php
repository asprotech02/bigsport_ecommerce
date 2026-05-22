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
}
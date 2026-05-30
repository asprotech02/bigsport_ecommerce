<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'promo_id', // 🌟 Wajib ada untuk relasi diskon
        'invoice_number',
        'shipping_address',
        'total_product_price',
        'total_shipping_cost',
        'discount_amount',
        'grand_total',
        'snap_token',
        'payment_status',
        'payment_type',
        'status',
        'cancel_reason', // 🌟 WAJIB TAMBAHKAN BARIS INI!
        // 🌟 biteship_order_id, waybill_id, courier_company, courier_type DIHAPUS
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function address() { return $this->belongsTo(Address::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function payment() { return $this->hasOne(Payment::class); }
    public function shippingDetail() { return $this->hasOne(ShippingDetail::class); }
    
    // 🌟 Relasi Baru
    public function promo() { return $this->belongsTo(Promo::class); }

    /**
     * Sinkronisasi status pembayaran dengan Midtrans API secara on-demand (Solusi Webhook Localhost)
     */
    public static function syncMidtransStatus($order)
    {
        if (!in_array($order->payment_status, ['unpaid', 'pending'])) {
            return;
        }

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        try {
            $status = \Midtrans\Transaction::status($order->invoice_number);
            if (!$status) return;

            $transactionStatus = $status->transaction_status ?? null;
            $paymentType = $status->payment_type ?? null;
            $grossAmount = $status->gross_amount ?? $order->grand_total;
            $transactionId = $status->transaction_id ?? null;
            $bankName = null;

            if ($paymentType == 'bank_transfer' && isset($status->va_numbers[0]->bank)) {
                $bankName = strtoupper($status->va_numbers[0]->bank);
                $paymentType = 'bank_transfer (' . $bankName . ')';
            } elseif ($paymentType == 'echannel') {
                $bankName = 'MANDIRI';
                $paymentType = 'bank_transfer (MANDIRI)';
            } elseif ($paymentType == 'cstore' && isset($status->store)) {
                $storeName = strtoupper($status->store);
                $bankName = $storeName;
                $paymentType = 'cstore (' . $storeName . ')';
            }

            if ($transactionStatus) {
                // Update atau Buat data Payment
                \App\Models\Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'midtrans_transaction_id' => $transactionId,
                        'payment_type' => $paymentType,
                        'payment_status' => $transactionStatus,
                        'bank_name' => $bankName,
                        'gross_amount' => $grossAmount,
                    ]
                );

                $orderItems = \App\Models\OrderItem::where('order_id', $order->id)->get();

                // 1. Jika Lunas
                if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed',
                        'payment_type' => $paymentType
                    ]);

                    // Potong stok utama & kurangi reserved
                    foreach ($orderItems as $item) {
                        \App\Models\ProductSku::where('id', $item->product_sku_id)
                            ->update([
                                'stock' => \Illuminate\Support\Facades\DB::raw("stock - {$item->quantity}"),
                                'reserved_stock' => \Illuminate\Support\Facades\DB::raw("reserved_stock - {$item->quantity}")
                            ]);
                    }

                    // Notifikasi User
                    \App\Models\UserNotification::create([
                        'user_id' => $order->user_id,
                        'type'    => 'transaksi',
                        'title'   => 'Pembayaran Berhasil',
                        'message' => "Pembayaran pesanan #{$order->invoice_number} sebesar Rp " . number_format($order->grand_total, 0, ',', '.') . " telah kami terima. Pesanan Anda akan segera dikemas."
                    ]);
                }
                // 2. Jika Kedaluwarsa / Gagal
                elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                    $newPaymentStatus = ($transactionStatus == 'expire') ? 'expired' : 'failed';
                    $order->update([
                        'payment_status' => $newPaymentStatus,
                        'status' => 'cancelled',
                        'payment_type' => $paymentType
                    ]);

                    // Kembalikan reserved stock
                    foreach ($orderItems as $item) {
                        \App\Models\ProductSku::where('id', $item->product_sku_id)
                            ->update([
                                'reserved_stock' => \Illuminate\Support\Facades\DB::raw("reserved_stock - {$item->quantity}")
                            ]);
                    }

                    \App\Models\UserNotification::create([
                        'user_id' => $order->user_id,
                        'type'    => 'transaksi',
                        'title'   => 'Pesanan Dibatalkan',
                        'message' => "Pesanan #{$order->invoice_number} telah dibatalkan karena melewati batas waktu pembayaran atau dibatalkan oleh sistem."
                    ]);
                }
                // 3. Jika Pending
                elseif ($transactionStatus == 'pending') {
                    $order->update([
                        'payment_status' => 'pending',
                        'payment_type' => $paymentType
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Abaikan error jika order belum dibuka snap token-nya di midtrans
        }
    }

    /**
     * Sinkronkan semua pesanan belum dibayar dalam 7 hari terakhir
     */
    public static function syncAllUnpaid()
    {
        try {
            $orders = self::whereIn('payment_status', ['unpaid', 'pending'])
                ->where('created_at', '>=', now()->subDays(7))
                ->get();
            foreach ($orders as $order) {
                self::syncMidtransStatus($order);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Midtrans On-Demand Sync Error: " . $e->getMessage());
        }
    }
}
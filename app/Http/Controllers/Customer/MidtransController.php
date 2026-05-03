<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem; // 🌟 WAJIB IMPORT
use App\Models\ProductSku; // 🌟 WAJIB IMPORT
use Illuminate\Support\Facades\Log; 

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        // 1. CATAT SEMUA DATA YANG MASUK KE LOG
        Log::info('--- WEBHOOK MIDTRANS MASUK ---');
        Log::info($request->all());

        $serverKey = config('midtrans.server_key');
        
        if (!$serverKey) {
            Log::error('GAGAL: Server Key Midtrans di .env kosong!');
            return response()->json(['message' => 'Server Key missing'], 500);
        }

        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // 2. VALIDASI KEAMANAN
        if ($hashed == $request->signature_key) {
            Log::info('Keamanan Valid! Memproses Order: ' . $request->order_id);
            
            $order = Order::where('invoice_number', $request->order_id)->first();

            if ($order) {
                $transactionStatus = $request->transaction_status;
                $paymentType = $request->payment_type;

                // Mempercantik nama Metode Pembayaran
                if ($paymentType == 'bank_transfer' && isset($request->va_numbers[0]['bank'])) {
                    $bankName = strtoupper($request->va_numbers[0]['bank']);
                    $paymentType = $paymentType . ' (' . $bankName . ')'; 
                } 
                elseif ($paymentType == 'echannel') {
                    $paymentType = 'bank_transfer (MANDIRI)';
                }
                elseif ($paymentType == 'cstore' && isset($request->store)) {
                    $storeName = strtoupper($request->store);
                    $paymentType = $paymentType . ' (' . $storeName . ')';
                }

                // 🌟 JIKA PEMBAYARAN SUKSES
                if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                    
                    // 🛡️ Perlindungan: Pastikan stok HANYA dipotong jika status sebelumnya belum 'paid'
                    if ($order->payment_status !== 'paid') {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing',
                            'payment_type' => $paymentType 
                        ]);

                        // 🔥 LOGIKA POTONG STOK OTOMATIS 🔥
                        $orderItems = OrderItem::where('order_id', $order->id)->get();
                        
                        foreach ($orderItems as $item) {
                            // Cari Sku terkait, lalu kurangi (decrement) stoknya sesuai quantity yang dibeli
                            ProductSku::where('id', $item->product_sku_id)
                                ->decrement('stock', $item->quantity);
                        }

                        Log::info('SUKSES: Order ' . $request->order_id . ' PAID. Stok Berhasil Dikurangi.');
                    }
                } 
                // 🌟 JIKA PEMBAYARAN GAGAL / BATAL / KEDALUWARSA
                elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                    $order->update([
                        'payment_status' => 'failed',
                        'status' => 'cancelled',
                        'payment_type' => $paymentType 
                    ]);
                    Log::info('DIBATALKAN: Order ' . $request->order_id . ' gagal/expired.');
                } 
                // 🌟 JIKA PEMBAYARAN PENDING (Menunggu dibayar)
                elseif ($transactionStatus == 'pending') {
                    $order->update([
                        'payment_status' => 'unpaid',
                        'payment_type' => $paymentType 
                    ]);
                }
            }

            return response()->json(['message' => 'Callback received successfully']);
        }
    }
}
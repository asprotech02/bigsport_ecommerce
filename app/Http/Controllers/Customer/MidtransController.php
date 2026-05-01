<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log; // Wajib ada

class MidtransController extends Controller
{
    public function callback(Request $request)
        {
            // 1. CATAT SEMUA DATA YANG MASUK KE LOG
            Log::info('--- WEBHOOK MIDTRANS MASUK ---');
            Log::info($request->all());

            $serverKey = config('midtrans.server_key');
            
            // Pastikan Server Key tidak kosong
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
                    
                    // --- LOGIKA BARU BUAT NANGKAP DETAIL BANK ---
                    $paymentType = $request->payment_type;

                    // Jika tipe bayarnya bank_transfer (BCA, BNI, BRI, dll)
                    if ($paymentType == 'bank_transfer' && isset($request->va_numbers[0]['bank'])) {
                        $bankName = strtoupper($request->va_numbers[0]['bank']);
                        $paymentType = $paymentType . ' (' . $bankName . ')'; 
                        // Hasilnya: "bank_transfer (BCA)"
                    } 
                    // Jika tipe bayarnya echannel (Ini khusus Mandiri Bill)
                    elseif ($paymentType == 'echannel') {
                        $paymentType = 'bank_transfer (MANDIRI)';
                    }
                    // Jika bayar di minimarket (Indomaret/Alfamart)
                    elseif ($paymentType == 'cstore' && isset($request->store)) {
                        $storeName = strtoupper($request->store);
                        $paymentType = $paymentType . ' (' . $storeName . ')';
                        // Hasilnya: "cstore (ALFAMART)"
                    }
                    // ---------------------------------------------

                    // Sekarang kita pake variabel $paymentType yang udah dimodifikasi
                    if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing',
                            'payment_type' => $paymentType // 👈 Pakai variabel baru
                        ]);
                        Log::info('SUKSES: Order ' . $request->order_id . ' berhasil diupdate ke PAID.');
                    } 
                    elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                        $order->update([
                            'payment_status' => 'failed',
                            'status' => 'cancelled',
                            'payment_type' => $paymentType // 👈 Pakai variabel baru
                        ]);
                        Log::info('DIBATALKAN: Order ' . $request->order_id . ' gagal/expired.');
                    } 
                    elseif ($transactionStatus == 'pending') {
                        $order->update([
                            'payment_status' => 'unpaid',
                            'payment_type' => $paymentType // 👈 Pakai variabel baru
                        ]);
                    }
                }

            return response()->json(['message' => 'Callback received successfully']);
        }
    }
}
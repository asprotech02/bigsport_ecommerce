<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\ShippingDetail;
use App\Models\UserNotification;
use App\Events\RealTimeNotification;

class BiteshipWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Log::info('--- WEBHOOK BITESHIP MASUK ---', $request->all());

        $event = $request->input('event');
        
        if ($event === 'order.status' || $event === 'waybill.status' || $event === 'tracking.status') {
            
            // 🌟 PERBAIKAN: Tangkap ID dari berbagai kemungkinan struktur JSON Biteship
            $biteshipOrderId = $request->input('order_id') ?? $request->input('id'); 
            $waybillId = $request->input('waybill_id') ?? $request->input('courier.waybill_id');     
            $newStatus = strtolower($request->input('status')); 

            // 🌟 PERBAIKAN: Cegah robot asal comot data jika ID kosong
            if (empty($biteshipOrderId) && empty($waybillId)) {
                Log::error("Webhook Biteship: Payload tidak memiliki Order ID atau Resi.");
                return response()->json(['message' => 'Missing ID'], 400);
            }

            // Cari Order dengan akurasi tinggi
            $shippingDetail = null;
            if (!empty($biteshipOrderId)) {
                $shippingDetail = ShippingDetail::where('biteship_order_id', $biteshipOrderId)->first();
            }
            if (!$shippingDetail && !empty($waybillId)) {
                $shippingDetail = ShippingDetail::where('tracking_number', $waybillId)->first();
            }

            if (!$shippingDetail) {
                Log::warning("Webhook Biteship: Data pengiriman untuk pesanan ini tidak ditemukan di database.");
                return response()->json(['message' => 'Order not found'], 404);
            }

            $order = Order::find($shippingDetail->order_id);
            if (!$order) {
                return response()->json(['message' => 'Order main not found'], 404);
            }

            // =========================================================
            // 🌟 LOGIKA ROBOT: UBAH STATUS DATABASE BERDASARKAN KURIR
            // =========================================================

            // A. Kurir menjemput / di jalan -> Ubah ke SHIPPED
            if (in_array($newStatus, ['allocated', 'picking_up', 'picked', 'dropping_off'])) {
                
                if ($order->status !== 'shipped') {
                    $order->update(['status' => 'shipped']);
                    
                    // Tembakkan Pop-up Reverb
                    $notif = UserNotification::create([
                        'user_id' => $order->user_id,
                        'type'    => 'transaksi',
                        'title'   => 'Pesanan Sedang Dikirim 🚚',
                        'message' => "Hore! Pesanan #{$order->invoice_number} sedang dalam perjalanan menuju alamat Anda oleh kurir."
                    ]);
                    try {
                        broadcast(new RealTimeNotification($notif));
                    } catch (\Exception $e) {
                        Log::warning("BiteshipWebhook RealTimeNotification (shipped) failed: " . $e->getMessage());
                    }
                }
            } 
            
            // B. Kurir menyatakan sudah sampai -> Ubah ke COMPLETED
            elseif ($newStatus === 'delivered') {
                
                if ($order->status !== 'completed') {
                    $order->update(['status' => 'completed']);
                    
                    // Tembakkan Pop-up Reverb
                    $notif = UserNotification::create([
                        'user_id' => $order->user_id,
                        'type'    => 'transaksi',
                        'title'   => 'Paket Telah Diterima 📦',
                        'message' => "Paket untuk pesanan #{$order->invoice_number} telah berhasil dikirim. Jangan lupa beri ulasan terbaikmu!"
                    ]);
                    try {
                        broadcast(new RealTimeNotification($notif));
                    } catch (\Exception $e) {
                        Log::warning("BiteshipWebhook RealTimeNotification (completed) failed: " . $e->getMessage());
                    }
                }
            }

            return response()->json(['message' => 'Webhook berhasil diproses']);
        }

        return response()->json(['message' => 'Event diabaikan']);
    }
}
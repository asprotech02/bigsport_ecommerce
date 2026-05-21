<?php

namespace App\Jobs;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupExpiredOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Cari order unpaid/pending yang usianya lewat 24 jam
        $expiredOrders = Order::with('items.sku')
            ->whereIn('payment_status', ['unpaid', 'pending'])
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '<', Carbon::now()->subHours(1))
            ->get();

        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order) {
                // 1. Batalkan pesanan
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'expired'
                ]);

                // 2. Kembalikan reserved_stock ke stock asli (Penting buat mencegah barang ghaib!)
                foreach ($order->items as $item) {
                    $sku = $item->sku;
                    if ($sku) {
                        $sku->decrement('reserved_stock', $item->quantity);
                        $sku->increment('stock', $item->quantity);
                    }
                }
                
                Log::info("Pesanan #{$order->invoice_number} otomatis dibatalkan karena kadaluarsa. Stok dikembalikan.");
            });
        }
    }
}
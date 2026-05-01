<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // 🔥 WAJIB ADA

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $addresses = Address::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->get();

        $orders = Order::where('user_id', $user->id)
            ->with(['items.sku.product.images', 'items.sku.product.brand'])
            ->latest()
            ->get();

        return view('customer.pages.profile', compact('user', 'orders', 'addresses'));
    }

    /**
     * Fungsi tunggal untuk mengambil detail tracking dari Biteship.
     */
    public function getTracking($id) {
        $order = \App\Models\Order::where('user_id', auth()->id())->findOrFail($id);

        if (!$order->biteship_order_id) {
            return response()->json([
                'success' => false, 
                'message' => 'Pesanan sedang diproses. Resi akan muncul otomatis setelah kurir melakukan pick-up.'
            ]);
        }

        // 🌟 JIKA MENGGUNAKAN DUMMY ID (BYPASS MODE)
        if (str_starts_with($order->biteship_order_id, 'bts_dummy')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'allocated',
                    'courier' => [
                        'company' => $order->courier_company ?? 'Kurir',
                        'waybill_id' => $order->waybill_id
                    ],
                    'history' => [
                        [
                            'note' => 'Kurir sedang menuju lokasi penjemputan (Simulasi)',
                            'updated_at' => now()->toIso8601String(),
                            'status' => 'allocated'
                        ],
                        [
                            'note' => 'Pesanan berhasil dibuat (Simulasi)',
                            'updated_at' => now()->subMinutes(10)->toIso8601String(),
                            'status' => 'created'
                        ]
                    ]
                ]
            ]);
        }

        // 🌟 JIKA ID ASLI, TEMBAK KE API BITESHIP
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'authorization' => env('BITESHIP_API_KEY'),
        ])->get("https://api.biteship.com/v1/orders/{$order->biteship_order_id}");

        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['courier']['waybill_id']) && !$order->waybill_id) {
                $order->update(['waybill_id' => $data['courier']['waybill_id']]);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        return response()->json([
            'success' => false, 
            'message' => 'Gagal mengambil data dari server kurir.'
        ]);
    }
}
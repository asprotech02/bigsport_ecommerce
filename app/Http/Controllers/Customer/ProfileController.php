<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // 🔥 WAJIB ADA
use Barryvdh\DomPDF\Facade\Pdf;

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
     * 1. Fungsi Mengambil Tracking Live dari API Biteship
     */
    public function getTracking($id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        if (!$order->waybill_id || !$order->courier_company) {
            return response()->json([
                'success' => false,
                'message' => 'Resi belum tersedia untuk pesanan ini.'
            ]);
        }

        try {
            // 🌟 FIX 1: Tambahkan withoutVerifying() untuk mem-bypass error SSL cURL di Localhost Laragon
            $response = Http::withoutVerifying()->withHeaders([
                'authorization' => env('BITESHIP_API_KEY')
            ])->get("https://api.biteship.com/v1/trackings/{$order->waybill_id}/couriers/{$order->courier_company}");

            $result = $response->json();

            if ($response->successful() && isset($result['success']) && $result['success'] == true) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'courier' => [
                            'company' => strtoupper($result['courier']['company'] ?? $order->courier_company),
                            'waybill_id' => $result['waybill_id'] ?? $order->waybill_id
                        ],
                        'status' => $result['status'] ?? 'Diproses',
                        'history' => $result['history'] ?? []
                    ]
                ]);
            }

            // 🌟 FIX 2: Tampilkan pesan error langsung dari Biteship jika resi tidak valid
            return response()->json([
                'success' => false,
                'message' => 'Biteship: ' . ($result['error'] ?? 'Data tracking tidak ditemukan.')
            ]);

        } catch (\Exception $e) {
            // 🌟 FIX 3: Tampilkan pesan sistem asli agar kita tau errornya apa (Bukan cuma "Gagal menghubungi...")
            return response()->json([
                'success' => false,
                'message' => 'System Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 2. Fungsi Cetak Invoice PDF
     */
    public function printInvoice($id)
    {
        // 🌟 FIX: Hapus relasi yang tidak dipakai/salah nama. 
        // Kita cukup butuh tabel relasi 'items' dan 'user' saja karena data lainnya sudah berupa snapshot.
        $order = Order::with(['items', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Pastikan hanya status tertentu yang bisa cetak invoice
        if (!in_array($order->status, ['processing', 'shipped', 'completed'])) {
            return redirect()->back()->with('error', 'Invoice belum tersedia untuk pesanan ini.');
        }

        // Load view PDF
        $pdf = Pdf::loadView('customer.pages.invoice_pdf', compact('order'));
        
        // Return download PDF dengan nama file dinamis
        return $pdf->download('INVOICE-' . $order->invoice_number . '.pdf');
    }
}
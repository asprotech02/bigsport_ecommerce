<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingDetail;
use App\Models\Order;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}

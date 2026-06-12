<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingDetail;
use App\Models\Payment;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    // List all orders with filters and pagination
    public function index(Request $request)
    {
        // 🌟 Sinkronisasi status pembayaran dengan Midtrans secara otomatis di localhost
        \App\Models\Order::syncAllUnpaid();

        $query = Order::with(['items.sku.product', 'shippingDetail', 'payment', 'user']);

        // Search invoice or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter range date
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        // Filter status order
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    // Show detailed view of a single order
    public function show($id)
    {
        $order = Order::with(['items.sku.product.images', 'shippingDetail', 'payment', 'user'])
            ->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,preparing,shipped,delivered,completed,cancelled',
            'payment_status' => 'required|in:unpaid,pending,paid,failed,expired,refunded',
        ]);

        $order = Order::with(['shippingDetail', 'user', 'items.sku'])->findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;
        $oldPaymentStatus = $order->payment_status;
        $newPaymentStatus = $request->payment_status;

        DB::transaction(function () use ($order, $newStatus, $oldStatus, $newPaymentStatus, $oldPaymentStatus) {
            $order->update([
                'status' => $newStatus,
                'payment_status' => $newPaymentStatus,
            ]);

            // Restore Stock if order is being cancelled
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                $isPaid = ($oldPaymentStatus === 'paid');
                foreach ($order->items as $item) {
                    $sku = $item->sku;
                    if ($sku) {
                        if ($isPaid) {
                            // KASUS: Pesanan sudah dibayar, stok utama sudah terpotong
                            // Aksi: Kembalikan stok ke etalase (Increment Stok Utama)
                            $sku->increment('stock', $item->quantity);
                        } else {
                            // KASUS: Pesanan belum dibayar, baru reserved
                            // Aksi: Kurangi angka reserved_stock, tapi pastikan tidak pernah di bawah 0
                            $currentReserved = $sku->reserved_stock;
                            $amountToDecrement = min($item->quantity, $currentReserved);
                            if ($amountToDecrement > 0) {
                                $sku->decrement('reserved_stock', $amountToDecrement);
                            }
                        }
                    }
                }
            }

            // Synchronize with payments table if exists
            $payment = Payment::where('order_id', $order->id)->first();
            if ($payment) {
                $payment->update([
                    'payment_status' => $newPaymentStatus
                ]);
            }

            // Trigger Automatic Notifications based on shipping/order status flow
            $notifTitle = 'Status Pesanan Diupdate';
            $notifMsg = "Pesanan Anda #{$order->invoice_number} telah diupdate menjadi " . strtoupper($newStatus) . ".";

            if ($newStatus == 'processing' || $newStatus == 'preparing') {
                $notifTitle = 'Pesanan Sedang Disiapkan 📦';
                $notifMsg = "Pesanan Anda #{$order->invoice_number} sedang disiapkan oleh tim logistik BigSport.";
            } elseif ($newStatus == 'shipped') {
                $courier = $order->shippingDetail->courier_company ?? 'Kurir';
                $resi = $order->shippingDetail->tracking_number ?? '-';
                $notifTitle = 'Pesanan Sedang Dikirim 🚚';
                $notifMsg = "Pesanan Anda #{$order->invoice_number} telah diserahkan ke kurir " . strtoupper($courier) . " dengan nomor resi: {$resi}. Lacak pengiriman Anda sekarang!";
            } elseif ($newStatus == 'delivered') {
                $notifTitle = 'Pesanan Telah Sampai 🏠';
                $notifMsg = "Pesanan Anda #{$order->invoice_number} telah sampai di alamat tujuan. Silakan konfirmasi penyelesaian pesanan.";
            } elseif ($newStatus == 'completed') {
                $notifTitle = 'Pesanan Selesai 🎉';
                $notifMsg = "Pesanan Anda #{$order->invoice_number} telah selesai. Terima kasih telah berbelanja di BigSport!";
            }

            // Create notification row
            UserNotification::create([
                'user_id' => $order->user_id,
                'type'    => 'order_status',
                'title'   => $notifTitle,
                'message' => $notifMsg,
                'is_read' => 0,
            ]);
        });

        return redirect()->back()
            ->with('success', 'Status pesanan & pembayaran berhasil diperbarui.');
    }

    // Download PDF Invoice (admin)
    public function printInvoice($id)
    {
        $order = Order::with(['items', 'user'])->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('customer.pages.invoice_pdf', compact('order'));
        return $pdf->download('INVOICE-' . $order->invoice_number . '.pdf');
    }

    // Soft-delete an order (only admin)
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('admin.orders.index')
            ->with('success', 'Pesanan berhasil dihapus.');
    }
}

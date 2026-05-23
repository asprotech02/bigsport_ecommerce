<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('order.user')->orderByDesc('created_at');

        // Search payment by Midtrans Transaction ID or Invoice Number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('midtrans_transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('order', function($oq) use ($search) {
                      $oq->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%");
                        });
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

        // Get unique banks and types for filter dropdowns
        $availableBanks = Payment::whereNotNull('bank_name')->distinct()->pluck('bank_name');
        $availableTypes = Payment::whereNotNull('payment_type')->distinct()->pluck('payment_type');

        $payments = $query->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('payments', 'availableBanks', 'availableTypes'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,pending,paid,failed,expired,refunded,settlement',
        ]);

        $payment = Payment::findOrFail($id);
        $newStatus = $request->payment_status;

        // Map settlement to paid to ensure consistency
        if ($newStatus === 'settlement') {
            $newStatus = 'paid';
        }

        DB::transaction(function () use ($payment, $newStatus) {
            $payment->update([
                'payment_status' => $newStatus,
            ]);

            // Synchronize with the Order payment_status if order exists
            if ($payment->order) {
                $payment->order->update([
                    'payment_status' => $newStatus,
                ]);

                // Also trigger User Notification for Payment Update
                $notifTitle = 'Pembayaran Diterima ✅';
                $notifMsg = "Pembayaran untuk pesanan #{$payment->order->invoice_number} telah berhasil dikonfirmasi.";

                if ($newStatus == 'failed') {
                    $notifTitle = 'Pembayaran Gagal ❌';
                    $notifMsg = "Pembayaran untuk pesanan #{$payment->order->invoice_number} gagal diproses.";
                } elseif ($newStatus == 'refunded') {
                    $notifTitle = 'Pembayaran Refunded 💰';
                    $notifMsg = "Dana pembayaran untuk pesanan #{$payment->order->invoice_number} telah di-refund oleh admin.";
                }

                \App\Models\UserNotification::create([
                    'user_id' => $payment->order->user_id,
                    'type'    => 'payment_status',
                    'title'   => $notifTitle,
                    'message' => $notifMsg,
                    'is_read' => 0,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}

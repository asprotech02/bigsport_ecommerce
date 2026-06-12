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
        // 🌟 Sinkronisasi status pembayaran dengan Midtrans secara otomatis di localhost
        \App\Models\Order::syncAllUnpaid();

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
        abort(403, 'Aksi ubah status pembayaran secara manual dinonaktifkan demi integritas data Midtrans.');
    }

    public function syncAll()
    {
        try {
            \App\Models\Order::syncAllUnpaid();
            return redirect()->back()->with('success', 'Sinkronisasi status pembayaran dengan Midtrans berhasil dijalankan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal melakukan sinkronisasi: ' . $e->getMessage());
        }
    }
}

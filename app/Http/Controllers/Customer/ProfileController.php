<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // 🔥 WAJIB ADA
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\UserNotification;


class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)->orderByDesc('is_default')->get();
        
        // 🌟 FIX: Pastikan memuat relasi shippingDetail agar resi bisa dibaca di View[cite: 2]
        $orders = Order::with(['items.sku.product.images', 'items.sku.product.brand', 'shippingDetail'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('customer.pages.profile', compact('user', 'orders', 'addresses'));
    }

    public function getTracking($id)
    {
        // 🌟 FIX: Panggil shippingDetail[cite: 2]
        $order = Order::with('shippingDetail')->where('user_id', Auth::id())->findOrFail($id);
        $shipping = $order->shippingDetail;

        if (!$shipping || !$shipping->tracking_number || !$shipping->courier_company) {
            return response()->json([
                'success' => false,
                'message' => 'Resi belum tersedia untuk pesanan ini.'
            ]);
        }

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'authorization' => env('BITESHIP_API_KEY')
            ])->get("https://api.biteship.com/v1/trackings/{$shipping->tracking_number}/couriers/{$shipping->courier_company}");

            $result = $response->json();

            if ($response->successful() && isset($result['success']) && $result['success'] == true) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'courier' => [
                            'company' => strtoupper($result['courier']['company'] ?? $shipping->courier_company),
                            'waybill_id' => $result['waybill_id'] ?? $shipping->tracking_number
                        ],
                        'status' => $result['status'] ?? 'Diproses',
                        'history' => $result['history'] ?? []
                    ]
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Biteship: ' . ($result['error'] ?? 'Data tracking tidak ditemukan.')]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'System Error: ' . $e->getMessage()]);
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

    /**
     * Memproses update data profil pengguna
     */
    public function updateProfile(\Illuminate\Http\Request $request)
    {
        // 1. Validasi inputan form
        $request->validate([
            'name'     => 'required|string|max:255',
            'birthday' => 'required|date',
            'gender'   => 'required|in:L,P',
        ]);

        // 2. Ambil data user yang sedang login
        $user = \Illuminate\Support\Facades\Auth::user();

        // 3. Update data ke database
        $user->update([
            'name'     => $request->name,
            'birthday' => $request->birthday,
            'gender'   => $request->gender,
        ]);

        // 4. Balikin ke halaman profil bawaan SPA lu
        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
    }


    /**
     * Memproses penggantian password dari dalam halaman profil (User Login)
     */
    public function updatePassword(\Illuminate\Http\Request $request)
    {
        // 1. Validasi inputan
        $request->validate([
            'old_password' => ['required', function ($attribute, $value, $fail) {
                if (!\Illuminate\Support\Facades\Hash::check($value, auth()->user()->password)) {
                    $fail('Password lama yang Anda masukkan salah');
                }
            }],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ], [
            'password.required' => 'Password baru wajib diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // 2. Update Password ke Database
        auth()->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password)
        ]);

        // 3. (Opsional) Keluarkan pesan sukses & kembalikan ke profil
        return redirect()->route('profile')->with('success', 'Password berhasil diperbarui!');
    }


    public function notifications()
    {
        // Ambil notifikasi user yang login, urutkan dari yang paling baru
        $notifications = \App\Models\UserNotification::where('user_id', auth()->id())
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('customer.pages.notification', compact('notifications'));
    }
}
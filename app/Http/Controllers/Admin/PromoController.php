<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    // List all promos
    public function index()
    {
        $promos = Promo::orderByDesc('created_at')->paginate(20);
        return view('admin.promos.index', compact('promos'));
    }

    // Show form to create a promo
    public function create()
    {
        return view('admin.promos.create');
    }

    // Store new promo
    public function store(Request $request)
    {
        $request->validate([
            'code'             => 'required|string|unique:promos,code',
            'type'             => 'required|in:fixed,percentage',
            'reward'           => 'required|numeric|min:0',
            'max_usage'        => 'required|integer|min:1',
            'min_order_amount' => 'required|numeric|min:0',
            'expires_at'       => 'nullable|date',
            'is_active'        => 'nullable|boolean',
        ]);

        $promo = Promo::create([
            'code'             => strtoupper($request->code),
            'type'             => $request->type,
            'reward'           => $request->reward,
            'max_usage'        => $request->max_usage,
            'min_order_amount' => $request->min_order_amount,
            'expires_at'       => $request->expires_at,
            'is_active'        => $request->has('is_active') ? 1 : 0,
            'used_count'       => 0,
        ]);

        if ($promo->is_active) {
            $rewardStr = ($promo->type === 'percentage') 
                ? $promo->reward . '%' 
                : 'Rp ' . number_format($promo->reward, 0, ',', '.');
                
            $title = 'Promo Baru Tersedia! 🏷️';
            $message = "Sedang ada promo baru menarik untukmu! Gunakan kode promo " . $promo->code . " untuk mendapatkan potongan " . $rewardStr . ". Buruan belanja!";
            
            $customers = \App\Models\User::where('role', 'customer')->get();
            
            if ($customers->isNotEmpty()) {
                // 1. Bulk insert ke database (Hanya 1 Query untuk seluruh user!)
                $notificationsData = [];
                $userIds = [];
                foreach ($customers as $customer) {
                    $userIds[] = $customer->id;
                    $notificationsData[] = [
                        'user_id'    => $customer->id,
                        'type'       => 'promo',
                        'title'      => $title,
                        'message'    => $message,
                        'is_read'    => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                \App\Models\UserNotification::insert($notificationsData);

                // 2. Broadcast ke seluruh user sekaligus (Hanya 1 Request Ke WebSockets/Reverb!)
                try {
                    broadcast(new \App\Events\RealTimePromoNotification($title, $message, $userIds));
                } catch (\Exception $e) {
                    \Log::warning("RealTimePromoNotification broadcast failed: " . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil ditambahkan.');
    }

    // Show edit form
    public function edit($id)
    {
        $promo = Promo::findOrFail($id);
        return view('admin.promos.edit', compact('promo'));
    }

    // Update promo
    public function update(Request $request, $id)
    {
        $promo = Promo::findOrFail($id);
        
        $request->validate([
            'code'             => 'required|string|unique:promos,code,' . $promo->id,
            'type'             => 'required|in:fixed,percentage',
            'reward'           => 'required|numeric|min:0',
            'max_usage'        => 'required|integer|min:1',
            'min_order_amount' => 'required|numeric|min:0',
            'expires_at'       => 'nullable|date',
            'is_active'        => 'nullable|boolean',
        ]);

        $promo->update([
            'code'             => strtoupper($request->code),
            'type'             => $request->type,
            'reward'           => $request->reward,
            'max_usage'        => $request->max_usage,
            'min_order_amount' => $request->min_order_amount,
            'expires_at'       => $request->expires_at,
            'is_active'        => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil diperbarui.');
    }

    // Delete promo (admin only)
    public function destroy($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->delete();
        
        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil dihapus.');
    }
}

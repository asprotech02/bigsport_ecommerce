<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    // 1. TAMBAHKAN FUNGSI INI BUAT NAMPILIN HALAMAN FORM
    public function create()
    {
        // Sesuaikan 'address_form' dengan lokasi/nama file blade lo.
        // Kalau file lo ada di resources/views/pages/customer/address_form.blade.php:
        return view('customer.pages.address_form'); 
    }

   public function store(Request $request)
{
    $request->validate([
        'receiver_name'  => 'required|string|max:255',
        'receiver_phone' => 'required|string|max:20',
        'province_id'    => 'required', 
        'city_id'        => 'required', 
        'district_id'    => 'required', 
        'postal_code'    => 'required|numeric',
        'full_address'   => 'required|string',
    ]);

    $user = Auth::user();

    // Set alamat lain jadi non-default jika user mencentang 'is_default'
    if ($request->has('is_default')) {
        Address::where('user_id', $user->id)->update(['is_default' => false]);
    }

    // 🔥 TRIK SAKTI YANG DIPERTAJAM: Cari ID Biteship
    // Kita gabungkan KODE POS agar pencarian Biteship 100% akurat tanpa meleset
    $searchQuery = $request->postal_code . ', ' . $request->district_name . ', ' . $request->city_name;
    
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'authorization' => env('BITESHIP_API_KEY'),
    ])->get("https://api.biteship.com/v1/maps/areas", [
        'input' => $searchQuery,
        'countries' => 'id'
    ]);

    $biteshipAreaId = null;
    if ($response->successful() && isset($response->json()['areas'][0])) {
        // Ambil ID pertama yang paling relevan (formatnya IDNP...)
        $biteshipAreaId = $response->json()['areas'][0]['id'];
    }

    // SIMPAN KE DATABASE
    Address::create([
        'user_id'        => $user->id,
        'receiver_name'  => $request->receiver_name,
        'receiver_phone' => $request->receiver_phone,
        'province_id'    => $request->province_id, 
        'province_name'  => $request->province_name,
        'city_id'        => $request->city_id,     
        'city_name'      => $request->city_name,
        // 🔥 Simpan ID Biteship (IDNP...) ke kolom district_id
        'district_id'    => $biteshipAreaId ?? $request->district_id, 
        'district_name'  => $request->district_name,
        'village_id'     => $request->village_id,
        'village_name'   => $request->village_name,
        'postal_code'    => $request->postal_code,
        'full_address'   => $request->full_address,
        'is_default'     => $request->has('is_default'),
    ]);

    $cartIds = $request->cart_ids ?? session('selected_cart_ids');

    if (!is_array($cartIds)) {
        $cartIds = [$cartIds]; 
    }

    // 🔥 PAKSA SIMPAN SESSION SEBELUM REDIRECT
    session(['selected_cart_ids' => $cartIds]);
    session()->save(); 

    return redirect()->route('checkout', ['cart_ids' => $cartIds])
        ->with('success', 'Alamat berhasil sinkron dengan Biteship!');
}

    public function setMain($id)
    {
        $userId = auth()->id();
        \App\Models\Address::where('user_id', $userId)->update(['is_default' => 0]);
        
        $address = \App\Models\Address::where('user_id', $userId)->findOrFail($id);
        $address->update(['is_default' => 1]);

        return response()->json(['success' => true, 'message' => 'Alamat utama diperbarui']);
    }

    public function destroy($id)
    {
        $address = \App\Models\Address::where('user_id', auth()->id())->findOrFail($id);
        
        if ($address->is_default) {
            return response()->json(['success' => false, 'message' => 'Alamat utama tidak bisa dihapus'], 400);
        }

        $address->delete();
        return response()->json(['success' => true, 'message' => 'Alamat dihapus']);
    }


    // Tambahkan di AddressController.php

public function edit($id)
{
    $address = Address::where('user_id', auth()->id())->findOrFail($id);
    return view('customer.pages.address_form', compact('address'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'receiver_name'  => 'required|string|max:255',
        'receiver_phone' => 'required|string|max:20',
        'postal_code'    => 'required|numeric',
        'full_address'   => 'required|string',
    ]);

    $address = Address::where('user_id', auth()->id())->findOrFail($id);

    // Set alamat lain jadi non-default jika user mencentang 'is_default'
    if ($request->has('is_default')) {
        Address::where('user_id', auth()->id())->update(['is_default' => false]);
    }

    // 🔥 LOGIKA BITESIP (Sama dengan store)
    $searchQuery = $request->postal_code . ', ' . $request->district_name . ', ' . $request->city_name;
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'authorization' => env('BITESHIP_API_KEY'),
    ])->get("https://api.biteship.com/v1/maps/areas", ['input' => $searchQuery, 'countries' => 'id']);

    $biteshipAreaId = null;
    if ($response->successful() && isset($response->json()['areas'][0])) {
        $biteshipAreaId = $response->json()['areas'][0]['id'];
    }

    $address->update([
        'receiver_name'  => $request->receiver_name,
        'receiver_phone' => $request->receiver_phone,
        'province_id'    => $request->province_id ?? $address->province_id,
        'province_name'  => $request->province_name ?? $address->province_name,
        'city_id'        => $request->city_id ?? $address->city_id,
        'city_name'      => $request->city_name ?? $address->city_name,
        'district_id'    => $biteshipAreaId ?? ($request->district_id ?? $address->district_id),
        'district_name'  => $request->district_name ?? $address->district_name,
        'village_id'     => $request->village_id ?? $address->village_id,
        'village_name'   => $request->village_name ?? $address->village_name,
        'postal_code'    => $request->postal_code,
        'full_address'   => $request->full_address,
        'is_default'     => $request->has('is_default'),
    ]);

    return redirect()->route('profile', ['tab' => 'alamat'])->with('success', 'Alamat berhasil diperbarui!');
}
}
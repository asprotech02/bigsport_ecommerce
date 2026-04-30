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
        // Sesuaikan 'address_edit' dengan lokasi/nama file blade lo.
        // Kalau file lo ada di resources/views/pages/customer/address_edit.blade.php:
        return view('pages.customer.address_edit'); 
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'receiver_name'  => 'required|string|max:255', // Sesuaikan nama field
            'receiver_phone' => 'required|string|max:20',  // Sesuaikan nama field
            'province_id'    => 'required',
            'city_id'        => 'required',
            'district_id'    => 'required',
            'postal_code'    => 'required|numeric',
            'full_address'   => 'required|string',         // Sesuaikan nama field
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();

        // 2. Set default false untuk alamat lama
        if ($request->has('is_default')) {
            \App\Models\Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

        // 3. Create langsung dari request (karena nama variabel form dan database udah sama)
        \App\Models\Address::create([
            'user_id'        => $user->id,
            'receiver_name'  => $request->receiver_name,
            'receiver_phone' => $request->receiver_phone,
            'province_id'    => $request->province_id,
            'province_name'  => $request->province_name, // Ini dapet dari hidden input JS
            'city_id'        => $request->city_id,
            'city_name'      => $request->city_name,     // Ini dapet dari hidden input JS
            'district_id'    => $request->district_id,
            'district_name'  => $request->district_name, // Ini dapet dari hidden input JS
            'postal_code'    => $request->postal_code,
            'full_address'   => $request->full_address,
            'is_default'     => $request->has('is_default') ? true : false,
        ]);

        return redirect()->route('checkout')->with('success', 'Alamat berhasil ditambahkan');
    }
}
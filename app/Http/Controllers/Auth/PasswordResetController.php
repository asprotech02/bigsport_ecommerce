<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    /**
     * Menangani proses pengiriman link reset password ke email user.
     */
    public function store(Request $request)
    {
        // 1. Validasi inputan harus berupa email yang valid
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // 2. Kirim link reset via SMTP bawaan Laravel
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // 3. Kembalikan ke halaman form dengan notifikasi sukses atau gagal
        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', 'Link reset password telah dikirim ke email Anda!');
        }

        return back()->withErrors(['email' => 'Mohon maaf, email tersebut tidak terdaftar di sistem kami.']);
    }
}
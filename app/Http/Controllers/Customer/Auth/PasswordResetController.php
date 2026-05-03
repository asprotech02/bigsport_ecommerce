<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller; // Namespace disesuaikan
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', 'Kami telah mengirimkan link reset password ke email Anda');
        }

        return back()->withErrors(['email' => 'Maaf kami tidak dapat menemukan akun dengan email tersebut']);
    }
}
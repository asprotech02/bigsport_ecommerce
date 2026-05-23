<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
// PERBAIKAN: Pastikan import namespace Request sudah sesuai dengan struktur baru Anda
use App\Http\Requests\Customer\Auth\LoginRequest; 
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('customer.pages.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if ($request->user() instanceof MustVerifyEmail && ! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if (in_array($request->user()->role, ['admin', 'sales', 'manager'])) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        return redirect()->intended(route('home', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
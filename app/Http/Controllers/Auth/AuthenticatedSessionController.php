<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman Login.
     * (Catatan: Jika Anda memanggil view ini langsung dari routes/auth.php, 
     * fungsi ini bisa diabaikan, tapi sebaiknya tetap ada untuk standar Laravel).
     */
    public function create(): View
    {
        return view('pages.customer.auth.login');
    }

    /**
     * Menangani proses autentikasi (Login) yang dikirim dari form.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Proses validasi email dan password menggunakan LoginRequest
        $request->authenticate();

        // 2. Regenerasi session untuk mencegah serangan 'session fixation'
        $request->session()->regenerate();

        // 3. LOGIKA GERBANG VERIFIKASI:
        // Cek apakah user wajib verifikasi email DAN apakah emailnya belum diverifikasi
        if ($request->user() instanceof MustVerifyEmail && ! $request->user()->hasVerifiedEmail()) {
            
            // Jika belum verifikasi, arahkan paksa ke halaman peringatan cek email
            return redirect()->route('verification.notice');
        }

        // 4. Jika sukses dan sudah verifikasi, arahkan ke halaman yang sebelumnya 
        // ingin diakses user, atau default ke halaman home (halaman utama katalog)
        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Menghancurkan session autentikasi (Logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 1. Logout user dari sistem (guard 'web')
        Auth::guard('web')->logout();

        // 2. Hancurkan data session yang tersimpan
        $request->session()->invalidate();

        // 3. Buat ulang token CSRF untuk keamanan form berikutnya
        $request->session()->regenerateToken();

        // 4. Arahkan kembali ke halaman utama setelah berhasil keluar
        return redirect('/');
    }
}
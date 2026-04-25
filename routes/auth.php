<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\NewPasswordController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// 1. RUTE GUEST (HANYA BISA DIAKSES JIKA PENGUNJUNG BELUM LOGIN)
// =========================================================================
Route::middleware('guest')->group(function () {
    
    // --- LOGIN ---
    Route::get('login', function () {
        return view('pages.customer.auth.login');
    })->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // --- REGISTER ---
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // --- LUPA PASSWORD (Kirim Email via SMTP) ---
    Route::get('forgot_password', function () {
        return view('pages.customer.auth.forgot_password');
    })->name('forgot_password'); 
    Route::post('forgot-password', [PasswordResetController::class, 'store'])->name('password.email');

    // --- RESET PASSWORD (Form Password Baru dari Link Email) ---
    Route::get('reset-password/{token}', function ($token) {
        return view('pages.customer.auth.reset_password', ['token' => $token]);
    })->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
    
}); // <--- PENUTUP GROUP GUEST (Sangat Penting, jangan dihapus!)


// =========================================================================
// 2. RUTE AUTH (HANYA BISA DIAKSES JIKA PENGUNJUNG SUDAH LOGIN)
// =========================================================================
Route::middleware('auth')->group(function () {
    
    // --- VERIFIKASI EMAIL (SMTP) ---
    // A. Menampilkan halaman peringatan "Tolong verifikasi email Anda"
    Route::get('/verify-email', function () {
        return view('pages.customer.auth.verify_email');
    })->name('verification.notice');

    // B. Memproses saat user mengeklik link dari kotak masuk email mereka
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        // Redirect ke halaman home jika verifikasi berhasil
        return redirect()->route('home')->with('status', 'Email berhasil diverifikasi!');
    })->middleware(['signed'])->name('verification.verify');

    // C. Tombol untuk mengirim ulang (Resend) email verifikasi
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Link verifikasi baru telah dikirim!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    // --- LOGOUT ---
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
        
}); // <--- PENUTUP GROUP AUTH
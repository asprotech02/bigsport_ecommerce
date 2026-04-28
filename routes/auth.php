<?php

// Import Controller dari folder Customer/Auth
use App\Http\Controllers\Customer\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Customer\Auth\RegisteredUserController;
use App\Http\Controllers\Customer\Auth\PasswordResetController;
use App\Http\Controllers\Customer\Auth\NewPasswordController;
use App\Http\Controllers\Customer\Auth\GoogleController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// =========================================================================
// 1. RUTE GUEST (Hanya akses jika BELUM LOGIN)
// =========================================================================
Route::middleware('guest')->group(function () {
    
    // --- LOGIN ---
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // --- REGISTER ---
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // --- LOGIN & REGISTER WITH GOOGLE ---
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

    // --- LUPA PASSWORD (Form Input Email) ---
    Route::get('forgot_password', function () {
        return view('pages.customer.auth.forgot_password'); // Sesuaikan nama file (-)
    })->name('password.request'); 
    
    Route::post('forgot_password', [PasswordResetController::class, 'store'])->name('password.email');

    // --- RESET PASSWORD (Link dari Email) ---
    Route::get('reset_password/{token}', function ($token) {
        return view('pages.customer.auth.reset_password', ['token' => $token]); // Sesuaikan nama file (-)
    })->name('password.reset');

    Route::post('reset_password', [NewPasswordController::class, 'store'])->name('password.store');
    
});

// =========================================================================
// 2. RUTE AUTH (Hanya akses jika SUDAH LOGIN)
// =========================================================================
Route::middleware('auth')->group(function () {
    
    // --- VERIFIKASI EMAIL ---
    Route::get('/verify_email', function () {
        return view('pages.customer.auth.verify_email'); // Sesuaikan nama file (-)
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('home')->with('status', 'Email berhasil diverifikasi!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Link verifikasi baru telah dikirim!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    // --- LOGOUT ---
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
        
});
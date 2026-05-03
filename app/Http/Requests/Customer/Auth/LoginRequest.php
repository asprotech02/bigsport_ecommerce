<?php

namespace App\Http\Requests\Customer\Auth;

use App\Models\User; // TAMBAHKAN BARIS INI
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Mengarahkan error login ke kantong khusus bernama 'login'.
     */
    protected $errorBag = 'login';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi: Email wajib valid, Password minimal 8 karakter.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    /**
     * Pesan Error kustom dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'Email wajib diisi',
            'email.email'       => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min'      => 'Password harus memiliki minimal 8 karakter',
        ];
    }

    /**
     * Logika Autentikasi.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Cari user berdasarkan email
        $user = User::where('email', $this->email)->first();

        // 2. Jika email tidak ditemukan di database
        if (!$user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Email yang Anda masukkan tidak terdaftar',
            ])->errorBag('login');
        }

        // 3. Jika email ada, coba login (cek password)
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'password' => 'Password yang Anda masukkan salah',
            ])->errorBag('login');
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Batasan percobaan login (Anti Brute-force).
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => 'Terlalu banyak percobaan login silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit',
        ])->errorBag('login');
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}
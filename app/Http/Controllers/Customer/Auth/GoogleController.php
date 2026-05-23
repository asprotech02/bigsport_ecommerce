<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller; // Namespace disesuaikan
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if ($user) {
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
                
                Auth::login($user);

                if (!$user->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice');
                }

                if (in_array($user->role, ['admin', 'sales', 'manager'])) {
                    return redirect()->route('admin.dashboard');
                }

                return redirect()->route('home');
            } else {
                $newUser = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(Str::random(16)), 
                ]);

                event(new Registered($newUser));
                Auth::login($newUser);

                return redirect()->route('verification.notice');
            }

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login menggunakan Google');
        }
    }
}
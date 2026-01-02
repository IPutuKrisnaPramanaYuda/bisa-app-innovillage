<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialiteController extends Controller
{
    // 1. Redirect ke Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Callback dari Google
    public function callback()
    {
        try {
            // Ambil user dari Google
            $googleUser = Socialite::driver('google')->user();

            // Cari user di database berdasarkan email
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Jika user belum ada, buat baru
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)), // Password acak
                    'email_verified_at' => now(), // Auto verify
                ]);
            } else {
                // Jika user sudah ada, update ID Google-nya
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            // Login-kan user
            Auth::login($user);

            // Redirect ke Dashboard AI
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login Gagal: ' . $e->getMessage());
        }
    }
}
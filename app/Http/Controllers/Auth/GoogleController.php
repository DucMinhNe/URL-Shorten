<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $g = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Đăng nhập Google thất bại, vui lòng thử lại.']);
        }

        $email = $g->getEmail();
        if (! $email) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Tài khoản Google không có email.']);
        }

        $existing = User::where('email', $email)->first();

        if ($existing) {
            // Link Google to existing account — DO NOT overwrite password or name.
            $existing->forceFill([
                'google_id' => $g->getId(),
                'avatar' => $existing->avatar ?: $g->getAvatar(),
                'email_verified_at' => $existing->email_verified_at ?? now(),
            ])->save();
            $user = $existing;
        } else {
            $user = User::create([
                'name' => $g->getName() ?: $g->getNickname() ?: 'User',
                'email' => $email,
                'google_id' => $g->getId(),
                'avatar' => $g->getAvatar(),
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(32)),
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}

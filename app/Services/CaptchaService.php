<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CaptchaService
{
    public function verify(?string $token, string $ip): bool
    {
        if (! $token) return false;

        try {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ]);
            return (bool) $response->json('success', false);
        } catch (\Throwable $e) {
            Log::warning('Turnstile verify failed: '.$e->getMessage());
            return false;
        }
    }
}

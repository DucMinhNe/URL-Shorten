<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CaptchaService
{
    public function verify(?string $token, string $ip): bool
    {
        $secret = config('services.turnstile.secret_key');

        // Demo/dev mode: no real Turnstile configured → accept any non-empty token
        // (frontend uses custom "not a robot" checkbox)
        if (empty($secret)) {
            return ! empty($token);
        }

        if (! $token) return false;

        try {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secret,
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

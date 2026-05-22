<?php

use App\Services\CaptchaService;
use Illuminate\Support\Facades\Http;

it('returns true when Turnstile verifies success', function () {
    Http::fake([
        'challenges.cloudflare.com/*' => Http::response(['success' => true], 200),
    ]);
    $svc = new CaptchaService();
    expect($svc->verify('any-token', '127.0.0.1'))->toBeTrue();
});

it('returns false when Turnstile fails', function () {
    Http::fake([
        'challenges.cloudflare.com/*' => Http::response(['success' => false], 200),
    ]);
    $svc = new CaptchaService();
    expect($svc->verify('bad', '127.0.0.1'))->toBeFalse();
});

it('returns false when http throws', function () {
    Http::fake(fn() => throw new \Exception('network down'));
    $svc = new CaptchaService();
    expect($svc->verify('x', '127.0.0.1'))->toBeFalse();
});

it('returns false when token is null', function () {
    $svc = new CaptchaService();
    expect($svc->verify(null, '127.0.0.1'))->toBeFalse();
});

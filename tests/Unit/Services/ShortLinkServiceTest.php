<?php

use App\Models\BlacklistDomain;
use App\Models\ShortLink;
use App\Models\User;
use App\Services\ShortLinkService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('generates unique 6-char slug', function () {
    $svc = new ShortLinkService();
    $slug = $svc->generateUniqueSlug();
    expect($slug)->toMatch('/^[A-Za-z0-9]{6}$/');
});

it('avoids slug collisions', function () {
    ShortLink::factory()->create(['slug' => 'ABC123']);
    $svc = new ShortLinkService();
    for ($i = 0; $i < 5; $i++) {
        $slug = $svc->generateUniqueSlug();
        expect($slug)->not->toBe('ABC123');
    }
});

it('blocks blacklisted domain', function () {
    BlacklistDomain::create(['domain' => 'spam.test']);
    $svc = new ShortLinkService();
    expect(fn() => $svc->create(null, 'https://spam.test/foo'))
        ->toThrow(\RuntimeException::class, 'Domain is blacklisted');
});

it('rejects duplicate custom alias', function () {
    ShortLink::factory()->create(['slug' => 'mycoolalias']);
    $svc = new ShortLinkService();
    expect(fn() => $svc->create(null, 'https://example.com', 'mycoolalias'))
        ->toThrow(\RuntimeException::class, 'Alias already taken');
});

it('hashes password when provided', function () {
    $user = User::factory()->create();
    $svc = new ShortLinkService();
    $link = $svc->create($user->id, 'https://example.com', null, 'secret');
    expect($link->password)->not->toBe('secret');
    expect(\Hash::check('secret', $link->password))->toBeTrue();
});

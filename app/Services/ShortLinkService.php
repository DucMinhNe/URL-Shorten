<?php

namespace App\Services;

use App\Models\BlacklistDomain;
use App\Models\ShortLink;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ShortLinkService
{
    public function generateUniqueSlug(int $length = 6): string
    {
        do {
            $slug = Str::random($length);
        } while (ShortLink::where('slug', $slug)->exists());

        return $slug;
    }

    public function create(?int $userId, string $originalUrl, ?string $customAlias = null, ?string $password = null): ShortLink
    {
        $host = parse_url($originalUrl, PHP_URL_HOST);
        if ($host && BlacklistDomain::where('domain', $host)->exists()) {
            throw new \RuntimeException('Domain is blacklisted');
        }

        if ($customAlias && ShortLink::where('slug', $customAlias)->exists()) {
            throw new \RuntimeException('Alias already taken');
        }

        $slug = $customAlias ?: $this->generateUniqueSlug();

        return ShortLink::create([
            'user_id' => $userId,
            'slug' => $slug,
            'original_url' => $originalUrl,
            'password' => $password ? Hash::make($password) : null,
            'status' => 'active',
        ]);
    }
}

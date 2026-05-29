<?php

namespace App\Services;

use App\Models\BlacklistDomain;
use App\Models\ShortLink;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class ShortLinkService
{
    // Slugs that collide with named routes or admin paths.
    public const RESERVED_SLUGS = [
        'admin','api','auth','dashboard','docs','email','filament','forgot-password',
        'help','home','links','livewire','locale','login','logout','password','payout',
        'profile','register','reset-password','confirm-password','shorten','storage',
        'support','two-factor','up','vendor','verification','verify-email',
    ];

    public function isReservedSlug(string $slug): bool
    {
        return in_array(strtolower($slug), self::RESERVED_SLUGS, true);
    }

    public function assertOriginalUrlAllowed(string $url): void
    {
        $host = parse_url($url, PHP_URL_HOST);
        if ($host && BlacklistDomain::where('domain', $host)->exists()) {
            throw new RuntimeException('Domain bị chặn (blacklisted).');
        }
    }

    public function generateUniqueSlug(int $length = 6): string
    {
        for ($i = 0; $i < 8; $i++) {
            $slug = Str::random($length);
            if (! $this->isReservedSlug($slug) && ! ShortLink::where('slug', $slug)->exists()) {
                return $slug;
            }
            $length++; // grow on collision
        }
        throw new RuntimeException('Không tạo được slug duy nhất.');
    }

    public function create(?int $userId, string $originalUrl, ?string $customAlias = null, ?string $password = null): ShortLink
    {
        $this->assertOriginalUrlAllowed($originalUrl);

        if ($customAlias) {
            if ($this->isReservedSlug($customAlias)) {
                throw new RuntimeException('Alias này đã được hệ thống giữ chỗ, vui lòng chọn alias khác.');
            }
            if (ShortLink::where('slug', $customAlias)->exists()) {
                throw new RuntimeException('Alias đã có người dùng.');
            }
        }

        $slug = $customAlias ?: $this->generateUniqueSlug();

        try {
            return ShortLink::create([
                'user_id' => $userId,
                'slug' => $slug,
                'original_url' => $originalUrl,
                'password' => $password ? Hash::make($password) : null,
                'status' => 'active',
            ]);
        } catch (QueryException $e) {
            // Race on slug uniqueness — surface a friendly error.
            throw new RuntimeException('Có lỗi xảy ra, vui lòng thử lại.');
        }
    }
}

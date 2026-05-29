<?php

namespace App\Support;

/**
 * Parser user-agent gọn nhẹ (regex), không cần package ngoài.
 * Đủ dùng cho thống kê device / browser / OS trên trang analytics.
 */
class UserAgentParser
{
    public static function deviceType(?string $ua): string
    {
        $ua = (string) $ua;
        if ($ua === '') {
            return 'Khác';
        }
        if (preg_match('/iPad|Tablet|PlayBook|Silk|(?=.*\bAndroid\b)(?=.*\bMobile\b)?(?=.*\bTablet\b)/i', $ua)) {
            return 'Tablet';
        }
        if (preg_match('/Mobi|iPhone|iPod|Android.*Mobile|Windows Phone|BlackBerry|IEMobile|Opera Mini/i', $ua)) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    public static function browser(?string $ua): string
    {
        $ua = (string) $ua;

        return match (true) {
            (bool) preg_match('/Edg|Edge/i', $ua) => 'Edge',
            (bool) preg_match('/OPR|Opera/i', $ua) => 'Opera',
            (bool) preg_match('/SamsungBrowser/i', $ua) => 'Samsung Internet',
            (bool) preg_match('/Firefox|FxiOS/i', $ua) => 'Firefox',
            (bool) preg_match('/Chrome|CriOS/i', $ua) => 'Chrome',
            (bool) preg_match('/Safari/i', $ua) && preg_match('/Version/i', $ua) => 'Safari',
            (bool) preg_match('/MSIE|Trident/i', $ua) => 'Internet Explorer',
            (bool) preg_match('/bot|crawl|spider|slurp/i', $ua) => 'Bot',
            default => 'Khác',
        };
    }

    public static function os(?string $ua): string
    {
        $ua = (string) $ua;

        return match (true) {
            (bool) preg_match('/Windows/i', $ua) => 'Windows',
            (bool) preg_match('/iPhone|iPad|iPod/i', $ua) => 'iOS',
            (bool) preg_match('/Android/i', $ua) => 'Android',
            (bool) preg_match('/Mac OS X|Macintosh/i', $ua) => 'macOS',
            (bool) preg_match('/Linux/i', $ua) => 'Linux',
            default => 'Khác',
        };
    }

    /** Host của referer; null/empty → "Trực tiếp". */
    public static function refererSource(?string $referer): string
    {
        $referer = trim((string) $referer);
        if ($referer === '') {
            return 'Trực tiếp';
        }
        $host = parse_url($referer, PHP_URL_HOST);
        if (! $host) {
            return 'Trực tiếp';
        }

        return preg_replace('/^www\./i', '', strtolower($host));
    }
}

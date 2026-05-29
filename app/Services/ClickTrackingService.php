<?php

namespace App\Services;

use App\Models\Click;
use App\Models\IpViewLog;
use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ClickTrackingService
{
    public function __construct(
        private SettingService $settings,
        private WalletService $wallet,
    ) {}

    public function record(ShortLink $link, string $ip, ?string $userAgent, bool $captchaPass, ?int $viewerUserId, ?string $referer = null): Click
    {
        // Serialize per (link, ip) so concurrent requests from same IP can't bypass dedup.
        $lock = Cache::lock("click:track:{$link->id}:{$ip}", 5);

        return $lock->block(3, function () use ($link, $ip, $userAgent, $captchaPass, $viewerUserId, $referer) {
            $hours = (int) $this->settings->get('ip_dedup_hours', 24);
            $alreadyViewed = IpViewLog::where('short_link_id', $link->id)
                ->where('ip_address', $ip)
                ->where('viewed_at', '>=', now()->subHours($hours))
                ->exists();

            $selfClick = $viewerUserId && $link->user_id && $viewerUserId === $link->user_id;
            $isValid = $captchaPass && ! $alreadyViewed && ! $selfClick;

            $earnings = 0;
            if ($isValid) {
                $rate = (int) $this->settings->get('rate_per_1000_views', 5000);
                $earnings = intdiv($rate, 1000);
            }

            return DB::transaction(function () use ($link, $ip, $userAgent, $referer, $isValid, $earnings) {
                $click = Click::create([
                    'short_link_id' => $link->id,
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                    'referer' => $referer,
                    'is_valid' => $isValid,
                    'earnings' => $earnings,
                    'created_at' => now(),
                ]);

                $link->increment('total_clicks');

                if ($isValid) {
                    IpViewLog::create([
                        'short_link_id' => $link->id,
                        'ip_address' => $ip,
                        'viewed_at' => now(),
                    ]);
                    $link->increment('valid_views');

                    if ($link->user_id && $earnings > 0) {
                        $link->increment('total_earned', $earnings);
                        $this->wallet->credit(
                            User::find($link->user_id),
                            $earnings,
                            'click',
                            $click->id,
                            "Click /{$link->slug}"
                        );
                    }
                }

                return $click;
            });
        });
    }
}

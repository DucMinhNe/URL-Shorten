<?php

namespace Database\Seeders;

use App\Models\AdCampaign;
use App\Models\AdImpression;
use App\Models\Click;
use App\Models\IpViewLog;
use App\Models\ShortLink;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClickSeeder extends Seeder
{
    public function run(): void
    {
        $links = ShortLink::where('status', 'active')->get();
        if ($links->isEmpty()) {
            return;
        }

        $rate = 250;  // VND per click — bumped for demo so user balances look meaningful
        $hotLinks = $links->random((int) ($links->count() * 0.3));
        $coldLinks = $links->diff($hotLinks);

        $clicks = [];
        $impressions = [];
        $ipLogs = [];

        $now = now();
        $ads = AdCampaign::where('status', 'active')->get()->groupBy('placement');

        for ($d = 89; $d >= 0; $d--) {
            $date = $now->copy()->subDays($d);
            $multiplier = in_array($date->dayOfWeek, [0, 6]) ? 1.5 : 1.0;
            $daily = (int) (rand(100, 250) * $multiplier);

            for ($c = 0; $c < $daily; $c++) {
                $link = (rand(0, 10) < 6) ? $hotLinks->random() : $coldLinks->random();
                $ip = fake()->ipv4();
                $isValid = fake()->boolean(70);
                $earnings = $isValid ? $rate : 0;
                $ts = $date->copy()->addMinutes(rand(0, 1439));
                $token = (string) Str::uuid();

                $clicks[] = [
                    'short_link_id' => $link->id,
                    'ip_address' => $ip,
                    'user_agent' => fake()->userAgent(),
                    'referer' => null,
                    'is_valid' => $isValid,
                    'earnings' => $earnings,
                    'created_at' => $ts,
                ];

                if ($isValid) {
                    $ipLogs[] = ['short_link_id' => $link->id, 'ip_address' => $ip, 'viewed_at' => $ts];
                }

                foreach (['top', 'side', 'bottom'] as $p) {
                    if (isset($ads[$p]) && $ads[$p]->count() > 0) {
                        $ad = $ads[$p]->random();
                        $impressions[] = [
                            'ad_campaign_id' => $ad->id,
                            'short_link_id' => $link->id,
                            'impression_token' => $token,
                            'ip_address' => $ip,
                            'was_clicked' => fake()->boolean(5),
                            'created_at' => $ts,
                        ];
                    }
                }

                if (count($clicks) >= 500) {
                    Click::insert($clicks);
                    $clicks = [];
                    if ($ipLogs) {
                        IpViewLog::insert($ipLogs);
                        $ipLogs = [];
                    }
                    if ($impressions) {
                        AdImpression::insert($impressions);
                        $impressions = [];
                    }
                }
            }
        }
        if ($clicks) {
            Click::insert($clicks);
        }
        if ($ipLogs) {
            IpViewLog::insert($ipLogs);
        }
        if ($impressions) {
            AdImpression::insert($impressions);
        }

        // Recompute aggregates on short_links
        DB::statement("
            UPDATE short_links sl SET
              total_clicks = (SELECT COUNT(*) FROM clicks c WHERE c.short_link_id = sl.id),
              valid_views = (SELECT COUNT(*) FROM clicks c WHERE c.short_link_id = sl.id AND is_valid=1),
              total_earned = (SELECT COALESCE(SUM(earnings),0) FROM clicks c WHERE c.short_link_id = sl.id)
        ");

        // Recompute user.total_earned + balance
        DB::statement('
            UPDATE users u SET
              total_earned = COALESCE((SELECT SUM(sl.total_earned) FROM short_links sl WHERE sl.user_id = u.id), 0)
            WHERE u.is_admin = 0
        ');
        DB::statement('UPDATE users SET balance = total_earned WHERE is_admin = 0');

        // Update ad_campaigns counters
        DB::statement('
            UPDATE ad_campaigns ac SET
              impressions = (SELECT COUNT(*) FROM ad_impressions ai WHERE ai.ad_campaign_id = ac.id),
              clicks_count = (SELECT COUNT(*) FROM ad_impressions ai WHERE ai.ad_campaign_id = ac.id AND ai.was_clicked = 1)
        ');
    }
}

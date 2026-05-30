<?php

namespace Database\Seeders;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProFeaturesSeeder extends Seeder
{
    /** Seed dữ liệu cho referral / premium / API để demo phong phú. */
    public function run(): void
    {
        // Mã giới thiệu cho mọi user (chạy nhẹ trên tập demo).
        User::whereNull('referral_code')->chunkById(200, function ($users) {
            foreach ($users as $u) {
                $u->forceFill(['referral_code' => strtoupper(Str::random(7))])->saveQuietly();
            }
        });

        $demo = User::where('email', 'demo@demo.com')->first();
        if ($demo) {
            // 6 người được demo mời (chọn user đang kiếm tiền, chưa có người giới thiệu).
            $picks = User::where('id', '!=', $demo->id)
                ->whereNull('referred_by')
                ->where('total_earned', '>', 0)
                ->inRandomOrder()->limit(6)->get();

            foreach ($picks as $p) {
                $p->forceFill(['referred_by' => $demo->id])->saveQuietly();
            }
            $demo->forceFill(['referral_earned' => (int) ($picks->sum('total_earned') * 0.1)])->saveQuietly();

            // API token mẫu.
            if (! $demo->apiTokens()->exists()) {
                $demo->apiTokens()->create(['name' => 'Demo bot', 'token' => ApiToken::hash(ApiToken::generate())]);
            }
        }

        // Vài user Premium để minh hoạ huy hiệu PRO + bypass quảng cáo.
        User::where('total_earned', '>', 0)->inRandomOrder()->limit(8)->get()
            ->each(fn ($u) => $u->forceFill([
                'is_premium' => true,
                'premium_until' => now()->addMonths(rand(1, 12)),
            ])->saveQuietly());
    }
}

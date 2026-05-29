<?php

namespace Database\Seeders;

use App\Models\PromoCode;
use App\Models\PromoCodeRedemption;
use App\Models\User;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('is_admin', true)->first();

        $codes = [
            ['code' => 'WELCOME50K',   'name' => 'Welcome bonus 50k',    'type' => 'welcome_bonus', 'value' => 50000, 'max_redemptions' => 1000, 'max_per_user' => 1, 'valid_until' => now()->addDays(30)],
            ['code' => 'WELCOME30K',   'name' => 'Welcome bonus 30k cũ', 'type' => 'welcome_bonus', 'value' => 30000, 'max_redemptions' => 500,  'max_per_user' => 1, 'valid_from' => now()->subDays(60), 'valid_until' => now()->subDays(15), 'is_active' => false],
            ['code' => 'SUMMER2026',   'name' => 'Bonus hè 2026',        'type' => 'bonus_credit',  'value' => 30000, 'max_redemptions' => 500,  'max_per_user' => 1, 'valid_until' => now()->addDays(40)],
            ['code' => 'BOOST15',      'name' => 'Rate boost 1.5x 7 ngày','type' => 'rate_boost',    'value' => 150,   'value_unit' => 'percent', 'max_redemptions' => null, 'max_per_user' => 1],
            ['code' => 'BOOST20',      'name' => 'Rate boost 2x cuối tuần','type' => 'rate_boost',   'value' => 200,   'value_unit' => 'percent', 'max_redemptions' => 200, 'max_per_user' => 1, 'valid_until' => now()->addDays(7)],
            ['code' => 'NOFEEPAYOUT',  'name' => 'Miễn phí rút PayPal',  'type' => 'payout_fee_waiver', 'value' => 100, 'value_unit' => 'percent', 'max_per_user' => 3],
            ['code' => 'CREATOR100K',  'name' => 'Bonus creator >10 link','type' => 'bonus_credit',  'value' => 100000, 'max_redemptions' => 100, 'min_balance_required' => 50000],
            ['code' => 'TIKTOK20K',    'name' => 'TikTok partner bonus', 'type' => 'bonus_credit',  'value' => 20000, 'max_redemptions' => 1000],
            ['code' => 'FACEBOOK15K',  'name' => 'Facebook share bonus', 'type' => 'bonus_credit',  'value' => 15000, 'max_redemptions' => 2000],
            ['code' => 'YOUTUBER50K',  'name' => 'YouTuber verified',    'type' => 'bonus_credit',  'value' => 50000, 'max_redemptions' => 200],
            ['code' => 'STUDENT10K',   'name' => 'Sinh viên bonus',      'type' => 'bonus_credit',  'value' => 10000, 'max_redemptions' => 500],
            ['code' => 'REFER100K',    'name' => 'Refer 1 user thưởng 100k', 'type' => 'bonus_credit', 'value' => 100000, 'max_redemptions' => null],
            ['code' => 'NEWYEAR2026',  'name' => 'Lì xì năm mới',        'type' => 'bonus_credit',  'value' => 26000, 'is_active' => false, 'valid_from' => now()->subDays(120), 'valid_until' => now()->subDays(90)],
            ['code' => 'BLACKFRIDAY',  'name' => 'BF 2x rate 1 tuần',    'type' => 'rate_boost',    'value' => 200,   'value_unit' => 'percent', 'is_active' => false, 'valid_until' => now()->subDays(40)],
            ['code' => 'VIP200K',      'name' => 'VIP bonus 200k',       'type' => 'bonus_credit',  'value' => 200000, 'max_redemptions' => 50, 'min_balance_required' => 500000],
            ['code' => 'XMAS2025',     'name' => 'Giáng sinh 2025',      'type' => 'bonus_credit',  'value' => 25000, 'is_active' => false, 'valid_until' => now()->subDays(150)],
            ['code' => 'BIRTHDAY',     'name' => 'Sinh nhật user',       'type' => 'bonus_credit',  'value' => 50000, 'max_per_user' => 1],
            ['code' => 'FREEPAYPAL',   'name' => 'Miễn phí PayPal lần 1','type' => 'payout_fee_waiver', 'value' => 100, 'value_unit' => 'percent', 'max_per_user' => 1],
            ['code' => 'WEEKEND2X',    'name' => 'Cuối tuần x2',          'type' => 'rate_boost',    'value' => 200,   'value_unit' => 'percent'],
            ['code' => 'CYBERMON',     'name' => 'Cyber Monday tháng 11','type' => 'bonus_credit',  'value' => 88000, 'is_active' => false],
        ];

        $created = [];
        foreach ($codes as $c) {
            $created[] = PromoCode::create(array_merge([
                'description' => 'Mã '.$c['name'],
                'value_unit' => 'vnd',
                'is_active' => true,
                'created_by' => $admin->id,
                'valid_from' => null,
                'valid_until' => null,
                'max_redemptions' => null,
                'max_per_user' => 1,
                'min_balance_required' => 0,
            ], $c));
        }

        // Seed redemptions
        $userIds = User::where('is_admin', false)->pluck('id')->toArray();
        $bulk = [];
        foreach ($created as $promo) {
            $redeemCount = fake()->numberBetween(5, 80);
            $chosenUsers = collect($userIds)->shuffle()->take($redeemCount);
            foreach ($chosenUsers as $uid) {
                $bulk[] = [
                    'promo_code_id' => $promo->id,
                    'user_id' => $uid,
                    'value_applied' => $promo->value,
                    'ip_address' => fake()->ipv4(),
                    'redeemed_at' => fake()->dateTimeBetween('-60 days', 'now'),
                ];
            }
            $promo->update(['redeemed_count' => $redeemCount]);
        }

        // Bulk insert in chunks
        foreach (array_chunk($bulk, 500) as $chunk) {
            \DB::table('promo_code_redemptions')->insert($chunk);
        }
    }
}

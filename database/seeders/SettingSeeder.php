<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key' => 'rate_per_1000_views', 'value' => '5000', 'type' => 'integer', 'description' => 'VND per 1000 valid views'],
            ['key' => 'min_payout_vnd', 'value' => '100000', 'type' => 'integer', 'description' => 'Min payout amount VND'],
            ['key' => 'min_payout_usd_paypal', 'value' => '4', 'type' => 'integer', 'description' => 'Min PayPal payout USD'],
            ['key' => 'interstitial_seconds', 'value' => '5', 'type' => 'integer', 'description' => 'Countdown seconds'],
            ['key' => 'ip_dedup_hours', 'value' => '24', 'type' => 'integer', 'description' => 'IP dedup window'],
            ['key' => 'paypal_to_vnd_rate', 'value' => '25000', 'type' => 'integer', 'description' => '1 USD = N VND'],
        ];
        foreach ($defaults as $d) {
            Setting::updateOrCreate(['key' => $d['key']], $d + ['updated_at' => now()]);
        }
    }
}

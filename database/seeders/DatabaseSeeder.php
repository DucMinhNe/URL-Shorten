<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            BlacklistDomainSeeder::class,
            UserSeeder::class,
            AdCampaignSeeder::class,
            ShortLinkSeeder::class,
            ClickSeeder::class,
            PayoutRequestSeeder::class,
            WalletTransactionSeeder::class,
        ]);
    }
}

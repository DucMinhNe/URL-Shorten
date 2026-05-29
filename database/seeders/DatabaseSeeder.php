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
            RoleSeeder::class,
            AdCampaignSeeder::class,
            ShortLinkSeeder::class,
            TagSeeder::class,
            ClickSeeder::class,
            PayoutRequestSeeder::class,
            WalletTransactionSeeder::class,
            ReportedLinkSeeder::class,
            SupportTicketSeeder::class,
            AnnouncementSeeder::class,
            PromoCodeSeeder::class,
            EmailTemplateSeeder::class,
            FaqSeeder::class,
            AuditLogSeeder::class,
        ]);
    }
}

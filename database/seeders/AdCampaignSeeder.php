<?php

namespace Database\Seeders;

use App\Models\AdCampaign;
use Illuminate\Database\Seeder;

class AdCampaignSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['top', 'side', 'bottom'] as $placement) {
            AdCampaign::factory()->count(5)->create([
                'placement' => $placement,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\AdCampaign;
use Illuminate\Database\Seeder;

class AdBannerSeeder extends Seeder
{
    /** Banner quảng cáo thật (creative hoàn chỉnh) — hiển thị nguyên tấm trên trang chờ. */
    public function run(): void
    {
        // Tạm dừng các ad stock cũ để chỉ phục vụ banner thật.
        AdCampaign::query()->update(['status' => 'paused']);

        $banners = [
            // placement, name, content, target
            ['top', 'Bcons City Life — Khu đô thị',
                ['image' => '/images/ads/bcons.png', 'brand' => 'Bcons City Life'],
                'https://bcons.com.vn'],
            ['top', 'Ford Ranger Raptor — TVC',
                ['video' => '/images/ads/raptor.mp4', 'brand' => 'Ford Việt Nam'],
                'https://www.ford.com.vn'],
            ['side', 'FPT Shop — PC E-Power',
                ['image' => '/images/ads/fpt-shop.png', 'brand' => 'FPT Shop'],
                'https://fptshop.com.vn'],
            ['bottom', 'British Council — Tiếng Anh Hè 2026',
                ['image' => '/images/ads/british-council.png', 'brand' => 'British Council'],
                'https://www.britishcouncil.vn'],
        ];

        foreach ($banners as [$placement, $name, $content, $target]) {
            AdCampaign::updateOrCreate(
                ['name' => $name],
                [
                    'placement' => $placement,
                    'type' => 'banner_image',
                    'content' => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'target_url' => $target,
                    'weight' => 50,
                    'status' => 'active',
                    'start_at' => null,
                    'end_at' => null,
                ],
            );
        }
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdCampaignFactory extends Factory
{
    /**
     * Realistic Vietnamese e-commerce / brand promotional ad data.
     * Uses placehold.co for branded color banners with promotional text in real brand colors.
     */
    private const ADS = [
        // ─── E-commerce ─────────────────────────
        ['name' => 'Shopee 9.9 Mega Sale',     'bg' => 'EE4D2D', 'fg' => 'FFFFFF', 'text' => 'SHOPEE+9.9+SALE\nGiam+den+90%25',           'target' => 'https://shopee.vn'],
        ['name' => 'Lazada Flash Sale',        'bg' => '0F156D', 'fg' => 'FFCC00', 'text' => 'LAZADA+FLASH+SALE\nVoucher+500K',           'target' => 'https://lazada.vn'],
        ['name' => 'Tiki Trading Deal',        'bg' => '189EFF', 'fg' => 'FFFFFF', 'text' => 'TIKI+TRADING\nFree+ship+toan+quoc',         'target' => 'https://tiki.vn'],
        ['name' => 'Sendo Voucher Khung',      'bg' => 'D0021B', 'fg' => 'FFFFFF', 'text' => 'SENDO\nHoan+tien+50%25+don+dau',           'target' => 'https://sendo.vn'],

        // ─── Food delivery ──────────────────────
        ['name' => 'GrabFood Uu Dai',          'bg' => '00B14F', 'fg' => 'FFFFFF', 'text' => 'GRABFOOD\nMa+SIEUDEAL+giam+50K',           'target' => 'https://food.grab.com'],
        ['name' => 'ShopeeFood Voucher',       'bg' => 'EE4D2D', 'fg' => 'FFEC00', 'text' => 'SHOPEEFOOD\nDat+ngay+nhan+30K',            'target' => 'https://shopeefood.vn'],
        ['name' => 'BeFood Khuyen Mai',        'bg' => 'FFCD00', 'fg' => '000000', 'text' => 'BE\n0d+phi+ship+don+dau',                  'target' => 'https://be.com.vn'],

        // ─── Banking / Fintech ──────────────────
        ['name' => 'Techcombank Cashback',     'bg' => 'EE0033', 'fg' => 'FFFFFF', 'text' => 'TECHCOMBANK\nHoan+8%25+chi+tieu',          'target' => 'https://techcombank.com'],
        ['name' => 'VPBank NEO Mo The',        'bg' => '00B74F', 'fg' => 'FFFFFF', 'text' => 'VPBANK+NEO\nMo+the+nhan+500K',             'target' => 'https://vpbank.com.vn'],
        ['name' => 'TPBank Vay Online',        'bg' => '6A1B9A', 'fg' => 'FFFFFF', 'text' => 'TPBANK\nVay+online+lai+suat+0%25',         'target' => 'https://tpb.vn'],

        // ─── Telecom / Apps ─────────────────────
        ['name' => 'My Viettel App',           'bg' => 'CC0000', 'fg' => 'FFFFFF', 'text' => 'MY+VIETTEL\nDang+ky+goi+5G+uu+dai',        'target' => 'https://viettel.vn'],
        ['name' => 'TikTok Shop',              'bg' => '000000', 'fg' => 'FF0050', 'text' => 'TIKTOK+SHOP\nMua+sam+livestream',          'target' => 'https://tiktok.com'],
        ['name' => 'FPT Play VIP',             'bg' => 'F47A20', 'fg' => 'FFFFFF', 'text' => 'FPT+PLAY+VIP\nFree+1+thang',               'target' => 'https://fptplay.vn'],

        // ─── Education ──────────────────────────
        ['name' => 'Edumall Khoa Hoc',         'bg' => '1565C0', 'fg' => 'FFFFFF', 'text' => 'EDUMALL\nIELTS+giam+70%25',                'target' => 'https://edumall.vn'],
        ['name' => 'Funix Lap Trinh',          'bg' => 'F95738', 'fg' => 'FFFFFF', 'text' => 'FUNIX\nHoc+lap+trinh+tu+xa',               'target' => 'https://funix.edu.vn'],
    ];

    public function definition(): array
    {
        $ad = fake()->randomElement(self::ADS);
        $placement = fake()->randomElement(['top', 'side', 'bottom']);
        $size = match ($placement) {
            'top', 'bottom' => '1456x360',
            'side'          => '600x600',
        };

        $imageUrl = "https://placehold.co/{$size}/{$ad['bg']}/{$ad['fg']}/png?text={$ad['text']}&font=montserrat";

        return [
            'name'       => $ad['name'].' · '.strtoupper($placement),
            'placement'  => $placement,
            'type'       => 'banner_image',
            'content'    => $imageUrl,
            'target_url' => $ad['target'],
            'weight'     => fake()->numberBetween(1, 10),
            'status'     => 'active',
        ];
    }
}

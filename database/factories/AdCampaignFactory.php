<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdCampaignFactory extends Factory
{
    /**
     * Real Vietnamese promotional ads with real photography (Unsplash),
     * real brand colors, real Vietnamese marketing copy and target URLs.
     */
    private const ADS = [
        // ─── E-commerce ─────────────────────────
        [
            'name'     => 'Shopee 9.9 Mega Sale — Giảm đến 90%',
            'image'    => 'shopee-mega-sale.jpg',
            'headline' => '9.9 MEGA SALE',
            'sub'      => 'Giảm đến 90% · Freeship 0đ toàn quốc',
            'cta'      => 'Săn deal ngay',
            'brand'    => 'Shopee',
            'color'    => 'EE4D2D',
            'target'   => 'https://shopee.vn',
        ],
        [
            'name'     => 'Lazada Flash Sale — Voucher 500K',
            'image'    => 'lazada-flash.jpg',
            'headline' => 'FLASH SALE 12.12',
            'sub'      => 'Voucher 500K · Trả góp 0% lãi suất',
            'cta'      => 'Mua ngay',
            'brand'    => 'Lazada',
            'color'    => '0F156D',
            'target'   => 'https://lazada.vn',
        ],
        [
            'name'     => 'Tiki Sách — Mua 1 Tặng 1',
            'image'    => 'tiki-books.jpg',
            'headline' => 'TIKI BOOKS',
            'sub'      => 'Mua 1 tặng 1 · Best-seller giảm 50%',
            'cta'      => 'Đọc ngay',
            'brand'    => 'Tiki',
            'color'    => '189EFF',
            'target'   => 'https://tiki.vn',
        ],
        [
            'name'     => 'TikTok Shop — Livestream Sale',
            'image'    => 'tiktok-shop.jpg',
            'headline' => 'TIKTOK SHOP',
            'sub'      => 'Mua sắm trực tiếp · Giảm thêm 30%',
            'cta'      => 'Vào xem',
            'brand'    => 'TikTok Shop',
            'color'    => '000000',
            'target'   => 'https://www.tiktok.com',
        ],

        // ─── Fashion ────────────────────────────
        [
            'name'     => 'Fashion Bộ Sưu Tập Mới',
            'image'    => 'fashion-sale.jpg',
            'headline' => 'XU HƯỚNG 2026',
            'sub'      => 'BST mới · Giảm 40% toàn shop',
            'cta'      => 'Khám phá',
            'brand'    => 'Routine Fashion',
            'color'    => 'B91C1C',
            'target'   => 'https://routine.vn',
        ],

        // ─── Electronics ────────────────────────
        [
            'name'     => 'CellphoneS — Laptop Gaming',
            'image'    => 'electronics.jpg',
            'headline' => 'LAPTOP GAMING',
            'sub'      => 'Trả góp 0% · Tặng chuột cao cấp',
            'cta'      => 'Xem chi tiết',
            'brand'    => 'CellphoneS',
            'color'    => 'EA1B1B',
            'target'   => 'https://cellphones.com.vn',
        ],

        // ─── Food delivery ──────────────────────
        [
            'name'     => 'GrabFood — Voucher 50K',
            'image'    => 'food-delivery.jpg',
            'headline' => 'GRABFOOD UPSALE',
            'sub'      => 'Nhập mã SIEUDEAL · Giảm 50K đơn đầu',
            'cta'      => 'Đặt ngay',
            'brand'    => 'GrabFood',
            'color'    => '00B14F',
            'target'   => 'https://food.grab.com',
        ],
        [
            'name'     => 'Be Bike — Freeship Cuối Tuần',
            'image'    => 'grab-bike.jpg',
            'headline' => 'BE BIKE',
            'sub'      => '0đ phí ship · Cuối tuần đặt xe nhanh',
            'cta'      => 'Đặt xe',
            'brand'    => 'Be',
            'color'    => 'FFCD00',
            'target'   => 'https://be.com.vn',
        ],

        // ─── Banking / Fintech ──────────────────
        [
            'name'     => 'Techcombank Visa Cashback 8%',
            'image'    => 'bank-card.jpg',
            'headline' => 'THẺ TECHCOMBANK VISA',
            'sub'      => 'Hoàn 8% chi tiêu · Mở thẻ online',
            'cta'      => 'Mở thẻ ngay',
            'brand'    => 'Techcombank',
            'color'    => 'EE0033',
            'target'   => 'https://techcombank.com',
        ],
        [
            'name'     => 'VPBank NEO — Vay Tín Chấp',
            'image'    => 'fintech.jpg',
            'headline' => 'VPBANK NEO',
            'sub'      => 'Vay tín chấp · Duyệt online trong 5 phút',
            'cta'      => 'Đăng ký vay',
            'brand'    => 'VPBank',
            'color'    => '00B74F',
            'target'   => 'https://vpbank.com.vn',
        ],

        // ─── Education ──────────────────────────
        [
            'name'     => 'IELTS Fighter — Giảm 70%',
            'image'    => 'course-ielts.jpg',
            'headline' => 'IELTS FIGHTER',
            'sub'      => 'Khóa luyện thi 8.0 · Giảm 70% tháng này',
            'cta'      => 'Học thử',
            'brand'    => 'IELTS Fighter',
            'color'    => '1565C0',
            'target'   => 'https://ielts-fighter.com',
        ],
        [
            'name'     => 'Funix — Học Lập Trình',
            'image'    => 'online-learning.jpg',
            'headline' => 'FUNIX',
            'sub'      => 'Học lập trình từ xa · Bằng FPT',
            'cta'      => 'Tìm hiểu',
            'brand'    => 'Funix',
            'color'    => 'F95738',
            'target'   => 'https://funix.edu.vn',
        ],

        // ─── Streaming / Telco ──────────────────
        [
            'name'     => 'FPT Play VIP — Free 1 Tháng',
            'image'    => 'streaming-tv.jpg',
            'headline' => 'FPT PLAY VIP',
            'sub'      => 'Trải nghiệm 1 tháng miễn phí',
            'cta'      => 'Đăng ký free',
            'brand'    => 'FPT Play',
            'color'    => 'F47A20',
            'target'   => 'https://fptplay.vn',
        ],
        [
            'name'     => 'Viettel 5G — Gói Cước Mới',
            'image'    => '5g-internet.jpg',
            'headline' => 'VIETTEL 5G',
            'sub'      => 'Data không giới hạn · Tốc độ Gbps',
            'cta'      => 'Đăng ký gói',
            'brand'    => 'Viettel',
            'color'    => 'CC0000',
            'target'   => 'https://viettel.vn',
        ],
        [
            'name'     => 'Mobifone — Gói Combo',
            'image'    => 'telco-mobile.jpg',
            'headline' => 'MOBIFONE COMBO',
            'sub'      => '4G mạnh + Gọi + SMS · Chỉ 99K/tháng',
            'cta'      => 'Đăng ký',
            'brand'    => 'Mobifone',
            'color'    => '003DA5',
            'target'   => 'https://mobifone.vn',
        ],
    ];

    public function definition(): array
    {
        $ad = fake()->randomElement(self::ADS);
        $placement = fake()->randomElement(['top','side','bottom']);

        // Encode ad data as compact JSON in the `content` field
        // (uses the original image URL location + metadata for overlay rendering)
        $payload = json_encode([
            'image' => '/images/ads/'.$ad['image'],
            'headline' => $ad['headline'],
            'sub'      => $ad['sub'],
            'cta'      => $ad['cta'],
            'brand'    => $ad['brand'],
            'color'    => '#'.$ad['color'],
        ]);

        return [
            'name'        => $ad['name'].' · '.strtoupper($placement),
            'placement'   => $placement,
            'type'        => 'banner_image',
            'content'     => $payload,
            'target_url'  => $ad['target'],
            'weight'      => fake()->numberBetween(1, 10),
            'status'      => 'active',
        ];
    }
}

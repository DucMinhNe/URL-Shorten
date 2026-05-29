<?php

namespace Database\Seeders;

use App\Models\ReportedLink;
use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportedLinkSeeder extends Seeder
{
    public function run(): void
    {
        $linkIds = ShortLink::inRandomOrder()->limit(200)->pluck('id')->toArray();
        $admin = User::where('is_admin', true)->first();

        $reasons = ['spam', 'malware', 'phishing', 'inappropriate', 'copyright', 'scam', 'other'];
        $statusPool = ['pending', 'pending', 'pending', 'reviewing', 'confirmed', 'confirmed', 'dismissed'];
        $actions = ['none', 'warned', 'disabled_link', 'blacklisted_domain', 'banned_user'];

        $descs = [
            'Link redirect đến site lừa đảo, dụ nạp thẻ điện thoại',
            'Tải file .exe khả nghi, antivirus warn',
            'Trang phishing giả Facebook login',
            'Quảng cáo cá độ, không phù hợp Việt Nam',
            'Bài viết copy nguyên xi từ blog cá nhân của tôi',
            'Link dẫn đến web 18+',
            'Mạo danh ngân hàng VCB, lừa OTP',
            'Tải malware miner Monero',
            'Link đến shop bán hàng giả',
            'Spam vào group Zalo của tao',
        ];

        $bulk = [];
        foreach ($linkIds as $i => $linkId) {
            $reason = fake()->randomElement($reasons);
            $status = fake()->randomElement($statusPool);
            $createdAt = fake()->dateTimeBetween('-30 days', '-1 hour');
            $reviewedAt = $status !== 'pending' ? fake()->dateTimeBetween($createdAt, 'now') : null;

            $bulk[] = [
                'short_link_id' => $linkId,
                'reporter_user_id' => fake()->boolean(40)
                    ? User::where('is_admin', false)->inRandomOrder()->value('id') : null,
                'reporter_email' => fake()->boolean(70) ? fake()->safeEmail() : null,
                'reporter_ip' => fake()->ipv4(),
                'reason' => $reason,
                'description' => fake()->randomElement($descs),
                'status' => $status,
                'reviewed_by' => $reviewedAt ? $admin->id : null,
                'reviewed_at' => $reviewedAt,
                'admin_note' => $status === 'confirmed' ? 'Đã verify, vi phạm chính sách. ' . fake()->sentence() : null,
                'action_taken' => $status === 'confirmed' ? fake()->randomElement($actions) : null,
                'created_at' => $createdAt,
                'updated_at' => $reviewedAt ?? $createdAt,
            ];
        }

        ReportedLink::insert($bulk);
    }
}

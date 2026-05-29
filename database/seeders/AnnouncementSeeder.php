<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@demo.com')->first();

        $items = [
            [
                'title' => '🎉 Welcome bonus 50.000đ cho user mới',
                'body' => '<p>Đăng ký tài khoản mới trước ngày <strong>30/06</strong> để nhận ngay 50.000đ vào ví. Áp dụng cho 1000 user đầu tiên.</p>',
                'type' => 'feature', 'target' => 'all', 'show_on_login' => true,
                'starts_at' => now()->subDays(10), 'ends_at' => now()->addDays(20),
                'view_count' => fake()->numberBetween(1500, 8000),
            ],
            [
                'title' => '⚡ Rate boost 1.5x cuối tuần này',
                'body' => '<p>Từ <strong>23/05 đến 25/05</strong>, mọi click hợp lệ được nhân 1.5x đơn giá. Share link ngay!</p>',
                'type' => 'success', 'target' => 'creators', 'show_on_dashboard' => true,
                'starts_at' => now()->subDay(), 'ends_at' => now()->addDays(2),
                'view_count' => fake()->numberBetween(800, 3000),
            ],
            [
                'title' => '🔧 Bảo trì hệ thống chủ nhật 24/05 02:00-04:00',
                'body' => '<p>Hệ thống sẽ tạm dừng để nâng cấp DB. Trong khoảng thời gian này, redirect vẫn hoạt động bình thường, nhưng dashboard có thể không truy cập được.</p>',
                'type' => 'warning', 'target' => 'all',
                'starts_at' => now()->subDays(2), 'ends_at' => now()->addDays(1),
                'view_count' => fake()->numberBetween(2000, 5000),
            ],
            [
                'title' => '✨ Tính năng mới: Tag liên kết',
                'body' => '<p>Bây giờ bạn có thể gắn tag vào link để dễ quản lý. Vào trang sửa link để thêm tag.</p>',
                'type' => 'feature', 'target' => 'creators',
                'view_count' => fake()->numberBetween(500, 2000),
            ],
            [
                'title' => '🚫 Cảnh báo: Domain lừa đảo',
                'body' => '<p>Bọn tao đã chặn 5 domain phishing trong tuần qua. Hệ thống tự động flag link đến các domain này. Vui lòng <strong>không rút gọn link lạ</strong> bạn không tin tưởng.</p>',
                'type' => 'danger', 'target' => 'all', 'is_dismissible' => false,
                'view_count' => fake()->numberBetween(3000, 7000),
            ],
            [
                'title' => '💳 Hỗ trợ thêm Vietcombank QR',
                'body' => '<p>Ngoài Momo / ZaloPay / PayPal, từ tuần sau bạn có thể nhận tiền qua Vietcombank QR.</p>',
                'type' => 'info', 'target' => 'creators',
                'starts_at' => now()->subDays(5),
                'view_count' => fake()->numberBetween(400, 1800),
            ],
            [
                'title' => '📊 Báo cáo tháng 04: 17.6M+ đ đã trả cho creator',
                'body' => '<p>Trong tháng 4/2026, LinkPay đã thanh toán hơn 17.6 triệu đ cho 124 creator. Cảm ơn các bạn!</p>',
                'type' => 'info', 'target' => 'all',
                'starts_at' => now()->subDays(20), 'ends_at' => now()->subDays(5),
                'is_active' => false,
                'view_count' => fake()->numberBetween(4500, 9000),
            ],
            [
                'title' => '⚠️ Tăng cường chống self-click',
                'body' => '<p>Tao vừa rollout thuật toán mới để phát hiện self-click qua fingerprint. Đừng tự click link của mình — sẽ bị flag và mất quyền rút tiền.</p>',
                'type' => 'warning', 'target' => 'creators',
                'view_count' => fake()->numberBetween(1200, 3500),
            ],
            [
                'title' => '🎁 Coupon SUMMER2026 — Bonus 30k',
                'body' => '<p>Nhập mã <code>SUMMER2026</code> ở trang ví để nhận 30.000đ bonus. Hết hạn 30/06.</p>',
                'type' => 'feature', 'target' => 'users', 'show_on_dashboard' => true,
                'view_count' => fake()->numberBetween(600, 2400),
            ],
            [
                'title' => '👑 Chương trình VIP Creator',
                'body' => '<p>Creator có >10 link active và doanh thu >500k/tháng sẽ được mời vào nhóm VIP. Quyền lợi: rate boost 1.5x, duyệt rút trong 6h, support riêng.</p>',
                'type' => 'info', 'target' => 'creators',
                'view_count' => fake()->numberBetween(700, 2200),
            ],
            [
                'title' => '📱 App Android sắp ra mắt',
                'body' => '<p>App LinkPay Android dự kiến phát hành tháng 7. Đăng ký nhận thông báo qua email!</p>',
                'type' => 'feature', 'target' => 'all',
                'view_count' => fake()->numberBetween(300, 1500),
            ],
            [
                'title' => '🐛 Đã fix bug hiển thị balance sai',
                'body' => '<p>Một số user phản ánh balance không cập nhật sau khi rút tiền. Đã fix lúc 14:30 ngày 22/05.</p>',
                'type' => 'success', 'target' => 'users',
                'starts_at' => now()->subDays(1), 'ends_at' => now()->addDay(),
                'view_count' => fake()->numberBetween(200, 800),
            ],
        ];

        foreach ($items as $item) {
            Announcement::create(array_merge($item, ['created_by' => $admin?->id]));
        }
    }
}

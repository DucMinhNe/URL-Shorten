<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['name' => 'Bắt đầu',         'icon' => 'heroicon-o-sparkles',      'sort_order' => 1],
            ['name' => 'Rút tiền',        'icon' => 'heroicon-o-banknotes',     'sort_order' => 2],
            ['name' => 'Tài khoản',       'icon' => 'heroicon-o-user-circle',   'sort_order' => 3],
            ['name' => 'Bảo mật',         'icon' => 'heroicon-o-shield-check',  'sort_order' => 4],
            ['name' => 'Quảng cáo',       'icon' => 'heroicon-o-megaphone',     'sort_order' => 5],
            ['name' => 'Sự cố kỹ thuật',  'icon' => 'heroicon-o-wrench-screwdriver', 'sort_order' => 6],
            ['name' => 'Chính sách',      'icon' => 'heroicon-o-document-text', 'sort_order' => 7],
            ['name' => 'API & Tích hợp',  'icon' => 'heroicon-o-cube',          'sort_order' => 8],
        ];

        $catMap = [];
        foreach ($cats as $c) {
            $cat = FaqCategory::create(array_merge($c, [
                'slug' => Str::slug($c['name']),
                'description' => 'Câu hỏi về '.$c['name'],
                'is_published' => true,
            ]));
            $catMap[$c['name']] = $cat->id;
        }

        $faqs = [
            // Bắt đầu
            ['cat' => 'Bắt đầu', 'q' => 'LinkPay là gì?', 'a' => '<p>LinkPay là dịch vụ rút gọn URL kèm quảng cáo, giúp bạn kiếm tiền từ mỗi lượt click hợp lệ.</p>'],
            ['cat' => 'Bắt đầu', 'q' => 'Đăng ký mất phí không?', 'a' => '<p>Không. Hoàn toàn miễn phí. Bạn còn được 50.000đ welcome bonus khi đăng ký mới.</p>'],
            ['cat' => 'Bắt đầu', 'q' => 'Cần xác minh giấy tờ không?', 'a' => '<p>Không bắt buộc. Chỉ khi rút trên 5 triệu/lần thì cần KYC để chống rửa tiền.</p>'],
            ['cat' => 'Bắt đầu', 'q' => 'Có giới hạn số link tạo không?', 'a' => '<p>Tài khoản thường: tối đa 500 link active. VIP Creator: không giới hạn.</p>'],

            // Rút tiền
            ['cat' => 'Rút tiền', 'q' => 'Tối thiểu bao nhiêu mới rút được?', 'a' => '<p><strong>100.000đ</strong> cho Momo/ZaloPay, <strong>$4 USD</strong> cho PayPal.</p>', 'featured' => true],
            ['cat' => 'Rút tiền', 'q' => 'Bao lâu thì nhận được tiền?', 'a' => '<p>Trong vòng 24h kể từ khi admin duyệt. VIP Creator: 6h.</p>', 'featured' => true],
            ['cat' => 'Rút tiền', 'q' => 'Có phí rút tiền không?', 'a' => '<p>Hoàn toàn không phí. Chỉ PayPal có thể bị PayPal tính ~$0.30/giao dịch quốc tế.</p>'],
            ['cat' => 'Rút tiền', 'q' => 'Sao yêu cầu rút bị từ chối?', 'a' => '<p>Thường do thông tin tài khoản không đúng. Admin sẽ note lý do cụ thể. Tiền được hoàn về ví trong cùng giao dịch.</p>'],
            ['cat' => 'Rút tiền', 'q' => 'Đổi phương thức rút được không?', 'a' => '<p>Được. Vào trang Rút tiền → chọn phương thức khác và điền tài khoản mới.</p>'],
            ['cat' => 'Rút tiền', 'q' => 'Có giới hạn rút trong ngày không?', 'a' => '<p>Tối đa 3 yêu cầu/ngày. Mỗi yêu cầu cap 5 triệu/lần.</p>'],

            // Tài khoản
            ['cat' => 'Tài khoản', 'q' => 'Đổi email được không?', 'a' => '<p>Được. Vào Hồ sơ → đổi email. Email mới phải xác minh lại.</p>'],
            ['cat' => 'Tài khoản', 'q' => 'Quên mật khẩu phải làm sao?', 'a' => '<p>Click "Quên mật khẩu" ở trang login. Bọn tao gửi link reset trong 1 phút.</p>'],
            ['cat' => 'Tài khoản', 'q' => 'Xoá tài khoản có lấy lại được không?', 'a' => '<p>Không. Xoá là xoá vĩnh viễn toàn bộ link, click, doanh thu, ví. Hãy rút tiền trước khi xoá.</p>'],
            ['cat' => 'Tài khoản', 'q' => 'Kết nối Google account?', 'a' => '<p>Có. Trên trang login chọn "Đăng nhập bằng Google" — nếu email trùng, hai account sẽ được link.</p>'],

            // Bảo mật
            ['cat' => 'Bảo mật', 'q' => 'LinkPay có an toàn không?', 'a' => '<p>Có. Tao dùng HTTPS, bcrypt password, 2FA chuẩn bị, Cloudflare Turnstile chống bot.</p>'],
            ['cat' => 'Bảo mật', 'q' => 'Sao có thông báo đăng nhập lạ?', 'a' => '<p>Mỗi khi tài khoản đăng nhập từ IP mới, bọn tao gửi email cảnh báo. Nếu không phải bạn → đổi mật khẩu ngay.</p>'],
            ['cat' => 'Bảo mật', 'q' => '2FA khi nào ra?', 'a' => '<p>Đang phát triển, dự kiến tháng 7/2026.</p>'],
            ['cat' => 'Bảo mật', 'q' => 'Tôi có thể đặt password cho link không?', 'a' => '<p>Có. Vào sửa link → nhập mật khẩu. Người mở link phải nhập đúng pass mới xem được.</p>'],

            // Quảng cáo
            ['cat' => 'Quảng cáo', 'q' => 'Tại sao có quảng cáo trên trang trung gian?', 'a' => '<p>Doanh thu từ quảng cáo là nguồn để chia cho creator. Mỗi 1000 view hợp lệ = 5000đ.</p>'],
            ['cat' => 'Quảng cáo', 'q' => 'Click ad có bonus không?', 'a' => '<p>Chưa. Hiện chỉ tính impression. Click bonus sắp ra mắt.</p>'],
            ['cat' => 'Quảng cáo', 'q' => 'Có tắt quảng cáo không?', 'a' => '<p>User Pro 49k/tháng có thể tắt ad nhưng vẫn nhận tiền (chia cho ad alternative network).</p>'],
            ['cat' => 'Quảng cáo', 'q' => 'Cách trở thành ad partner?', 'a' => '<p>Email <a href="mailto:ads@linkpay.vn">ads@linkpay.vn</a> với portfolio.</p>'],

            // Sự cố
            ['cat' => 'Sự cố kỹ thuật', 'q' => 'Link bị 410 sao?', 'a' => '<p>Link đã bị disable bởi user hoặc admin (vi phạm chính sách). Vào Sửa link → đổi trạng thái thành Active.</p>'],
            ['cat' => 'Sự cố kỹ thuật', 'q' => 'Chart dashboard không load', 'a' => '<p>Thử reload Ctrl+F5. Nếu vẫn không được, xoá cache trình duyệt hoặc thử trình duyệt khác.</p>'],
            ['cat' => 'Sự cố kỹ thuật', 'q' => 'Balance bị âm', 'a' => '<p>Không thể âm. Nếu thấy, đó là bug — báo ngay qua ticket support.</p>'],
            ['cat' => 'Sự cố kỹ thuật', 'q' => 'Click không cộng vào view hợp lệ', 'a' => '<p>Có thể là: 1) IP đã view trong 24h qua, 2) Self-click, 3) Captcha fail. Xem dashboard có rate ~70% là OK.</p>', 'featured' => true],

            // Chính sách
            ['cat' => 'Chính sách', 'q' => 'Link nào bị cấm?', 'a' => '<p>Phishing, malware, 18+, cờ bạc, cá độ, lừa đảo, vi phạm bản quyền.</p>'],
            ['cat' => 'Chính sách', 'q' => 'Tự click có phạt không?', 'a' => '<p>Hệ thống phát hiện tự động qua IP + fingerprint + user account. Phát hiện → flag click không hợp lệ. Nếu cố tình + nhiều → có thể bị ban tài khoản.</p>'],
            ['cat' => 'Chính sách', 'q' => 'Account bị ban thì sao?', 'a' => '<p>Email lý do ban. Có thể appeal qua ticket. Số dư bị đóng băng cho đến khi admin review.</p>'],
            ['cat' => 'Chính sách', 'q' => 'Có thuế thu nhập không?', 'a' => '<p>LinkPay không khấu trừ thuế. Bạn tự chịu trách nhiệm khai báo thu nhập với cơ quan thuế.</p>'],

            // API
            ['cat' => 'API & Tích hợp', 'q' => 'Có API public không?', 'a' => '<p>Sắp có. Dự kiến quý 3/2026. Sẽ hỗ trợ rút gọn link, query stats, webhook click.</p>'],
            ['cat' => 'API & Tích hợp', 'q' => 'Tích hợp WordPress?', 'a' => '<p>Plugin chính thức trên WP repository — search "LinkPay Shortener".</p>'],
            ['cat' => 'API & Tích hợp', 'q' => 'Tự deploy bản self-host?', 'a' => '<p>Bản open-source trên GitHub. Có sẵn Docker compose.</p>'],
        ];

        foreach ($faqs as $i => $f) {
            Faq::create([
                'category_id' => $catMap[$f['cat']] ?? null,
                'question' => $f['q'],
                'answer' => $f['a'],
                'sort_order' => $i + 1,
                'is_published' => true,
                'is_featured' => $f['featured'] ?? false,
                'helpful_count' => fake()->numberBetween(5, 250),
                'not_helpful_count' => fake()->numberBetween(0, 30),
                'view_count' => fake()->numberBetween(50, 3000),
                'tags' => fake()->randomElements(['payout', 'beginner', 'account', 'security', 'policy'], rand(0, 2)),
            ]);
        }
    }
}

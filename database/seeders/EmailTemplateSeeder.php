<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'welcome', 'name' => 'Chào mừng user mới',
                'subject' => '🎉 Chào mừng {{ user_name }} đến với LinkPay!',
                'body_html' => '<h1>Chào {{ user_name }}!</h1><p>Cảm ơn bạn đã đăng ký LinkPay. Số dư khởi đầu: <strong>{{ welcome_bonus }}đ</strong>.</p><p>Bắt đầu tạo link đầu tiên của bạn ngay!</p>',
                'body_text' => 'Chào {{ user_name }}! Cảm ơn bạn đã đăng ký LinkPay. Welcome bonus: {{ welcome_bonus }}đ.',
                'variables' => ['user_name', 'welcome_bonus', 'login_url'],
                'sent_count' => 248,
            ],
            [
                'key' => 'email_verify', 'name' => 'Xác minh email',
                'subject' => 'Xác minh địa chỉ email của bạn',
                'body_html' => '<p>Click <a href="{{ verify_url }}">vào đây</a> để xác minh email. Link có hiệu lực 60 phút.</p>',
                'variables' => ['verify_url', 'user_name'],
                'sent_count' => 312,
            ],
            [
                'key' => 'password_reset', 'name' => 'Reset mật khẩu',
                'subject' => 'Đặt lại mật khẩu LinkPay',
                'body_html' => '<p>Có ai đó (hi vọng là bạn) đã yêu cầu reset mật khẩu. <a href="{{ reset_url }}">Click đây</a> để đặt mật khẩu mới.</p>',
                'variables' => ['reset_url', 'user_name'],
                'sent_count' => 87,
            ],
            [
                'key' => 'payout_paid', 'name' => 'Đã chuyển tiền rút',
                'subject' => '✅ Yêu cầu rút {{ amount }}đ của bạn đã được chuyển',
                'body_html' => '<p>Tiền {{ amount }}đ đã được chuyển qua <strong>{{ method }}</strong>.</p><p>Mã giao dịch: <code>{{ transaction_ref }}</code></p>',
                'variables' => ['amount', 'method', 'transaction_ref'],
                'sent_count' => 89,
            ],
            [
                'key' => 'payout_rejected', 'name' => 'Yêu cầu rút bị từ chối',
                'subject' => '❌ Yêu cầu rút tiền bị từ chối',
                'body_html' => '<p>Yêu cầu rút {{ amount }}đ của bạn đã bị từ chối với lý do:</p><blockquote>{{ reason }}</blockquote><p>Số tiền đã được hoàn lại vào ví.</p>',
                'variables' => ['amount', 'reason'],
                'sent_count' => 23,
            ],
            [
                'key' => 'link_disabled', 'name' => 'Link bị vô hiệu hoá',
                'subject' => '⚠️ Link /{{ slug }} của bạn đã bị vô hiệu hoá',
                'body_html' => '<p>Link <code>/{{ slug }}</code> đã bị admin vô hiệu hoá vì:</p><p>{{ reason }}</p>',
                'variables' => ['slug', 'reason'],
                'sent_count' => 12,
            ],
            [
                'key' => 'ticket_replied', 'name' => 'Admin trả lời ticket',
                'subject' => '[{{ ticket_code }}] Admin đã trả lời ticket',
                'body_html' => '<p>Ticket {{ ticket_code }} có phản hồi mới từ {{ staff_name }}:</p><blockquote>{{ message_excerpt }}</blockquote><p><a href="{{ ticket_url }}">Xem ticket →</a></p>',
                'variables' => ['ticket_code', 'staff_name', 'message_excerpt', 'ticket_url'],
                'sent_count' => 156,
            ],
            [
                'key' => 'weekly_summary', 'name' => 'Báo cáo tuần',
                'subject' => '📊 Báo cáo tuần — Bạn kiếm được {{ weekly_earned }}đ',
                'body_html' => '<p>Tuần này:</p><ul><li>Click: {{ clicks }}</li><li>View hợp lệ: {{ valid_views }}</li><li>Doanh thu: <strong>{{ weekly_earned }}đ</strong></li></ul>',
                'variables' => ['weekly_earned', 'clicks', 'valid_views'],
                'sent_count' => 1240,
            ],
            [
                'key' => 'promo_redeemed', 'name' => 'Đã đổi mã promo',
                'subject' => '🎁 Đổi mã {{ code }} thành công',
                'body_html' => '<p>Bạn vừa nhận {{ value }} từ mã <code>{{ code }}</code>.</p>',
                'variables' => ['code', 'value'],
                'sent_count' => 432,
            ],
            [
                'key' => 'security_login', 'name' => 'Cảnh báo đăng nhập lạ',
                'subject' => '🔐 Đăng nhập từ thiết bị mới',
                'body_html' => '<p>Tài khoản của bạn vừa đăng nhập từ <strong>{{ ip }} ({{ location }})</strong> vào lúc {{ time }}.</p><p>Nếu không phải bạn, đổi mật khẩu ngay.</p>',
                'variables' => ['ip', 'location', 'time'],
                'sent_count' => 67,
            ],
            [
                'key' => 'announcement_blast', 'name' => 'Gửi announcement qua email',
                'subject' => '{{ announcement_title }}',
                'body_html' => '<h2>{{ announcement_title }}</h2><div>{{ announcement_body }}</div>',
                'variables' => ['announcement_title', 'announcement_body'],
                'sent_count' => 3120,
            ],
            [
                'key' => 'kyc_required', 'name' => 'Yêu cầu KYC trước rút >5tr',
                'subject' => '📋 Cần xác minh danh tính',
                'body_html' => '<p>Bạn đã đạt mức cần KYC. Vui lòng <a href="{{ kyc_url }}">điền form</a>.</p>',
                'variables' => ['kyc_url'],
                'sent_count' => 4,
                'is_active' => false,
            ],
        ];

        foreach ($templates as $t) {
            EmailTemplate::create(array_merge([
                'locale' => 'vi',
                'is_active' => true,
                'from_name' => 'LinkPay Team',
                'from_email' => 'noreply@linkpay.vn',
                'last_sent_at' => now()->subDays(rand(0, 30)),
            ], $t));
        }
    }
}

<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Nhãn tiếng Việt tập trung cho mọi enum hiển thị (badge/column) trong admin + public.
 * Dùng: Labels::get('priority', $state)  → "Cao"
 * Không có map → humanize: 'in_progress' → 'In progress'.
 */
class Labels
{
    public const MAP = [
        // Support ticket
        'ticket_category' => [
            'payout' => 'Thanh toán', 'account' => 'Tài khoản', 'link_issue' => 'Lỗi liên kết',
            'fraud_report' => 'Báo cáo gian lận', 'feature_request' => 'Yêu cầu tính năng',
            'bug' => 'Lỗi kỹ thuật', 'other' => 'Khác',
        ],
        'priority' => [
            'low' => 'Thấp', 'normal' => 'Bình thường', 'high' => 'Cao', 'urgent' => 'Khẩn cấp',
        ],
        'ticket_status' => [
            'open' => 'Mới mở', 'in_progress' => 'Đang xử lý', 'waiting_user' => 'Chờ phản hồi',
            'resolved' => 'Đã giải quyết', 'closed' => 'Đã đóng',
        ],
        // Short link
        'link_status' => [
            'active' => 'Hoạt động', 'disabled' => 'Đã tắt', 'blocked' => 'Bị chặn',
            'expired' => 'Hết hạn', 'limit_reached' => 'Đạt giới hạn',
        ],
        // User
        'user_status' => ['active' => 'Hoạt động', 'banned' => 'Bị cấm'],
        // Payout / wallet
        'method' => ['momo' => 'MoMo', 'zalo' => 'ZaloPay', 'paypal' => 'PayPal'],
        'payout_status' => [
            'pending' => 'Chờ duyệt', 'approved' => 'Đã duyệt', 'rejected' => 'Từ chối', 'paid' => 'Đã trả',
        ],
        'wallet_type' => [
            'credit' => 'Cộng tiền', 'payout_hold' => 'Giữ rút', 'payout_release' => 'Nhả rút',
            'payout_reject' => 'Hoàn rút', 'admin_adjust' => 'Điều chỉnh',
        ],
        // Ad campaign
        'ad_placement' => ['top' => 'Trên cùng', 'side' => 'Bên cạnh', 'bottom' => 'Dưới cùng'],
        'ad_status' => ['active' => 'Đang chạy', 'paused' => 'Tạm dừng'],
        'ad_type' => ['banner_image' => 'Ảnh banner', 'html' => 'HTML', 'iframe' => 'Iframe'],
        // Promo code
        'promo_type' => [
            'bonus_credit' => 'Tặng tiền', 'payout_fee_waiver' => 'Miễn phí rút',
            'rate_boost' => 'Tăng tỉ lệ', 'welcome_bonus' => 'Thưởng chào mừng',
        ],
        'value_unit' => ['vnd' => 'VNĐ', 'percent' => 'Phần trăm'],
        // Announcement
        'announcement_type' => [
            'info' => 'Thông tin', 'success' => 'Thành công', 'warning' => 'Cảnh báo',
            'danger' => 'Khẩn', 'feature' => 'Tính năng mới',
        ],
        'announcement_target' => [
            'all' => 'Tất cả', 'users' => 'Người dùng', 'admins' => 'Quản trị', 'creators' => 'Creator',
        ],
        // Reported link
        'report_reason' => [
            'spam' => 'Spam', 'malware' => 'Mã độc', 'phishing' => 'Giả mạo (phishing)',
            'inappropriate' => 'Không phù hợp', 'copyright' => 'Vi phạm bản quyền', 'scam' => 'Lừa đảo', 'other' => 'Khác',
        ],
        'report_status' => [
            'pending' => 'Chờ xử lý', 'reviewing' => 'Đang xem xét',
            'confirmed' => 'Đã xác nhận', 'dismissed' => 'Đã bỏ qua',
        ],
        'report_action' => [
            'none' => 'Chưa xử lý', 'warned' => 'Đã cảnh báo', 'disabled_link' => 'Đã tắt link',
            'blacklisted_domain' => 'Đã chặn tên miền', 'banned_user' => 'Đã cấm tài khoản',
        ],
        'severity' => ['low' => 'Thấp', 'medium' => 'Trung bình', 'high' => 'Cao', 'critical' => 'Nghiêm trọng'],
        // Setting
        'setting_type' => ['string' => 'Chuỗi', 'integer' => 'Số nguyên', 'boolean' => 'Đúng/Sai', 'json' => 'JSON'],
    ];

    public static function get(string $group, ?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return self::MAP[$group][$value] ?? self::humanize($value);
    }

    /** Fallback: 'in_progress' → 'In progress' (viết hoa chữ đầu, thay _ bằng dấu cách). */
    public static function humanize(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return Str::ucfirst(str_replace(['_', '-'], ' ', $value));
    }

    /** Trả về map [value => nhãn] cho dùng trong SelectFilter/options. */
    public static function options(string $group): array
    {
        return self::MAP[$group] ?? [];
    }
}

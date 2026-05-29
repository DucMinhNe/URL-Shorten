<?php

/*
|--------------------------------------------------------------------------
| FAQ — câu hỏi thường gặp
|--------------------------------------------------------------------------
| Dữ liệu tĩnh, KHÔNG dùng DB (giữ nhẹ). Trang /faq và section FAQ ở landing
| đều đọc từ đây. Mỗi nhóm có icon (heroicon) + danh sách câu hỏi.
| Đánh dấu 'featured' => true cho câu muốn hiện ở landing.
*/

return [
    'groups' => [
        [
            'title' => 'Thanh toán & rút tiền',
            'icon' => 'heroicon-o-banknotes',
            'items' => [
                [
                    'q' => 'Bao lâu mới có thể rút tiền?',
                    'a' => 'Khi số dư đạt 100.000đ (Momo/ZaloPay) hoặc $4 (PayPal), bạn gửi yêu cầu rút. Admin duyệt và chuyển trong vòng 24h.',
                    'featured' => true,
                ],
                [
                    'q' => 'Có phí ẩn không?',
                    'a' => 'Hoàn toàn không. 0đ phí tạo link, 0đ phí rút tiền. Bạn chỉ chịu phí ngân hàng nếu PayPal có (~$0.30 cho giao dịch quốc tế).',
                    'featured' => true,
                ],
                [
                    'q' => 'Hỗ trợ những phương thức rút nào?',
                    'a' => 'Hiện hỗ trợ MoMo, ZaloPay và PayPal. Bạn cài phương thức mặc định trong trang Hồ sơ và đổi bất cứ lúc nào.',
                    'featured' => false,
                ],
            ],
        ],
        [
            'title' => 'Cách kiếm tiền',
            'icon' => 'heroicon-o-cursor-arrow-rays',
            'items' => [
                [
                    'q' => 'Ai chi tiền? Quảng cáo từ đâu?',
                    'a' => 'Đối tác quảng cáo trả phí khi banner của họ hiển thị trên trang trung gian 5 giây. Hệ thống giữ một phần để vận hành, phần còn lại trả cho người tạo link theo CPM cố định.',
                    'featured' => true,
                ],
                [
                    'q' => 'Tôi có thể tự click link của mình để kiếm tiền không?',
                    'a' => 'Không. Hệ thống nhận diện self-click qua tài khoản và IP. Click không hợp lệ vẫn được ghi nhận nhưng không cộng tiền.',
                    'featured' => true,
                ],
                [
                    'q' => 'View hợp lệ được tính thế nào?',
                    'a' => 'Một view hợp lệ là khi người xem vượt qua captcha, ở lại đủ thời gian đếm ngược, và không trùng IP trong 24h. Chỉ view hợp lệ mới cộng tiền.',
                    'featured' => false,
                ],
            ],
        ],
        [
            'title' => 'Quản lý liên kết',
            'icon' => 'heroicon-o-link',
            'items' => [
                [
                    'q' => 'Link rút gọn có hết hạn không?',
                    'a' => 'Mặc định không hết hạn. Bạn có thể đặt ngày hết hạn hoặc giới hạn số click trong phần "Tuỳ chọn nâng cao" khi tạo/sửa link, hoặc tự tắt link bất cứ lúc nào.',
                    'featured' => true,
                ],
                [
                    'q' => 'Tôi có thể đặt mật khẩu cho link không?',
                    'a' => 'Có. Khi tạo hoặc sửa link, thêm mật khẩu bảo vệ — người xem phải nhập đúng mật khẩu mới mở được liên kết.',
                    'featured' => false,
                ],
                [
                    'q' => 'Xem thống kê chi tiết của một link ở đâu?',
                    'a' => 'Trong trang "Liên kết của tôi", bấm biểu tượng biểu đồ ở mỗi dòng để xem click theo ngày, thiết bị, trình duyệt và nguồn truy cập.',
                    'featured' => false,
                ],
            ],
        ],
    ],
];

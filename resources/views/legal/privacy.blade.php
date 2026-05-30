@php
    $h = 'font-size:18px;font-weight:800;color:var(--color-ink-deep);margin:22px 0 8px;';
    $p = 'color:var(--color-charcoal);margin:0 0 10px;';
@endphp
<x-legal-layout title="Chính sách bảo mật" active="privacy">
    <p style="{{ $p }}">LinkPay tôn trọng quyền riêng tư của bạn. Chính sách này mô tả dữ liệu chúng tôi thu thập và cách sử dụng.</p>

    <h2 style="{{ $h }}">1. Dữ liệu thu thập</h2>
    <p style="{{ $p }}">Thông tin tài khoản (tên, email), thông tin thanh toán bạn cung cấp khi rút tiền (số MoMo/ZaloPay/PayPal), và dữ liệu kỹ thuật khi có lượt truy cập liên kết: địa chỉ IP, trình duyệt, thiết bị, nguồn truy cập (referrer) — phục vụ tính lượt xem hợp lệ và chống gian lận.</p>

    <h2 style="{{ $h }}">2. Mục đích sử dụng</h2>
    <p style="{{ $p }}">Vận hành dịch vụ, tính thu nhập, xử lý rút tiền, phát hiện gian lận, và cải thiện trải nghiệm. Chúng tôi không bán dữ liệu cá nhân của bạn.</p>

    <h2 style="{{ $h }}">3. Cookie</h2>
    <p style="{{ $p }}">Chúng tôi dùng cookie để duy trì phiên đăng nhập và ghi nhận mã giới thiệu. Bạn có thể tắt cookie trong trình duyệt, nhưng một số tính năng có thể không hoạt động.</p>

    <h2 style="{{ $h }}">4. Chia sẻ với bên thứ ba</h2>
    <p style="{{ $p }}">Dữ liệu chỉ được chia sẻ với nhà cung cấp thanh toán để xử lý rút tiền, hoặc khi pháp luật yêu cầu. Quảng cáo hiển thị trên trang chờ không truy cập dữ liệu cá nhân của bạn.</p>

    <h2 style="{{ $h }}">5. Quyền của bạn</h2>
    <p style="{{ $p }}">Bạn có thể xem, sửa thông tin trong trang Hồ sơ, hoặc yêu cầu xoá tài khoản. Khi xoá, dữ liệu cá nhân sẽ được gỡ trừ phần phải lưu theo quy định kế toán/chống gian lận.</p>

    <h2 style="{{ $h }}">6. Liên hệ</h2>
    <p style="{{ $p }}">Mọi thắc mắc về dữ liệu, vui lòng <a href="{{ route('contact') }}" style="color:var(--color-primary);font-weight:700;">liên hệ với chúng tôi</a>.</p>
</x-legal-layout>

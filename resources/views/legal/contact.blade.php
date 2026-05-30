@php
    $h = 'font-size:18px;font-weight:800;color:var(--color-ink-deep);margin:22px 0 8px;';
    $p = 'color:var(--color-charcoal);margin:0 0 10px;';
@endphp
<x-legal-layout title="Liên hệ" active="contact">
    <p style="{{ $p }}">Cần hỗ trợ? Đội ngũ LinkPay luôn sẵn sàng giúp bạn.</p>

    <h2 style="{{ $h }}">Email hỗ trợ</h2>
    <p style="{{ $p }}"><a href="mailto:support@mess.io.vn" style="color:var(--color-primary);font-weight:700;">support@mess.io.vn</a> — phản hồi trong vòng 24 giờ làm việc.</p>

    <h2 style="{{ $h }}">Báo cáo gian lận / liên kết xấu</h2>
    <p style="{{ $p }}"><a href="mailto:report@mess.io.vn" style="color:var(--color-primary);font-weight:700;">report@mess.io.vn</a> — kèm đường dẫn rút gọn để chúng tôi xử lý nhanh.</p>

    <h2 style="{{ $h }}">Hợp tác quảng cáo</h2>
    <p style="{{ $p }}">Muốn đặt banner trên trang chờ LinkPay? Gửi email tới <a href="mailto:ads@mess.io.vn" style="color:var(--color-primary);font-weight:700;">ads@mess.io.vn</a>.</p>

    <h2 style="{{ $h }}">Câu hỏi thường gặp</h2>
    <p style="{{ $p }}">Nhiều thắc mắc về kiếm tiền, rút tiền và lượt xem hợp lệ đã có sẵn lời giải trong trang <a href="{{ route('faq') }}" style="color:var(--color-primary);font-weight:700;">FAQ</a>.</p>
</x-legal-layout>

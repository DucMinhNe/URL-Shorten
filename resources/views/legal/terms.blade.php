@php
    $h = 'font-size:18px;font-weight:800;color:var(--color-ink-deep);margin:22px 0 8px;';
    $p = 'color:var(--color-charcoal);margin:0 0 10px;';
@endphp
<x-legal-layout title="Điều khoản sử dụng" active="terms">
    <p style="{{ $p }}">Bằng việc tạo tài khoản và sử dụng LinkPay, bạn đồng ý với các điều khoản dưới đây. Vui lòng đọc kỹ.</p>

    <h2 style="{{ $h }}">1. Tài khoản</h2>
    <p style="{{ $p }}">Bạn chịu trách nhiệm bảo mật thông tin đăng nhập và mọi hoạt động dưới tài khoản của mình. Mỗi người chỉ được sử dụng một tài khoản; tạo nhiều tài khoản để gian lận lượt xem sẽ bị khoá.</p>

    <h2 style="{{ $h }}">2. Sử dụng dịch vụ</h2>
    <p style="{{ $p }}">Không rút gọn liên kết tới nội dung vi phạm pháp luật, lừa đảo, mã độc, khiêu dâm, xâm phạm bản quyền hoặc spam. Chúng tôi có quyền chặn liên kết và đình chỉ tài khoản vi phạm mà không cần báo trước.</p>

    <h2 style="{{ $h }}">3. Kiếm tiền & lượt xem hợp lệ</h2>
    <p style="{{ $p }}">Thu nhập được tính theo <strong>lượt xem hợp lệ</strong>: mỗi IP chỉ tính một lần trong khoảng thời gian quy định, không tính lượt tự bấm và lượt bị nghi ngờ là bot. Mọi hành vi tăng lượt xem giả tạo (bot, auto-click, ép xem) sẽ bị huỷ thu nhập và khoá tài khoản.</p>

    <h2 style="{{ $h }}">4. Rút tiền</h2>
    <p style="{{ $p }}">Bạn có thể yêu cầu rút khi số dư đạt mức tối thiểu công bố trên trang Rút tiền. Yêu cầu được duyệt thủ công trong vòng 24 giờ làm việc. Chúng tôi có quyền tạm giữ hoặc từ chối thanh toán nếu phát hiện gian lận.</p>

    <h2 style="{{ $h }}">5. Giới thiệu bạn bè</h2>
    <p style="{{ $p }}">Người giới thiệu nhận phần trăm hoa hồng trên thu nhập của người được mời, không trừ vào thu nhập của người được mời. Tự giới thiệu chính mình hoặc lạm dụng chương trình sẽ bị huỷ hoa hồng.</p>

    <h2 style="{{ $h }}">6. Thay đổi điều khoản</h2>
    <p style="{{ $p }}">Chúng tôi có thể cập nhật điều khoản theo thời gian. Việc tiếp tục sử dụng dịch vụ sau khi cập nhật đồng nghĩa bạn chấp nhận điều khoản mới.</p>
</x-legal-layout>

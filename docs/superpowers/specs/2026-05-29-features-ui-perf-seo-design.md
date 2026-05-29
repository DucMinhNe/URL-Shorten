# LinkPay — Thêm tính năng + Tối ưu UI / Performance / SEO

**Ngày:** 2026-05-29
**Phạm vi:** Bổ sung tính năng hiển thị (màu mè, data thật), tối ưu UI, tối ưu performance (giữ no-build), tối ưu SEO trang chính.

## Ràng buộc
- **Giữ no-build**: không dựng lại Vite/Tailwind CLI. Asset commit sẵn ở `public/css/app.css`, `public/js/app.js`. Tối ưu CSS bằng gzip + cache thay vì purge.
- **Không hardcode data** mới: lấy từ DB / `Setting` / config.
- **FAQ để nhẹ**: data trong `config/faq.php`, KHÔNG dùng DB/migration.
- Không đụng `.env` production (MySQL hosting). Test local bằng sqlite.

## A. Tính năng mới

### A1. QR code mỗi link
- Package: `endroid/qr-code` (^6), render server-side, không cần Node.
- Route `GET /links/{shortLink}/qr` (auth, owner-only) → trả ảnh QR (PNG mặc định, `?format=svg`).
- Trên bảng `/links`: nút "QR" mở modal hiển thị QR + nút tải PNG/SVG. QR encode URL `route('link.show', slug)`.

### A2. Analytics chi tiết per-link
- Route `GET /links/{shortLink}/stats` (auth, owner-only).
- Tính từ bảng `clicks` thật:
  - Timeline 30 ngày: tổng click vs valid view/ngày (Chart.js line).
  - Device / Browser / OS breakdown: parse `user_agent` (helper `UserAgentParser`, không thêm package).
  - Top referrers: parse host từ `referer` (null → "Trực tiếp").
  - Tỉ lệ view hợp lệ, tổng earned, CTR donut.
- Trang dùng card gradient theo design system Sneat hiện có.

### A3. Link hết hạn + giới hạn click
- Migration mới: `short_links.expires_at` (nullable timestamp), `short_links.max_clicks` (nullable unsigned int).
- Model `ShortLink`: cast `expires_at` datetime; helper `isExpired()`, `isLimitReached()`, `isLive()`.
- Form tạo/sửa link: thêm field ngày hết hạn + max click (optional).
- `RedirectController@show`: nếu hết hạn / vượt limit → trang "link không khả dụng" thay vì redirect.
- Bảng `/links`: badge trạng thái màu (Hoạt động / Hết hạn / Đạt giới hạn / Tắt / Chặn).

### A4. FAQ page nhẹ + de-hardcode landing
- `config/faq.php`: mảng FAQ (câu hỏi/đáp). Không DB.
- Route `GET /faq` → trang FAQ đầy đủ, đọc từ config.
- Landing: section FAQ đọc từ config (bỏ mảng hardcode trong blade).
- Stats band landing: số thật — tổng valid views toàn hệ thống, CPM từ `Setting`, số link active, số phương thức payout. Có fallback nếu DB trống.

## B. Tối ưu UI
- Dashboard: stat-card gradient + animate đếm số (JS nhỏ), mini-sparkline, feed "hoạt động gần đây" (click mới nhất), empty state đẹp khi chưa có link.
- `/links`: nút copy link + toast xác nhận, badge trạng thái màu, hover/transition.
- Trang analytics & faq bám đúng component/класс Sneat sẵn có. Dọn inline-style lặp thành class tiện ích khi hợp lý.

## C. Tối ưu Performance (no-build)
- Bỏ `<script src="https://cdn.tailwindcss.com">` ở `countdown.blade.php` (trùng `app.css`); giữ inline CSS animation cần thiết.
- Ảnh ads `public/images/ads/*.jpg` → sinh bản `.webp` (PHP GD `imagewebp`), dùng `<picture>` với JPG fallback, thêm `loading="lazy"` + `width`/`height`.
- `public/.htaccess`: bật `mod_deflate` (gzip) + `Cache-Control: public, max-age=31536000, immutable` cho css/js/svg/jpg/webp/woff2.
- Dashboard: eager-load quan hệ tránh N+1.

## D. Tối ưu SEO trang chính
- Component `<x-seo>` (props: title, description, image, type) xuất: meta description, Open Graph, Twitter card, canonical (`config('app.url')` + path), `theme-color`. Dùng ở `layouts/guest.blade.php` + override ở home.
- JSON-LD trên home: `Organization` + `WebSite` + `FAQPage` (từ config FAQ).
- Favicon thật (hiện 0 byte): tạo `favicon.svg` brand "LP", `favicon.ico`, `apple-touch-icon.png`, `site.webmanifest`, `theme-color`.
- `GET /sitemap.xml` động: liệt kê route public (home, faq, login, register). Không liệt kê link/route private.
- `robots.txt`: thêm dòng `Sitemap:`; `Disallow` các route private (`/admin`, `/dashboard`, `/links`, `/payout`, `/profile`).

## Thay đổi kỹ thuật tóm tắt
- Composer: `+ endroid/qr-code`.
- Migration: `+ expires_at, max_clicks` trên `short_links`.
- Route mới: `links/{shortLink}/qr`, `links/{shortLink}/stats`, `/faq`, `/sitemap.xml`.
- File mới: `config/faq.php`, `app/Support/UserAgentParser.php`, `resources/views/components/seo.blade.php`, view analytics/faq, controller sitemap.
- KHÔNG đụng build pipeline, KHÔNG đụng `.env`.

## Không làm (YAGNI)
- REST API/token (chưa cần).
- Geo-IP theo quốc gia (cần dịch vụ ngoài; chỉ làm device/browser/referrer từ data sẵn có).
- Purge CSS qua build (đã chọn no-build; thay bằng gzip+cache).

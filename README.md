# URL Shortener with Ads

Hệ thống rút gọn link kèm quảng cáo, trả tiền cho người dùng theo lượt view hợp lệ. Xây dựng trên Laravel 12 + Filament.

## Tính năng

- 🔗 Rút gọn URL với custom alias, password protect, guest mode
- 📺 Trang interstitial 5s adf.ly style với 3 ad slot (top + side + bottom)
- 🛡️ Cloudflare Turnstile captcha + IP dedup 24h + chống tự click
- 💰 Tính tiền theo CPM cấu hình được trong admin
- 💸 Yêu cầu rút tiền qua Momo/ZaloPay/PayPal, admin duyệt thủ công
- 🎛️ Admin panel Filament 3: users, links, ads, payouts, settings, blacklist
- 📊 Dashboard user với KPI cards + chart 30 ngày (Chart.js)
- 🌐 Đa ngôn ngữ VN + EN với cookie switcher
- 🔐 Google OAuth login + email/password (Breeze)

## Tech stack

Laravel 12 · PHP 8.3 · MySQL · Filament 3.3 · Blade + Alpine.js + Tailwind CSS · Pest 4 · Vite

## Yêu cầu

- PHP 8.3+
- Composer 2.x
- Node.js 20+
- MySQL 8 (hoặc MariaDB 10.6+, XAMPP cũng OK)

## Cài đặt local

```bash
git clone <repo-url> URL-Shorten
cd URL-Shorten

composer install
npm install

cp .env.example .env
php artisan key:generate

# Tạo DB MySQL
mysql -u root -e "CREATE DATABASE url_shorten CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Migrate + seed demo data (~15s, ~17k clicks)
php artisan migrate:fresh --seed

# Build assets
npm run build

# Start dev server
php artisan serve
```

Truy cập `http://localhost:8000`.

## Tài khoản demo

| Vai trò | Email | Mật khẩu | Truy cập |
|---|---|---|---|
| Admin | admin@demo.com | Admin@123 | `/admin` |
| User | demo@demo.com | Demo@123 | `/dashboard` |

## Cấu trúc thư mục chính

```
app/
├── Filament/Resources/        # Admin CRUD (6 resources)
├── Filament/Widgets/          # 3 dashboard widgets
├── Http/Controllers/          # Home, Link, Redirect, Interstitial, Payout, Dashboard
├── Models/                    # 10 Eloquent models
└── Services/                  # 7 services (business logic)

database/
├── migrations/                # 10 schema migrations
├── factories/                 # Factory definitions
└── seeders/                   # 8 seeders cho demo data

resources/views/
├── home.blade.php             # Landing với form rút gọn
├── dashboard.blade.php        # User KPI + chart
├── links/                     # CRUD link
├── payout/                    # Yêu cầu rút tiền
└── interstitial/              # Trang trung gian adf.ly style
```

## Luồng chính

### Người dùng rút gọn link
1. Vào `/` (guest) hoặc `/links/create` (đã login)
2. Nhập URL gốc → server sinh slug random hoặc dùng alias custom → save vào DB
3. Trả về link rút gọn `domain.com/{slug}`

### Người xem click link
1. GET `/{slug}` → kiểm tra slug, nếu có password thì hiện form unlock
2. Hiển thị trang interstitial: 3 ad slot + countdown 5s + Cloudflare Turnstile
3. Sau 5s + captcha pass → POST `/{slug}/verify` → server xác thực captcha, dedup IP, cộng tiền cho owner (nếu valid view) → trả JSON redirect → JS chuyển đến URL gốc

### User rút tiền
1. Vào `/payout` → xem balance + nhập amount + chọn Momo/Zalo/PayPal + account info
2. Server check balance, trừ tiền (hold), tạo PayoutRequest status=pending
3. Admin vào `/admin` → tab Payouts → action "Mark Paid" (nhập transaction_ref) hoặc "Reject" (nhập note → refund tiền)

## Cấu hình mở rộng (tuỳ chọn)

### Google OAuth
Tạo OAuth client tại [Google Cloud Console](https://console.cloud.google.com/), authorized redirect URI = `http://localhost:8000/auth/google/callback`. Update `.env`:
```
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-secret
```

### Cloudflare Turnstile
Mặc định dùng test keys (always-pass). Để dùng captcha thật: đăng ký free tại [Cloudflare Turnstile](https://www.cloudflare.com/products/turnstile/) và cập nhật `.env`:
```
TURNSTILE_SITE_KEY=0x4AAA...
TURNSTILE_SECRET_KEY=0x4AAA...
```

### Email (cho production)
Đổi `MAIL_MAILER=log` sang `smtp` và config Gmail/cPanel SMTP:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=app-password-16-chars
MAIL_ENCRYPTION=tls
```

## Deploy lên shared hosting

1. Build local: `npm run build`
2. Upload toàn bộ project lên hosting qua FTP/cPanel
3. Đặt document root trỏ vào `public/`
4. Tạo DB MySQL qua cPanel + update `.env`
5. SSH (nếu có): `php artisan migrate --force && php artisan db:seed --force`
6. Setup cron 1 phút/lần: `* * * * * cd /home/user/site && php artisan schedule:run >> /dev/null 2>&1`

## Tài liệu thiết kế

- `docs/superpowers/specs/2026-05-22-url-shorten-ads-design.md` — Design document (kiến trúc, ERD, flow chi tiết)
- `docs/superpowers/plans/2026-05-22-url-shorten-implementation.md` — Implementation plan (40 tasks)

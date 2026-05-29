# URL Shortener with Ad Monetization — Design Document

**Ngày:** 2026-05-22
**Trạng thái:** Draft v1 — chờ user review

---

## 1. Tổng quan

Hệ thống cho phép user rút gọn URL, mỗi link rút gọn khi được click sẽ qua **trang trung gian (interstitial)** hiển thị quảng cáo trong 5 giây cùng captcha xác thực, rồi redirect đến URL gốc. Mỗi 1.000 view hợp lệ user nhận tiền theo rate cố định do admin cấu hình. User có thể request rút tiền qua **Momo/ZaloPay/PayPal**, admin duyệt thủ công. Quảng cáo do **admin nhập tay** (banner/HTML), không có self-service portal cho advertiser.

**Mục tiêu:** Đầy đủ chức năng để demo, code sạch, seeder data phong phú. Không tối ưu scale.

---

## 2. Goals & Non-goals

### In scope
- Auth: email + password (verify), Google OAuth, guest mode
- Rút gọn link: random slug, custom alias, password-protected
- Interstitial 5s + captcha (Cloudflare Turnstile) + 1 IP / link / 24h dedup
- Wallet & Payout: balance, lịch sử giao dịch, request rút tiền (Momo/ZaloPay/PayPal)
- Admin (Filament): user, link, ad campaign, payout, settings, blacklist domain
- Dashboard user cơ bản (click, view hợp lệ, doanh thu, line chart)
- i18n VN + EN, switcher trên header
- Seeder demo data đầy đủ (50 user, 500+ link, 10k+ click, payout history)

### Out of scope (KHÔNG làm)
- Self-service portal cho advertiser (admin nhập tay)
- CPM theo quốc gia (fixed rate phẳng)
- Referral / bonus lifetime
- Custom branded domain (`yourbrand.com/xxx`)
- QR code / Bulk shorten
- Real-time live view counter (WebSocket)
- Tích hợp ad network ngoài (AdSense/PropellerAds)
- Redis / queue worker / Horizon
- GeoIP / VPN detection cao cấp
- Mobile app

---

## 3. Tech Stack

| Layer | Lib/Version |
|---|---|
| Framework | **Laravel 12.x** (latest stable) |
| PHP | 8.3+ |
| Database | MySQL 8.x (tương thích MariaDB 10.6+) |
| Admin panel | **Filament 3.x** (Filament 4 nếu đã ra stable) |
| Auth scaffolding | Laravel Breeze (Blade stack) |
| OAuth | Laravel Socialite (Google provider) |
| Frontend | Blade + Alpine.js 3 + Tailwind CSS 3 (Vite build) |
| Captcha | Cloudflare Turnstile (free, không quota) |
| Email | SMTP (env-based, dev dùng Mailtrap / Mailpit) |
| i18n | Laravel localization built-in (`__()`, `@lang()`) |
| Test | Pest 3 (Laravel 12 default) |
| Code style | Laravel Pint |
| Queue driver | `sync` (không cần job background) |
| Cache driver | `database` (không cần Redis) |

---

## 4. Module Decomposition

6 module chính (chia theo feature, không tách package):

1. **Auth** — Breeze + Socialite Google, email verify, password reset
2. **Link Management** — CRUD link, slug generator, custom alias, password protect
3. **Redirect & Ad Serving** — `/{slug}` → interstitial → ad chọn theo weight → captcha verify → redirect
4. **Wallet & Payout** — balance, transaction log, payout request flow
5. **Admin Panel** — Filament Resources cho user/link/ads/payout/settings/blacklist
6. **Analytics** — Dashboard user (line chart 30 ngày), dashboard admin (widget thống kê)

---

## 5. Cấu trúc thư mục (Laravel 12 standard MVC)

```
app/
├── Filament/
│   ├── Resources/
│   │   ├── UserResource.php
│   │   ├── ShortLinkResource.php
│   │   ├── AdCampaignResource.php
│   │   ├── PayoutRequestResource.php
│   │   ├── SettingResource.php
│   │   └── BlacklistDomainResource.php
│   ├── Pages/Dashboard.php
│   └── Widgets/
│       ├── StatsOverview.php
│       ├── ClicksChart.php
│       └── PendingPayouts.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                   # Breeze sinh + GoogleController custom
│   │   ├── HomeController.php
│   │   ├── ShortLinkController.php
│   │   ├── DashboardController.php
│   │   ├── RedirectController.php
│   │   ├── InterstitialController.php
│   │   ├── PayoutController.php
│   │   ├── ProfileController.php
│   │   └── LocaleController.php
│   ├── Requests/
│   │   ├── StoreShortLinkRequest.php
│   │   ├── UpdateShortLinkRequest.php
│   │   ├── UnlockLinkRequest.php
│   │   └── StorePayoutRequest.php
│   └── Middleware/
│       └── SetLocale.php
├── Models/
│   ├── User.php
│   ├── ShortLink.php
│   ├── Click.php
│   ├── AdCampaign.php
│   ├── AdImpression.php
│   ├── PayoutRequest.php
│   ├── WalletTransaction.php
│   ├── Setting.php
│   ├── BlacklistDomain.php
│   └── IpViewLog.php
├── Services/
│   ├── ShortLinkService.php
│   ├── ClickTrackingService.php
│   ├── AdServingService.php
│   ├── PayoutService.php
│   ├── WalletService.php
│   ├── CaptchaService.php          # Turnstile verify
│   └── SettingService.php          # get/set cached config
└── Providers/
    └── Filament/AdminPanelProvider.php

database/
├── migrations/
└── seeders/
    ├── DatabaseSeeder.php          # gọi tất cả seeder
    ├── SettingSeeder.php
    ├── UserSeeder.php
    ├── ShortLinkSeeder.php
    ├── ClickSeeder.php
    ├── AdCampaignSeeder.php
    ├── AdImpressionSeeder.php
    ├── PayoutRequestSeeder.php
    ├── WalletTransactionSeeder.php
    └── BlacklistDomainSeeder.php

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── home.blade.php
│   ├── auth/                       # Breeze
│   ├── dashboard/
│   │   ├── index.blade.php
│   │   ├── links.blade.php
│   │   └── payout.blade.php
│   ├── interstitial/
│   │   ├── countdown.blade.php
│   │   ├── password.blade.php
│   │   └── blocked.blade.php
│   └── profile/
├── lang/
│   ├── vi.json                     # JSON key-value
│   └── en.json
├── css/app.css                     # Tailwind
└── js/app.js
```

---

## 6. Routes Map

```php
// routes/web.php

// ===== PUBLIC =====
Route::get('/',                    [HomeController::class, 'index'])->name('home');
Route::post('/shorten',            [HomeController::class, 'shortenGuest'])->name('shorten.guest');
Route::get('/locale/{locale}',     [LocaleController::class, 'switch'])->name('locale.switch');

// Auth (Breeze): /login, /register, /forgot-password, /reset-password, /verify-email
require __DIR__.'/auth.php';

// OAuth
Route::get('/auth/google',          [Auth\GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [Auth\GoogleController::class, 'callback']);

// ===== AUTHENTICATED USER =====
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/dashboard',       [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('links',        ShortLinkController::class)->except('show');
    Route::get('/payout',          [PayoutController::class, 'index'])->name('payout.index');
    Route::post('/payout',         [PayoutController::class, 'store'])->name('payout.store');
    Route::get('/profile',         [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',       [ProfileController::class, 'update'])->name('profile.update');
});

// ===== REDIRECT FLOW (public, no auth required) =====
Route::get('/{slug}',              [RedirectController::class, 'show'])
    ->where('slug','[A-Za-z0-9_-]+')->name('link.show');
Route::post('/{slug}/unlock',      [RedirectController::class, 'unlock'])->name('link.unlock');
Route::post('/{slug}/verify',      [InterstitialController::class, 'verify'])->name('link.verify');

// ===== ADMIN: Filament tự gen /admin/* =====
// Quản lý qua AdminPanelProvider, không declare route thủ công
```

---

## 7. Database Schema (ERD)

### 7.1 Tổng quan ERD

```
┌──────────┐         ┌────────────────┐         ┌──────────┐
│  users   │──1:N───▶│  short_links   │◀──1:N───│  clicks  │
└──────────┘         └────────────────┘         └──────────┘
     │                       │                        │
     │                       │                        │
     │ 1:N                   │ 1:N                    │ N:1
     ▼                       ▼                        ▼
┌──────────────┐    ┌──────────────────┐    ┌──────────────┐
│payout_requests│   │  ip_view_logs    │    │ad_impressions│
└──────────────┘    └──────────────────┘    └──────────────┘
     │                                              │
     │                                              │ N:1
     ▼                                              ▼
┌─────────────────────┐                    ┌──────────────┐
│ wallet_transactions │                    │ ad_campaigns │
└─────────────────────┘                    └──────────────┘

settings, blacklist_domains: standalone (no FK)
```

### 7.2 Schema chi tiết

#### `users`
```
id              BIGINT PK
name            VARCHAR(255)
email           VARCHAR(255) UNIQUE
email_verified_at TIMESTAMP NULL
password        VARCHAR(255) NULL          -- NULL nếu chỉ login Google
google_id       VARCHAR(255) NULL UNIQUE
avatar          VARCHAR(500) NULL
balance         BIGINT DEFAULT 0           -- VND (không decimal)
total_earned    BIGINT DEFAULT 0
status          ENUM('active','banned') DEFAULT 'active'
payout_method   ENUM('momo','zalo','paypal') NULL
payout_account  VARCHAR(255) NULL          -- phone / email
preferred_locale VARCHAR(5) DEFAULT 'vi'
is_admin        BOOLEAN DEFAULT FALSE      -- access Filament panel
remember_token  VARCHAR(100) NULL
created_at, updated_at
```

#### `short_links`
```
id              BIGINT PK
user_id         BIGINT NULL FK→users.id ON DELETE SET NULL  -- NULL = guest
slug            VARCHAR(32) UNIQUE
original_url    TEXT
title           VARCHAR(255) NULL
password        VARCHAR(255) NULL          -- bcrypt
status          ENUM('active','disabled','blocked') DEFAULT 'active'
total_clicks    INTEGER UNSIGNED DEFAULT 0
valid_views     INTEGER UNSIGNED DEFAULT 0
total_earned    BIGINT DEFAULT 0           -- denormalized cho dashboard
created_at, updated_at

INDEX (user_id, status)
INDEX (slug)  -- unique
```

#### `clicks`
```
id              BIGINT PK
short_link_id   BIGINT FK→short_links.id ON DELETE CASCADE
ip_address      VARCHAR(45)                -- IPv6 safe
user_agent      VARCHAR(500) NULL
referer         VARCHAR(500) NULL
is_valid        BOOLEAN DEFAULT FALSE
earnings        BIGINT DEFAULT 0           -- VND user nhận từ click này
created_at      TIMESTAMP

INDEX (short_link_id, created_at)
INDEX (created_at)                          -- cho admin dashboard
```

#### `ad_campaigns`
```
id              BIGINT PK
name            VARCHAR(255)
placement       ENUM('top','side','bottom') -- slot trên trang interstitial
type            ENUM('banner_image','html','iframe')
content         TEXT                       -- image URL / HTML / iframe URL
target_url      VARCHAR(500) NULL          -- nếu banner click ra link
weight          INTEGER UNSIGNED DEFAULT 1 -- chọn ngẫu nhiên theo trọng số trong cùng placement
status          ENUM('active','paused') DEFAULT 'active'
start_at        TIMESTAMP NULL
end_at          TIMESTAMP NULL
impressions     INTEGER UNSIGNED DEFAULT 0
clicks_count    INTEGER UNSIGNED DEFAULT 0
created_at, updated_at

INDEX (status, placement, weight)
```

**Kích thước recommended cho mỗi placement (UI guide):**
- `top` — banner 728×90 (leaderboard) hoặc responsive
- `side` — 300×250 (medium rectangle), hiển thị bên phải khi desktop, ẩn khi mobile
- `bottom` — 728×90 hoặc 320×100 mobile

#### `ad_impressions`
```
id              BIGINT PK
ad_campaign_id  BIGINT FK→ad_campaigns.id ON DELETE CASCADE
short_link_id   BIGINT FK→short_links.id ON DELETE CASCADE
click_id        BIGINT FK→clicks.id NULL ON DELETE SET NULL
ip_address      VARCHAR(45)
was_clicked     BOOLEAN DEFAULT FALSE      -- user click vào banner
created_at      TIMESTAMP

INDEX (ad_campaign_id, created_at)
```

#### `payout_requests`
```
id              BIGINT PK
user_id         BIGINT FK→users.id ON DELETE CASCADE
amount          BIGINT                     -- VND
method          ENUM('momo','zalo','paypal')
account_info    VARCHAR(255)               -- snapshot tại thời điểm request
status          ENUM('pending','approved','rejected','paid') DEFAULT 'pending'
admin_note      TEXT NULL
processed_by    BIGINT FK→users.id NULL
processed_at    TIMESTAMP NULL
transaction_ref VARCHAR(255) NULL          -- mã giao dịch admin nhập sau khi chuyển
created_at, updated_at

INDEX (user_id, status)
INDEX (status, created_at)
```

#### `wallet_transactions`
```
id              BIGINT PK
user_id         BIGINT FK→users.id ON DELETE CASCADE
type            ENUM('credit','payout_hold','payout_release','payout_reject','admin_adjust')
amount          BIGINT                     -- có thể âm
balance_after   BIGINT
reference_type  VARCHAR(50) NULL           -- 'click', 'payout_request'
reference_id    BIGINT NULL                -- polymorphic-style nhưng không cần Eloquent morph
description     VARCHAR(500) NULL
created_at

INDEX (user_id, created_at)
```

#### `settings`
```
id              BIGINT PK
key             VARCHAR(100) UNIQUE
value           TEXT
type            ENUM('string','integer','boolean','json') DEFAULT 'string'
description     VARCHAR(500) NULL
updated_at
```

**Default settings seed:**
- `rate_per_1000_views` = `5000` (VND/1000 view)
- `min_payout_vnd` = `100000`
- `min_payout_usd_paypal` = `4`
- `interstitial_seconds` = `5`
- `ip_dedup_hours` = `24`
- `turnstile_site_key` = (env)
- `turnstile_secret_key` = (env)
- `paypal_to_vnd_rate` = `25000` (1 USD = 25000 VND, để hiển thị balance USD cho PayPal)

#### `blacklist_domains`
```
id              BIGINT PK
domain          VARCHAR(255) UNIQUE
reason          VARCHAR(500) NULL
created_by      BIGINT FK→users.id NULL
created_at, updated_at
```

#### `ip_view_logs`
```
short_link_id   BIGINT FK→short_links.id ON DELETE CASCADE
ip_address      VARCHAR(45)
viewed_at       TIMESTAMP

PRIMARY KEY (short_link_id, ip_address, viewed_at)
INDEX (viewed_at)  -- cho cleanup
```

Cleanup task: chạy mỗi ngày, `DELETE FROM ip_view_logs WHERE viewed_at < NOW() - INTERVAL 30 DAY`.

---

## 8. Core Flows

### 8.1 Shorten link flow

```
User → POST /links (auth) or POST /shorten (guest)
       payload: { original_url, custom_alias?, password? }
       │
       ▼
StoreShortLinkRequest validates:
  - original_url: url, http/https only, max 2048
  - custom_alias: nullable, 3-32 chars, regex ^[A-Za-z0-9_-]+$, unique slugs
  - password: nullable, min 4 chars
       │
       ▼
ShortLinkController@store:
  1. Check original_url host against BlacklistDomain → reject if matched
  2. ShortLinkService::create(user_id?, original_url, custom_alias?, password?)
     - Nếu custom_alias: dùng làm slug, check unique
     - Else: generate random slug 6 chars (base62), retry tới khi unique
     - Nếu password: bcrypt hash
     - INSERT short_link, status='active'
  3. Optional: fetch og:title via simple HTTP HEAD (skip nếu chậm)
  4. Redirect về /links với flash success kèm full short URL
```

### 8.2 Redirect & Ad serving flow (CORE)

```
Visitor clicks: https://site.com/abc123
       │
       ▼
GET /{slug} → RedirectController@show
  1. Find short_link by slug
     - 404 nếu không tồn tại
     - View 'blocked' nếu status != 'active'
  2. Nếu có password:
     - Render 'interstitial.password' với form POST
     - User nhập → POST /{slug}/unlock → check Hash::check
     - Nếu đúng: set session flag `unlocked:{slug}` rồi redirect về GET /{slug}
  3. (Đã unlock hoặc không có password):
     - Pick 3 ads (multi-slot): AdServingService::pickAdsForInterstitial()
       For each placement IN ('top','side','bottom'):
         SELECT * FROM ad_campaigns
         WHERE status='active' AND placement=:p
           AND (start_at IS NULL OR start_at <= NOW())
           AND (end_at IS NULL OR end_at >= NOW())
         ORDER BY -LOG(1-RAND())/weight LIMIT 1   -- weighted random per slot
       Trả về map { top: AdCampaign|null, side: ..., bottom: ... }
       (Nếu 1 slot không có ad active → để trống slot đó, không break flow)
     - Generate impression token (signed UUID) lưu vào session, bind đến 3 ad ids
     - Log 1-3 AdImpression records (1 per slot có ad)
     - Render 'interstitial.countdown' layout adf.ly style:
       * Top slot: banner 728×90 ngang trên
       * Side slot: 300×250 cố định bên phải (sticky), ẩn khi mobile
       * Center: countdown timer + Cloudflare Turnstile + Skip button
       * Bottom slot: banner 728×90 dưới
       * Countdown JS 5s, button Skip disabled tới khi countdown=0 + captcha pass
       │
       ▼
User chờ 5s + captcha pass → click Skip → POST /{slug}/verify
       │
       ▼
InterstitialController@verify:
  Input: { slug, impression_token, cf-turnstile-response }
  1. Verify Turnstile: CaptchaService::verify(token, request_ip)
     - HTTP POST https://challenges.cloudflare.com/turnstile/v0/siteverify
     - Nếu fail: return JSON { redirect_url: original_url, valid: false }
       (vẫn cho qua, nhưng không tính view hợp lệ)
  2. Verify impression_token match session
  3. Tính is_valid:
     - Captcha pass
     - Không tồn tại (short_link_id, ip) trong ip_view_logs có viewed_at > NOW()-24h
     - Logged-in user ≠ short_link.user_id (chống tự click)
  4. INSERT click record (is_valid, earnings)
     - Nếu is_valid: earnings = setting.rate_per_1000_views / 1000
  5. Nếu is_valid AND link.user_id IS NOT NULL:
     - DB::transaction:
       - UPDATE users SET balance = balance + earnings, total_earned = total_earned + earnings
       - INSERT wallet_transactions (credit, +earnings, ref_type='click', ref_id=click.id)
     - INSERT ip_view_logs
     - UPDATE short_links SET valid_views++, total_earned += earnings
  6. UPDATE short_links SET total_clicks++
  7. UPDATE ad_impressions SET click_id = click.id WHERE impression_token = ... (cập nhật tất cả 1-3 impression cùng token)
  8. UPDATE ad_campaigns SET impressions++ cho mỗi ad đã hiển thị
  9. Return JSON { redirect_url: original_url, valid: is_valid }
       │
       ▼
JS bên client: window.location.href = response.redirect_url
```

**Edge cases:**
- Guest link (user_id=null): không credit ai, vẫn ghi click
- Captcha fail: vẫn redirect nhưng không tính view
- Link expired/disabled: hiển thị trang 'blocked' giải thích
- IP duplicate trong 24h: ghi click với is_valid=false
- User chính chủ click link mình: ghi với is_valid=false

### 8.3 Payout flow

```
User dashboard → /payout
  - Hiển thị: balance, total earned, list payout history với status
  - Form: amount + method (momo/zalo/paypal) + account_info

User → POST /payout
       │
       ▼
StorePayoutRequest validates:
  - amount: integer, >= setting.min_payout_vnd (hoặc min_payout_usd_paypal * rate)
  - amount <= user.balance
  - method: in [momo, zalo, paypal]
  - account_info: regex phù hợp (phone VN 10 số / email PayPal)
       │
       ▼
PayoutController@store → PayoutService::createRequest():
  DB::transaction:
    1. Lock user row (SELECT ... FOR UPDATE)
    2. Check balance >= amount lần nữa
    3. UPDATE users SET balance = balance - amount
    4. INSERT payout_requests (status='pending', ...)
    5. INSERT wallet_transactions (type='payout_hold', amount=-amount, ref=payout.id)
  Send email user "Yêu cầu đang chờ duyệt"

Admin xem trong Filament → PayoutRequestResource:
  - Tab Pending / Approved / Rejected / Paid
  - Action "Approve" (chưa chuyển tiền): status='approved' (intermediate)
  - Action "Mark as Paid": modal nhập transaction_ref → status='paid'
    - INSERT wallet_transactions (type='payout_release', amount=0, description)
    - Send email user "Đã chuyển X VND qua Y"
  - Action "Reject": modal nhập admin_note
    - DB::transaction:
      - UPDATE users SET balance = balance + amount (refund)
      - UPDATE payout_requests SET status='rejected'
      - INSERT wallet_transactions (type='payout_reject', amount=+amount)
    - Send email user "Bị từ chối: <reason>"
```

---

## 9. Admin Panel (Filament 3)

### 9.1 Authentication
- Filament chỉ cho phép user có `is_admin = true` truy cập `/admin`
- Implement qua `User::canAccessPanel()` method
- Seed 1 admin user mặc định: `admin@demo.com` / `Admin@123`

### 9.2 Resources

| Resource | Features |
|---|---|
| **UserResource** | List với search, filter by status. View balance + total_earned. Actions: Ban/Unban, Toggle admin, Reset password (sinh random), View transactions (relation manager). |
| **ShortLinkResource** | List với filter user/status. Action: Disable, View clicks (relation manager). Không cho phép admin edit URL gốc (chỉ disable). |
| **AdCampaignResource** | Full CRUD. Form fields: name, **placement (top/side/bottom)**, type, content (rich textarea / image upload), target_url, weight, status, start_at, end_at. Filter list theo placement. Stats: impressions, clicks_count, CTR (computed). |
| **PayoutRequestResource** | Tabs theo status. Actions Approve/Mark Paid/Reject với modal note. Export CSV/Excel pending. |
| **SettingResource** | Custom page (key-value form thay vì CRUD). Edit từng setting với input type tương ứng. |
| **BlacklistDomainResource** | Simple CRUD. |

### 9.3 Dashboard widgets
- **StatsOverview** — Total users, links, clicks today, valid views today, revenue paid this month
- **ClicksChart** — Line chart 30 ngày: total clicks, valid clicks
- **PendingPayouts** — Table list 10 payout pending mới nhất, click vào để duyệt nhanh

---

## 10. Anti-fraud Mechanisms

| Cơ chế | Chi tiết |
|---|---|
| **Cloudflare Turnstile** | Free, không quota. Embed widget JS public, verify server-side với secret_key. Khi captcha fail → click vẫn ghi nhưng `is_valid=false`. |
| **IP dedup 24h** | Bảng `ip_view_logs(short_link_id, ip_address, viewed_at)`. Check trước khi cộng tiền. Cleanup mỗi ngày xóa > 30 ngày. |
| **Self-click block** | Nếu `Auth::user()->id == short_link.user_id` → `is_valid=false`, không cộng tiền. |
| **Blacklist domain** | Admin nhập danh sách domain cấm rút gọn. Check tại `ShortLinkService::create()`. |
| **Rate limit per IP** | Laravel `RateLimiter` cho route `/{slug}/verify`: 60/phút theo IP. Chống spam. |
| **Banned user** | User bị ban không login được, link bị mark blocked. |

**Không làm:** VPN/proxy detection (đắt, không hợp lý ở giai đoạn này); user-agent heuristic (false positive cao).

---

## 11. i18n (VN + EN)

- Sử dụng JSON-based localization: `resources/lang/vi.json`, `resources/lang/en.json`
- Mỗi UI string dùng key tiếng Anh trong code: `{{ __('Shorten your URL') }}` → tra lookup VN/EN
- Default locale: `vi`
- Locale switcher trên header: 2 link `/locale/vi` `/locale/en` → set cookie + redirect back
- Middleware `SetLocale`: đọc cookie/user.preferred_locale → `App::setLocale()`
- Tất cả email template, admin label, validation messages đều dịch

---

## 12. Configuration & Settings

### `.env` keys

```
APP_NAME="URL Shortener"
APP_ENV=local
APP_KEY=
APP_URL=http://localhost:8000
APP_LOCALE=vi
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shorten
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=database
QUEUE_CONNECTION=sync
SESSION_DRIVER=database

# Email: dev dùng log driver hoặc Mailpit, prod dùng SMTP của shared host / Gmail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com              # hoặc smtp host từ cPanel
MAIL_PORT=587
MAIL_USERNAME=your-account@gmail.com
MAIL_PASSWORD=                         # App Password 16 ký tự từ Google Account
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@urlshorten.test
MAIL_FROM_NAME="${APP_NAME}"

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

TURNSTILE_SITE_KEY=        # 1x00000000000000000000AA (test key)
TURNSTILE_SECRET_KEY=      # 1x0000000000000000000000000000000AA
```

### Settings (DB, edit qua admin)

| Key | Default | Type | Mục đích |
|---|---|---|---|
| `rate_per_1000_views` | 5000 | int | VND/1000 view hợp lệ |
| `min_payout_vnd` | 100000 | int | Ngưỡng tối thiểu Momo/Zalo |
| `min_payout_usd_paypal` | 4 | int | Ngưỡng PayPal (USD) |
| `interstitial_seconds` | 5 | int | Countdown giây |
| `ip_dedup_hours` | 24 | int | Dedup IP trong N giờ |
| `paypal_to_vnd_rate` | 25000 | int | Tỷ giá quy đổi |

---

## 13. Seeder & Demo Data Plan

Mục tiêu: chạy `php artisan migrate:fresh --seed` xong là demo được ngay.

### `DatabaseSeeder` gọi theo thứ tự:
1. `SettingSeeder` — 8 settings mặc định
2. `BlacklistDomainSeeder` — 5-10 domain mẫu (spam.test, phishing.example, ...)
3. `UserSeeder`:
   - 1 admin: `admin@demo.com` / `Admin@123` (is_admin=true)
   - 1 demo user: `demo@demo.com` / `Demo@123` (balance cao, nhiều link)
   - 48 user random qua factory (mix payout method, balance random 0-500k)
4. `AdCampaignSeeder` — 12-15 campaigns chia đều 3 placement (4-5 top, 4-5 side, 4-5 bottom). Banner image từ `picsum.photos/728/90` (top/bottom) và `picsum.photos/300/250` (side), vài HTML demo
5. `ShortLinkSeeder`:
   - 200-500 link random gán user
   - Mix: 70% random slug, 30% custom alias, 10% có password
   - Original URL: faker URL + một số URL thực tế (youtube, github, ...)
6. `ClickSeeder`:
   - ~15,000 click trải dài 90 ngày qua (`created_at` backdate)
   - 70% is_valid, 30% invalid
   - Phân bố không đều theo ngày (ngày cuối tuần nhiều hơn)
   - Phân bố không đều theo link (top 10% link chiếm 50% click — Pareto)
   - Tự động cộng dồn `short_links.total_clicks`, `valid_views`, `total_earned`
7. `AdImpressionSeeder` — mỗi click sinh 1-3 impression (pick 1 ad/placement), was_clicked random ~5% mỗi impression
8. `WalletTransactionSeeder` — sinh từ ClickSeeder (credit) + PayoutSeeder
9. `PayoutRequestSeeder` — 25-30 request:
   - 40% pending, 30% paid, 20% rejected, 10% approved
   - amount random 50k-500k VND

Sau seed: `php artisan tinker` test một link bất kỳ, kiểm tra số liệu dashboard nhất quán.

---

## 14. Testing Strategy

Minimal nhưng có để show:

- **Pest 3** (default Laravel 12)
- Feature tests (~10 test cases):
  - `tests/Feature/Auth/RegisterTest.php` — register + email verify
  - `tests/Feature/Auth/GoogleLoginTest.php` — mock Socialite
  - `tests/Feature/ShortenLinkTest.php` — store + custom alias + duplicate + blacklist
  - `tests/Feature/RedirectTest.php` — guest click, password-protected, blocked link
  - `tests/Feature/InterstitialVerifyTest.php` — captcha pass/fail, IP dedup, self-click, earnings credit
  - `tests/Feature/PayoutTest.php` — request thành công, request vượt balance
- Unit tests (~5):
  - `tests/Unit/Services/ShortLinkServiceTest.php` — generateUniqueSlug
  - `tests/Unit/Services/WalletServiceTest.php` — credit/debit atomic
  - `tests/Unit/Services/AdServingServiceTest.php` — weighted random distribution
- Chạy: `php artisan test` hoặc `./vendor/bin/pest`

Không cần code coverage 80%+. Mục tiêu: chứng minh phần critical (payout, earnings) đúng.

---

## 15. Deployment to Shared Hosting

1. Build assets local: `npm run build` → upload `public/build/`
2. Upload toàn bộ Laravel project lên hosting (qua FTP/cPanel)
3. Đặt document root trỏ vào `public/` (cPanel "Set Document Root")
4. Tạo MySQL DB qua cPanel → cập nhật `.env`
5. SSH (nếu có): `php artisan migrate --force && php artisan db:seed --force`
   - Nếu không có SSH: dùng [package `migrations-ui`](https://github.com/DaveJamesMiller/laravel-migrations-ui) hoặc dán SQL qua phpMyAdmin
6. Cron 1 phút/lần: `* * * * * cd /home/user/site && php artisan schedule:run >> /dev/null 2>&1`
7. Schedule chạy: `ip_view_logs` cleanup hàng ngày (define trong `routes/console.php`)

---

## 16. Out of Scope / Future Work

Khi đề tài mở rộng (hoặc khi nâng cấp thành sản phẩm thật):

- Self-service advertiser portal (deposit, campaign creation, billing)
- CPM theo quốc gia (lookup GeoIP qua MaxMind GeoLite2 free)
- Referral system (link mã giới thiệu, % lifetime)
- Custom branded domain (1 user 1 subdomain)
- Bulk shorten + API key cho dev integration
- Real-time live counter (WebSocket via Reverb)
- Ad network fallback (PropellerAds / Adsterra)
- Advanced anti-fraud (machine learning click pattern, MaxMind minFraud)
- Multi-tenant white-label
- Mobile app (Flutter / React Native)
- Migrate sang VPS + Redis + Horizon khi traffic > 10k click/ngày

---

## 17. Resolved Decisions (đã chốt từ brainstorming)

- **GeoIP / country_code:** KHÔNG seed. Bỏ cột khỏi schema để tránh rác (sau này thêm cũng được).
- **Layout interstitial:** Multi-slot kiểu adf.ly với 3 placement: `top` (728×90), `side` (300×250, sticky right desktop / ẩn mobile), `bottom` (728×90). Mỗi placement pick 1 ad weighted random độc lập.
- **SMTP email:** Dùng SMTP của shared hosting (cPanel) hoặc Gmail SMTP (smtp.gmail.com:587 TLS với App Password). Local dev có thể dùng `MAIL_MAILER=log` để xem qua `storage/logs/laravel.log`.
- **Domain demo:** Chỉ chạy local `http://localhost:8000`. Google OAuth callback cấu hình `http://localhost:8000/auth/google/callback` trong Google Cloud Console.

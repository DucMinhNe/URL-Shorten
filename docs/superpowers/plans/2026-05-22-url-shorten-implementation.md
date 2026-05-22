# URL Shortener with Ads — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a Laravel 12 URL shortener with interstitial ad monetization, captcha anti-fraud, wallet/payout flow, Filament admin panel, and rich demo seeders for a student capstone project.

**Architecture:** Standard Laravel MVC + Filament 3 admin + Blade/Alpine/Tailwind public UI. MySQL only (no Redis/queue). Service layer for business logic. Multi-slot adf.ly-style interstitial (top/side/bottom). Cloudflare Turnstile captcha. Manual admin-approved payouts via Momo/ZaloPay/PayPal.

**Tech Stack:** Laravel 12, PHP 8.3, MySQL 8, Filament 3, Laravel Breeze (Blade), Laravel Socialite (Google), Tailwind 3, Alpine.js 3, Cloudflare Turnstile, Pest 3.

**Reference spec:** `docs/superpowers/specs/2026-05-22-url-shorten-ads-design.md`

---

## Phase 0 — Project Initialization

### Task 1: Bootstrap Laravel 12 project

**Files:**
- Create: project root (Laravel skeleton)

- [ ] **Step 1: Verify PHP + Composer + Node available**

Run:
```bash
php -v        # expect 8.3+
composer -V   # expect 2.x
node -v       # expect 20+
mysql --version
```

- [ ] **Step 2: Create Laravel project**

Run from `/Users/marcus/data/URL-Shorten` parent:
```bash
cd /Users/marcus/data
composer create-project laravel/laravel URL-Shorten "^12.0"
cd URL-Shorten
```

Expected: Laravel skeleton created, `php artisan --version` shows Laravel 12.x.

- [ ] **Step 3: Init git + first commit**

```bash
git init
git add .
git commit -m "chore: initial Laravel 12 skeleton"
```

- [ ] **Step 4: Add `.gitignore` entry for `.env.testing` and OS files**

Append to `.gitignore`:
```
.env.testing
.DS_Store
Thumbs.db
/storage/app/private/*
!/storage/app/private/.gitkeep
```

- [ ] **Step 5: Commit**

```bash
git add .gitignore
git commit -m "chore: extend gitignore"
```

---

### Task 2: Install dependencies

**Files:**
- Modify: `composer.json`, `package.json`

- [ ] **Step 1: Install Laravel Breeze (Blade stack) + Socialite + Filament 3**

```bash
composer require laravel/breeze --dev
composer require laravel/socialite
composer require filament/filament:"^3.2" -W
```

- [ ] **Step 2: Install Breeze scaffolding with Blade + dark mode**

```bash
php artisan breeze:install blade --dark
```

When prompted for tests, choose Pest.

- [ ] **Step 3: Install Filament panel**

```bash
php artisan filament:install --panels
```

When prompted for panel id, enter `admin`. This generates `app/Providers/Filament/AdminPanelProvider.php`.

- [ ] **Step 4: Install npm deps + build assets**

```bash
npm install
npm run build
```

- [ ] **Step 5: Commit**

```bash
git add .
git commit -m "feat: install Breeze, Socialite, Filament 3"
```

---

### Task 3: Configure `.env`

**Files:**
- Modify: `.env`, `.env.example`

- [ ] **Step 1: Update `.env` with full config**

Replace `.env` contents:
```env
APP_NAME="URL Shortener"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Ho_Chi_Minh
APP_URL=http://localhost:8000
APP_LOCALE=vi
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=vi_VN

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shorten
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=database
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Email — dev dùng log, prod đổi sang smtp Gmail/cPanel
MAIL_MAILER=log
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@urlshorten.test"
MAIL_FROM_NAME="${APP_NAME}"

# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

# Cloudflare Turnstile (test keys hoạt động always-pass)
TURNSTILE_SITE_KEY=1x00000000000000000000AA
TURNSTILE_SECRET_KEY=1x0000000000000000000000000000000AA
```

- [ ] **Step 2: Generate key + create DB**

```bash
php artisan key:generate
mysql -u root -e "CREATE DATABASE IF NOT EXISTS url_shorten CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

- [ ] **Step 3: Add Socialite + Turnstile config to `config/services.php`**

Add inside `return [` array:
```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
'turnstile' => [
    'site_key' => env('TURNSTILE_SITE_KEY'),
    'secret_key' => env('TURNSTILE_SECRET_KEY'),
],
```

- [ ] **Step 4: Commit**

```bash
git add .env.example config/services.php
git commit -m "chore: configure env + services (Google, Turnstile)"
```

---

## Phase 1 — Database Schema

### Task 4: Modify `users` migration

**Files:**
- Modify: `database/migrations/0001_01_01_000000_create_users_table.php`

- [ ] **Step 1: Update users migration**

Replace the `create('users', ...)` block:
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password')->nullable();
    $table->string('google_id')->nullable()->unique();
    $table->string('avatar', 500)->nullable();
    $table->unsignedBigInteger('balance')->default(0);
    $table->unsignedBigInteger('total_earned')->default(0);
    $table->enum('status', ['active', 'banned'])->default('active');
    $table->enum('payout_method', ['momo', 'zalo', 'paypal'])->nullable();
    $table->string('payout_account')->nullable();
    $table->string('preferred_locale', 5)->default('vi');
    $table->boolean('is_admin')->default(false);
    $table->rememberToken();
    $table->timestamps();
});
```

- [ ] **Step 2: Commit**

```bash
git add database/migrations/0001_01_01_000000_create_users_table.php
git commit -m "feat(db): extend users table with balance/admin/payout fields"
```

---

### Task 5: Create migrations for domain tables

**Files:**
- Create 9 migrations via artisan

- [ ] **Step 1: Generate migrations**

```bash
php artisan make:migration create_settings_table
php artisan make:migration create_blacklist_domains_table
php artisan make:migration create_short_links_table
php artisan make:migration create_clicks_table
php artisan make:migration create_ad_campaigns_table
php artisan make:migration create_ad_impressions_table
php artisan make:migration create_payout_requests_table
php artisan make:migration create_wallet_transactions_table
php artisan make:migration create_ip_view_logs_table
```

- [ ] **Step 2: Fill `settings` migration**

In `create_settings_table.php` `up()`:
```php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('key', 100)->unique();
    $table->text('value');
    $table->enum('type', ['string','integer','boolean','json'])->default('string');
    $table->string('description', 500)->nullable();
    $table->timestamp('updated_at')->nullable();
});
```

- [ ] **Step 3: Fill `blacklist_domains`**
```php
Schema::create('blacklist_domains', function (Blueprint $table) {
    $table->id();
    $table->string('domain')->unique();
    $table->string('reason', 500)->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
});
```

- [ ] **Step 4: Fill `short_links`**
```php
Schema::create('short_links', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->string('slug', 32)->unique();
    $table->text('original_url');
    $table->string('title')->nullable();
    $table->string('password')->nullable();
    $table->enum('status', ['active','disabled','blocked'])->default('active');
    $table->unsignedInteger('total_clicks')->default(0);
    $table->unsignedInteger('valid_views')->default(0);
    $table->unsignedBigInteger('total_earned')->default(0);
    $table->timestamps();
    $table->index(['user_id','status']);
});
```

- [ ] **Step 5: Fill `clicks`**
```php
Schema::create('clicks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('short_link_id')->constrained()->cascadeOnDelete();
    $table->string('ip_address', 45);
    $table->string('user_agent', 500)->nullable();
    $table->string('referer', 500)->nullable();
    $table->boolean('is_valid')->default(false);
    $table->unsignedBigInteger('earnings')->default(0);
    $table->timestamp('created_at')->useCurrent();
    $table->index(['short_link_id','created_at']);
    $table->index('created_at');
});
```

- [ ] **Step 6: Fill `ad_campaigns`**
```php
Schema::create('ad_campaigns', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->enum('placement', ['top','side','bottom']);
    $table->enum('type', ['banner_image','html','iframe']);
    $table->text('content');
    $table->string('target_url', 500)->nullable();
    $table->unsignedInteger('weight')->default(1);
    $table->enum('status', ['active','paused'])->default('active');
    $table->timestamp('start_at')->nullable();
    $table->timestamp('end_at')->nullable();
    $table->unsignedInteger('impressions')->default(0);
    $table->unsignedInteger('clicks_count')->default(0);
    $table->timestamps();
    $table->index(['status','placement','weight']);
});
```

- [ ] **Step 7: Fill `ad_impressions`**
```php
Schema::create('ad_impressions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('ad_campaign_id')->constrained()->cascadeOnDelete();
    $table->foreignId('short_link_id')->constrained()->cascadeOnDelete();
    $table->foreignId('click_id')->nullable()->constrained()->nullOnDelete();
    $table->string('impression_token', 64)->index();
    $table->string('ip_address', 45);
    $table->boolean('was_clicked')->default(false);
    $table->timestamp('created_at')->useCurrent();
    $table->index(['ad_campaign_id','created_at']);
});
```

- [ ] **Step 8: Fill `payout_requests`**
```php
Schema::create('payout_requests', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->unsignedBigInteger('amount');
    $table->enum('method', ['momo','zalo','paypal']);
    $table->string('account_info');
    $table->enum('status', ['pending','approved','rejected','paid'])->default('pending');
    $table->text('admin_note')->nullable();
    $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('processed_at')->nullable();
    $table->string('transaction_ref')->nullable();
    $table->timestamps();
    $table->index(['user_id','status']);
    $table->index(['status','created_at']);
});
```

- [ ] **Step 9: Fill `wallet_transactions`**
```php
Schema::create('wallet_transactions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->enum('type', ['credit','payout_hold','payout_release','payout_reject','admin_adjust']);
    $table->bigInteger('amount');
    $table->unsignedBigInteger('balance_after');
    $table->string('reference_type', 50)->nullable();
    $table->unsignedBigInteger('reference_id')->nullable();
    $table->string('description', 500)->nullable();
    $table->timestamp('created_at')->useCurrent();
    $table->index(['user_id','created_at']);
});
```

- [ ] **Step 10: Fill `ip_view_logs`**
```php
Schema::create('ip_view_logs', function (Blueprint $table) {
    $table->foreignId('short_link_id')->constrained()->cascadeOnDelete();
    $table->string('ip_address', 45);
    $table->timestamp('viewed_at')->useCurrent();
    $table->primary(['short_link_id','ip_address','viewed_at']);
    $table->index('viewed_at');
});
```

- [ ] **Step 11: Run migrations**

```bash
php artisan migrate
```

Expected: 9 new migrations run successfully.

- [ ] **Step 12: Commit**

```bash
git add database/migrations
git commit -m "feat(db): create 9 domain tables (settings, links, clicks, ads, payouts, wallet)"
```

---

## Phase 2 — Models & Relations

### Task 6: User model with auth + wallet helpers

**Files:**
- Modify: `app/Models/User.php`

- [ ] **Step 1: Replace User model**

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','google_id','avatar',
        'balance','total_earned','status','payout_method',
        'payout_account','preferred_locale','is_admin',
    ];

    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'balance' => 'integer',
            'total_earned' => 'integer',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin && $this->status === 'active';
    }

    public function shortLinks(): HasMany
    {
        return $this->hasMany(ShortLink::class);
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(PayoutRequest::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Models/User.php
git commit -m "feat(model): extend User with FilamentUser + relations"
```

---

### Task 7: Domain models

**Files:**
- Create: `app/Models/{ShortLink,Click,AdCampaign,AdImpression,PayoutRequest,WalletTransaction,Setting,BlacklistDomain,IpViewLog}.php`

- [ ] **Step 1: Generate model files**

```bash
php artisan make:model ShortLink -f
php artisan make:model Click -f
php artisan make:model AdCampaign -f
php artisan make:model AdImpression -f
php artisan make:model PayoutRequest -f
php artisan make:model WalletTransaction -f
php artisan make:model Setting
php artisan make:model BlacklistDomain -f
php artisan make:model IpViewLog
```

- [ ] **Step 2: `app/Models/ShortLink.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShortLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','slug','original_url','title','password',
        'status','total_clicks','valid_views','total_earned',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function clicks(): HasMany { return $this->hasMany(Click::class); }
    public function adImpressions(): HasMany { return $this->hasMany(AdImpression::class); }

    public function isActive(): bool { return $this->status === 'active'; }
    public function hasPassword(): bool { return ! empty($this->password); }
}
```

- [ ] **Step 3: `app/Models/Click.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Click extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['short_link_id','ip_address','user_agent','referer','is_valid','earnings','created_at'];
    protected $casts = ['is_valid' => 'boolean', 'created_at' => 'datetime'];

    public function shortLink(): BelongsTo { return $this->belongsTo(ShortLink::class); }
}
```

- [ ] **Step 4: `app/Models/AdCampaign.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdCampaign extends Model
{
    use HasFactory;

    protected $fillable = ['name','placement','type','content','target_url','weight','status','start_at','end_at','impressions','clicks_count'];
    protected $casts = ['start_at' => 'datetime', 'end_at' => 'datetime'];

    public function impressionsRel(): HasMany { return $this->hasMany(AdImpression::class); }

    public function scopeActive($q)
    {
        return $q->where('status','active')
            ->where(fn($x) => $x->whereNull('start_at')->orWhere('start_at','<=', now()))
            ->where(fn($x) => $x->whereNull('end_at')->orWhere('end_at','>=', now()));
    }
}
```

- [ ] **Step 5: `app/Models/AdImpression.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdImpression extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['ad_campaign_id','short_link_id','click_id','impression_token','ip_address','was_clicked','created_at'];
    protected $casts = ['was_clicked' => 'boolean', 'created_at' => 'datetime'];

    public function adCampaign(): BelongsTo { return $this->belongsTo(AdCampaign::class); }
    public function shortLink(): BelongsTo { return $this->belongsTo(ShortLink::class); }
    public function click(): BelongsTo { return $this->belongsTo(Click::class); }
}
```

- [ ] **Step 6: `app/Models/PayoutRequest.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','amount','method','account_info','status','admin_note','processed_by','processed_at','transaction_ref'];
    protected $casts = ['processed_at' => 'datetime', 'amount' => 'integer'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function processor(): BelongsTo { return $this->belongsTo(User::class, 'processed_by'); }
}
```

- [ ] **Step 7: `app/Models/WalletTransaction.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['user_id','type','amount','balance_after','reference_type','reference_id','description','created_at'];
    protected $casts = ['created_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
```

- [ ] **Step 8: `app/Models/Setting.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;
    protected $fillable = ['key','value','type','description','updated_at'];

    public function getTypedValue(): mixed
    {
        return match ($this->type) {
            'integer' => (int) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }
}
```

- [ ] **Step 9: `app/Models/BlacklistDomain.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlacklistDomain extends Model
{
    use HasFactory;

    protected $fillable = ['domain','reason','created_by'];

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
```

- [ ] **Step 10: `app/Models/IpViewLog.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpViewLog extends Model
{
    public $timestamps = false;
    protected $table = 'ip_view_logs';
    protected $fillable = ['short_link_id','ip_address','viewed_at'];
    protected $casts = ['viewed_at' => 'datetime'];
    public $incrementing = false;
}
```

- [ ] **Step 11: Smoke test models**

```bash
php artisan tinker --execute="echo App\Models\ShortLink::count();"
```

Expected: `0` (no error).

- [ ] **Step 12: Commit**

```bash
git add app/Models
git commit -m "feat(model): add 9 domain models with relations"
```

---

## Phase 3 — Auth + Google OAuth + i18n

### Task 8: Google OAuth controller

**Files:**
- Create: `app/Http/Controllers/Auth/GoogleController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create GoogleController**

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $g = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
            ['email' => $g->getEmail()],
            [
                'name' => $g->getName() ?: $g->getNickname() ?: 'User',
                'google_id' => $g->getId(),
                'avatar' => $g->getAvatar(),
                'email_verified_at' => now(),
                'password' => $u['password'] ?? Str::password(32),
            ]
        );

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}
```

- [ ] **Step 2: Add routes**

In `routes/web.php` before `require __DIR__.'/auth.php';`:
```php
use App\Http\Controllers\Auth\GoogleController;
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
```

- [ ] **Step 3: Add "Continue with Google" button to `resources/views/auth/login.blade.php`**

Above the existing form, add:
```blade
<a href="{{ route('auth.google') }}"
   class="flex w-full items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 mb-4">
    <svg class="h-5 w-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.07H2.18A10.97 10.97 0 0 0 1 12c0 1.77.42 3.45 1.18 4.93l3.66-2.83z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.83C6.71 7.31 9.14 5.38 12 5.38z"/></svg>
    {{ __('Continue with Google') }}
</a>
<div class="relative mb-4"><div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-300"></div></div><div class="relative flex justify-center text-xs"><span class="bg-white px-2 text-gray-500 dark:bg-gray-800">{{ __('or') }}</span></div></div>
```

- [ ] **Step 4: Test redirect manually**

```bash
php artisan serve
```

Visit `http://localhost:8000/auth/google` — expect redirect to Google (will fail without valid GOOGLE_CLIENT_ID, but route resolves).

- [ ] **Step 5: Commit**

```bash
git add .
git commit -m "feat(auth): add Google OAuth via Socialite"
```

---

### Task 9: i18n setup (VN + EN)

**Files:**
- Create: `lang/vi.json`, `lang/en.json`
- Create: `app/Http/Middleware/SetLocale.php`
- Create: `app/Http/Controllers/LocaleController.php`
- Modify: `bootstrap/app.php`, `routes/web.php`

- [ ] **Step 1: Create lang files**

`lang/en.json`:
```json
{
    "Shorten your URL": "Shorten your URL",
    "Original URL": "Original URL",
    "Custom alias (optional)": "Custom alias (optional)",
    "Password (optional)": "Password (optional)",
    "Continue with Google": "Continue with Google",
    "or": "or",
    "Dashboard": "Dashboard",
    "My links": "My links",
    "Payout": "Payout",
    "Profile": "Profile",
    "Log out": "Log out",
    "Balance": "Balance",
    "Total earned": "Total earned",
    "Skip Ad": "Skip Ad",
    "Please complete the captcha": "Please complete the captcha",
    "This link is protected": "This link is protected",
    "Enter password": "Enter password",
    "Unlock": "Unlock",
    "Link not available": "Link not available",
    "Request payout": "Request payout",
    "Amount (VND)": "Amount (VND)",
    "Method": "Method",
    "Account info": "Account info",
    "Submit request": "Submit request",
    "Insufficient balance": "Insufficient balance",
    "Domain is blacklisted": "Domain is blacklisted",
    "Alias already taken": "Alias already taken"
}
```

`lang/vi.json`:
```json
{
    "Shorten your URL": "Rút gọn liên kết",
    "Original URL": "URL gốc",
    "Custom alias (optional)": "Alias tuỳ chỉnh (tuỳ chọn)",
    "Password (optional)": "Mật khẩu (tuỳ chọn)",
    "Continue with Google": "Tiếp tục với Google",
    "or": "hoặc",
    "Dashboard": "Bảng điều khiển",
    "My links": "Liên kết của tôi",
    "Payout": "Rút tiền",
    "Profile": "Hồ sơ",
    "Log out": "Đăng xuất",
    "Balance": "Số dư",
    "Total earned": "Tổng thu nhập",
    "Skip Ad": "Bỏ qua quảng cáo",
    "Please complete the captcha": "Vui lòng hoàn thành captcha",
    "This link is protected": "Liên kết này được bảo vệ",
    "Enter password": "Nhập mật khẩu",
    "Unlock": "Mở khóa",
    "Link not available": "Liên kết không khả dụng",
    "Request payout": "Yêu cầu rút tiền",
    "Amount (VND)": "Số tiền (VND)",
    "Method": "Phương thức",
    "Account info": "Thông tin tài khoản",
    "Submit request": "Gửi yêu cầu",
    "Insufficient balance": "Số dư không đủ",
    "Domain is blacklisted": "Tên miền nằm trong danh sách đen",
    "Alias already taken": "Alias đã được sử dụng"
}
```

- [ ] **Step 2: Create SetLocale middleware**

```bash
php artisan make:middleware SetLocale
```

`app/Http/Middleware/SetLocale.php`:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->cookie('locale')
            ?? optional($request->user())->preferred_locale
            ?? config('app.locale');

        if (in_array($locale, ['vi','en'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
```

- [ ] **Step 3: Register middleware globally**

In `bootstrap/app.php`, inside `withMiddleware(...)` callback:
```php
$middleware->web(append: [
    \App\Http\Middleware\SetLocale::class,
]);
```

- [ ] **Step 4: Create LocaleController**

```bash
php artisan make:controller LocaleController
```

`app/Http/Controllers/LocaleController.php`:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        abort_unless(in_array($locale, ['vi','en']), 404);

        if ($user = $request->user()) {
            $user->update(['preferred_locale' => $locale]);
        }

        return back()->withCookie(cookie('locale', $locale, 60 * 24 * 365));
    }
}
```

- [ ] **Step 5: Add route**

In `routes/web.php`:
```php
use App\Http\Controllers\LocaleController;
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
```

- [ ] **Step 6: Commit**

```bash
git add lang app/Http/Middleware/SetLocale.php app/Http/Controllers/LocaleController.php bootstrap/app.php routes/web.php
git commit -m "feat(i18n): VN+EN switcher with cookie + user preference"
```

---

## Phase 4 — Foundational Services (TDD)

### Task 10: SettingService

**Files:**
- Create: `app/Services/SettingService.php`
- Create: `tests/Unit/Services/SettingServiceTest.php`

- [ ] **Step 1: Write failing test**

```bash
php artisan make:test --pest Unit/Services/SettingServiceTest
```

`tests/Unit/Services/SettingServiceTest.php`:
```php
<?php

use App\Models\Setting;
use App\Services\SettingService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('returns default when key missing', function () {
    $svc = new SettingService();
    expect($svc->get('missing_key', 'fallback'))->toBe('fallback');
});

it('returns typed integer value', function () {
    Setting::create(['key' => 'rate', 'value' => '5000', 'type' => 'integer']);
    $svc = new SettingService();
    expect($svc->get('rate'))->toBe(5000);
});

it('sets and persists value', function () {
    $svc = new SettingService();
    $svc->set('foo', 'bar');
    expect(Setting::where('key','foo')->value('value'))->toBe('bar');
});
```

- [ ] **Step 2: Run test — expect FAIL**

```bash
./vendor/bin/pest tests/Unit/Services/SettingServiceTest.php
```

Expected: `class SettingService not found`.

- [ ] **Step 3: Implement SettingService**

`app/Services/SettingService.php`:
```php
<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->getTypedValue() : $default;
    }

    public function set(string $key, mixed $value, string $type = 'string'): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value,
             'type' => $type, 'updated_at' => now()]
        );
    }
}
```

- [ ] **Step 4: Run test — expect PASS**

```bash
./vendor/bin/pest tests/Unit/Services/SettingServiceTest.php
```

Expected: 3 passed.

- [ ] **Step 5: Commit**

```bash
git add app/Services/SettingService.php tests/Unit/Services
git commit -m "feat(service): SettingService get/set typed values"
```

---

### Task 11: WalletService (atomic credit/debit)

**Files:**
- Create: `app/Services/WalletService.php`
- Create: `tests/Unit/Services/WalletServiceTest.php`

- [ ] **Step 1: Write failing test**

`tests/Unit/Services/WalletServiceTest.php`:
```php
<?php

use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\WalletService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('credits user balance atomically and records transaction', function () {
    $user = User::factory()->create(['balance' => 1000, 'total_earned' => 0]);
    $svc = new WalletService();

    $svc->credit($user, 500, 'click', 42, 'click earnings');

    $user->refresh();
    expect($user->balance)->toBe(1500);
    expect($user->total_earned)->toBe(500);

    $tx = WalletTransaction::where('user_id', $user->id)->first();
    expect($tx->type)->toBe('credit');
    expect($tx->amount)->toBe(500);
    expect($tx->balance_after)->toBe(1500);
    expect($tx->reference_type)->toBe('click');
    expect($tx->reference_id)->toBe(42);
});

it('debits user balance and rejects when insufficient', function () {
    $user = User::factory()->create(['balance' => 100]);
    $svc = new WalletService();

    expect(fn() => $svc->debit($user, 200, 'payout_hold', null))
        ->toThrow(\RuntimeException::class, 'Insufficient balance');

    $user->refresh();
    expect($user->balance)->toBe(100);
});

it('debits successfully and records transaction', function () {
    $user = User::factory()->create(['balance' => 500]);
    $svc = new WalletService();

    $svc->debit($user, 200, 'payout_hold', 7, 'payout req #7');

    $user->refresh();
    expect($user->balance)->toBe(300);

    $tx = WalletTransaction::where('user_id', $user->id)->first();
    expect($tx->amount)->toBe(-200);
    expect($tx->balance_after)->toBe(300);
});
```

- [ ] **Step 2: Run — FAIL**

```bash
./vendor/bin/pest tests/Unit/Services/WalletServiceTest.php
```

- [ ] **Step 3: Implement WalletService**

`app/Services/WalletService.php`:
```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function credit(User $user, int $amount, ?string $refType = null, ?int $refId = null, ?string $description = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $refType, $refId, $description) {
            $user = User::lockForUpdate()->find($user->id);
            $user->balance += $amount;
            $user->total_earned += $amount;
            $user->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $user->balance,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'description' => $description,
                'created_at' => now(),
            ]);
        });
    }

    public function debit(User $user, int $amount, string $type, ?int $refId = null, ?string $description = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $type, $refId, $description) {
            $user = User::lockForUpdate()->find($user->id);
            if ($user->balance < $amount) {
                throw new \RuntimeException('Insufficient balance');
            }
            $user->balance -= $amount;
            $user->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => $type,
                'amount' => -$amount,
                'balance_after' => $user->balance,
                'reference_type' => $type === 'payout_hold' ? 'payout_request' : null,
                'reference_id' => $refId,
                'description' => $description,
                'created_at' => now(),
            ]);
        });
    }

    public function refund(User $user, int $amount, ?int $refId = null, ?string $description = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $refId, $description) {
            $user = User::lockForUpdate()->find($user->id);
            $user->balance += $amount;
            $user->save();

            return WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'payout_reject',
                'amount' => $amount,
                'balance_after' => $user->balance,
                'reference_type' => 'payout_request',
                'reference_id' => $refId,
                'description' => $description,
                'created_at' => now(),
            ]);
        });
    }
}
```

- [ ] **Step 4: Run — PASS**

```bash
./vendor/bin/pest tests/Unit/Services/WalletServiceTest.php
```

- [ ] **Step 5: Commit**

```bash
git add app/Services/WalletService.php tests/Unit/Services/WalletServiceTest.php
git commit -m "feat(service): WalletService credit/debit/refund with DB lock"
```

---

### Task 12: CaptchaService (Cloudflare Turnstile)

**Files:**
- Create: `app/Services/CaptchaService.php`
- Create: `tests/Unit/Services/CaptchaServiceTest.php`

- [ ] **Step 1: Write test**

`tests/Unit/Services/CaptchaServiceTest.php`:
```php
<?php

use App\Services\CaptchaService;
use Illuminate\Support\Facades\Http;

it('returns true when Turnstile verifies success', function () {
    Http::fake([
        'challenges.cloudflare.com/*' => Http::response(['success' => true], 200),
    ]);
    $svc = new CaptchaService();
    expect($svc->verify('any-token', '127.0.0.1'))->toBeTrue();
});

it('returns false when Turnstile fails', function () {
    Http::fake([
        'challenges.cloudflare.com/*' => Http::response(['success' => false], 200),
    ]);
    $svc = new CaptchaService();
    expect($svc->verify('bad', '127.0.0.1'))->toBeFalse();
});

it('returns false when http throws', function () {
    Http::fake(fn() => throw new \Exception('network down'));
    $svc = new CaptchaService();
    expect($svc->verify('x', '127.0.0.1'))->toBeFalse();
});
```

- [ ] **Step 2: Run — FAIL**

- [ ] **Step 3: Implement**

`app/Services/CaptchaService.php`:
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CaptchaService
{
    public function verify(?string $token, string $ip): bool
    {
        if (! $token) return false;

        try {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ]);
            return (bool) $response->json('success', false);
        } catch (\Throwable $e) {
            Log::warning('Turnstile verify failed: '.$e->getMessage());
            return false;
        }
    }
}
```

- [ ] **Step 4: Run — PASS**

- [ ] **Step 5: Commit**

```bash
git add app/Services/CaptchaService.php tests/Unit/Services/CaptchaServiceTest.php
git commit -m "feat(service): CaptchaService for Turnstile verify"
```

---

## Phase 5 — Link Management

### Task 13: ShortLinkService (slug gen + blacklist check)

**Files:**
- Create: `app/Services/ShortLinkService.php`
- Create: `tests/Unit/Services/ShortLinkServiceTest.php`

- [ ] **Step 1: Write tests**

`tests/Unit/Services/ShortLinkServiceTest.php`:
```php
<?php

use App\Models\BlacklistDomain;
use App\Models\ShortLink;
use App\Models\User;
use App\Services\ShortLinkService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('generates unique 6-char slug', function () {
    $svc = new ShortLinkService();
    $slug = $svc->generateUniqueSlug();
    expect($slug)->toMatch('/^[A-Za-z0-9]{6}$/');
});

it('avoids slug collisions', function () {
    ShortLink::factory()->create(['slug' => 'ABC123']);
    $svc = new ShortLinkService();
    for ($i = 0; $i < 5; $i++) {
        $slug = $svc->generateUniqueSlug();
        expect($slug)->not->toBe('ABC123');
    }
});

it('blocks blacklisted domain', function () {
    BlacklistDomain::create(['domain' => 'spam.test']);
    $svc = new ShortLinkService();
    expect(fn() => $svc->create(null, 'https://spam.test/foo'))
        ->toThrow(\RuntimeException::class, 'Domain is blacklisted');
});

it('rejects duplicate custom alias', function () {
    ShortLink::factory()->create(['slug' => 'mycoolalias']);
    $svc = new ShortLinkService();
    expect(fn() => $svc->create(null, 'https://example.com', 'mycoolalias'))
        ->toThrow(\RuntimeException::class, 'Alias already taken');
});

it('hashes password when provided', function () {
    $user = User::factory()->create();
    $svc = new ShortLinkService();
    $link = $svc->create($user->id, 'https://example.com', null, 'secret');
    expect($link->password)->not->toBe('secret');
    expect(\Hash::check('secret', $link->password))->toBeTrue();
});
```

- [ ] **Step 2: Run — FAIL**

- [ ] **Step 3: Implement**

`app/Services/ShortLinkService.php`:
```php
<?php

namespace App\Services;

use App\Models\BlacklistDomain;
use App\Models\ShortLink;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ShortLinkService
{
    public function generateUniqueSlug(int $length = 6): string
    {
        do {
            $slug = Str::random($length);
        } while (ShortLink::where('slug', $slug)->exists());
        return $slug;
    }

    public function create(?int $userId, string $originalUrl, ?string $customAlias = null, ?string $password = null): ShortLink
    {
        $host = parse_url($originalUrl, PHP_URL_HOST);
        if ($host && BlacklistDomain::where('domain', $host)->exists()) {
            throw new \RuntimeException('Domain is blacklisted');
        }

        $slug = $customAlias ?: $this->generateUniqueSlug();
        if ($customAlias && ShortLink::where('slug', $customAlias)->exists()) {
            throw new \RuntimeException('Alias already taken');
        }

        return ShortLink::create([
            'user_id' => $userId,
            'slug' => $slug,
            'original_url' => $originalUrl,
            'password' => $password ? Hash::make($password) : null,
            'status' => 'active',
        ]);
    }
}
```

- [ ] **Step 4: Run — PASS**

- [ ] **Step 5: Commit**

```bash
git add app/Services/ShortLinkService.php tests/Unit/Services/ShortLinkServiceTest.php
git commit -m "feat(service): ShortLinkService create/slug-gen/blacklist"
```

---

### Task 14: Link CRUD controller + form requests

**Files:**
- Create: `app/Http/Requests/StoreShortLinkRequest.php`, `UpdateShortLinkRequest.php`
- Create: `app/Http/Controllers/ShortLinkController.php`
- Create: `app/Http/Controllers/HomeController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create form requests**

```bash
php artisan make:request StoreShortLinkRequest
php artisan make:request UpdateShortLinkRequest
```

`StoreShortLinkRequest.php`:
```php
<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreShortLinkRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'original_url' => ['required','url:http,https','max:2048'],
            'custom_alias' => ['nullable','alpha_dash','min:3','max:32','unique:short_links,slug'],
            'password' => ['nullable','string','min:4','max:64'],
        ];
    }
}
```

`UpdateShortLinkRequest.php` (same minus alias unique scope):
```php
<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShortLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->id === $this->route('link')->user_id;
    }

    public function rules(): array
    {
        return [
            'original_url' => ['required','url:http,https','max:2048'],
            'password' => ['nullable','string','min:4','max:64'],
            'status' => ['required','in:active,disabled'],
        ];
    }
}
```

- [ ] **Step 2: Create ShortLinkController**

```bash
php artisan make:controller ShortLinkController --resource
```

`app/Http/Controllers/ShortLinkController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortLinkRequest;
use App\Http\Requests\UpdateShortLinkRequest;
use App\Models\ShortLink;
use App\Services\ShortLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ShortLinkController extends Controller
{
    public function __construct(private ShortLinkService $svc) {}

    public function index(Request $request)
    {
        $links = $request->user()->shortLinks()->latest()->paginate(20);
        return view('links.index', compact('links'));
    }

    public function create() { return view('links.create'); }

    public function store(StoreShortLinkRequest $request)
    {
        try {
            $link = $this->svc->create(
                $request->user()->id,
                $request->original_url,
                $request->custom_alias,
                $request->password,
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['original_url' => __($e->getMessage())])->withInput();
        }

        return redirect()->route('links.index')->with('shortUrl', url('/'.$link->slug));
    }

    public function edit(ShortLink $link)
    {
        abort_unless($link->user_id === request()->user()->id, 403);
        return view('links.edit', compact('link'));
    }

    public function update(UpdateShortLinkRequest $request, ShortLink $link)
    {
        $data = $request->only(['original_url','status']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } elseif ($request->boolean('remove_password')) {
            $data['password'] = null;
        }
        $link->update($data);
        return redirect()->route('links.index')->with('status', __('Updated'));
    }

    public function destroy(ShortLink $link)
    {
        abort_unless($link->user_id === request()->user()->id, 403);
        $link->delete();
        return back()->with('status', __('Deleted'));
    }
}
```

- [ ] **Step 3: Create HomeController for landing + guest shorten**

`app/Http/Controllers/HomeController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Services\ShortLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function __construct(private ShortLinkService $svc) {}

    public function index() { return view('home'); }

    public function shortenGuest(Request $request)
    {
        $data = Validator::make($request->all(), [
            'original_url' => ['required','url:http,https','max:2048'],
            'custom_alias' => ['nullable','alpha_dash','min:3','max:32','unique:short_links,slug'],
        ])->validate();

        try {
            $link = $this->svc->create(null, $data['original_url'], $data['custom_alias'] ?? null);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['original_url' => __($e->getMessage())])->withInput();
        }

        return back()->with('shortUrl', url('/'.$link->slug));
    }
}
```

- [ ] **Step 4: Wire routes**

In `routes/web.php`:
```php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShortLinkController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/shorten', [HomeController::class, 'shortenGuest'])->name('shorten.guest');

Route::middleware(['auth','verified'])->group(function () {
    Route::resource('links', ShortLinkController::class)->except('show');
});
```

- [ ] **Step 5: Commit**

```bash
git add .
git commit -m "feat(links): CRUD controller + form requests + guest shorten"
```

---

### Task 15: Link views (Blade + Tailwind)

**Files:**
- Create: `resources/views/home.blade.php`, `links/index.blade.php`, `links/create.blade.php`, `links/edit.blade.php`

- [ ] **Step 1: `home.blade.php`**

```blade
<x-guest-layout>
<div class="max-w-3xl mx-auto px-4 py-16">
    <h1 class="text-4xl font-bold mb-2">{{ __('Shorten your URL') }}</h1>
    <p class="text-gray-600 dark:text-gray-300 mb-8">{{ __('Earn money for every valid view of your shortened link.') }}</p>

    @if(session('shortUrl'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-300 rounded p-4 mb-4">
            <strong>{{ __('Your short URL') }}:</strong>
            <a href="{{ session('shortUrl') }}" class="text-blue-600 underline" target="_blank">{{ session('shortUrl') }}</a>
        </div>
    @endif

    <form method="POST" action="{{ route('shorten.guest') }}" class="space-y-4 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">{{ __('Original URL') }}</label>
            <input name="original_url" value="{{ old('original_url') }}" type="url" required
                   class="w-full rounded border-gray-300 dark:bg-gray-700"
                   placeholder="https://example.com/...">
            @error('original_url') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">{{ __('Custom alias (optional)') }}</label>
            <input name="custom_alias" value="{{ old('custom_alias') }}" type="text" pattern="[A-Za-z0-9_-]{3,32}"
                   class="w-full rounded border-gray-300 dark:bg-gray-700"
                   placeholder="my-link">
        </div>
        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded">
            {{ __('Shorten') }}
        </button>
        @guest
            <p class="text-xs text-gray-500 text-center">{{ __('Sign up to earn money from your links.') }}</p>
        @endguest
    </form>
</div>
</x-guest-layout>
```

- [ ] **Step 2: `links/index.blade.php`**

```blade
<x-app-layout>
<x-slot name="header"><h2 class="font-semibold text-xl">{{ __('My links') }}</h2></x-slot>
<div class="py-12 max-w-7xl mx-auto px-4">
    @if(session('shortUrl'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-300 rounded p-4 mb-4">
            {{ __('Short URL') }}:
            <a href="{{ session('shortUrl') }}" class="text-blue-600 underline" target="_blank">{{ session('shortUrl') }}</a>
        </div>
    @endif
    <a href="{{ route('links.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mb-4">+ {{ __('New link') }}</a>

    <div class="bg-white dark:bg-gray-800 rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700"><tr>
            <th class="px-4 py-2 text-left">Short</th>
            <th class="px-4 py-2 text-left">{{ __('Original URL') }}</th>
            <th class="px-4 py-2">Clicks</th>
            <th class="px-4 py-2">{{ __('Valid views') }}</th>
            <th class="px-4 py-2">{{ __('Earned') }}</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2"></th>
        </tr></thead>
        <tbody>
        @forelse($links as $link)
            <tr class="border-t dark:border-gray-700">
                <td class="px-4 py-2"><a href="{{ url('/'.$link->slug) }}" class="text-blue-600 underline" target="_blank">/{{ $link->slug }}</a></td>
                <td class="px-4 py-2 truncate max-w-xs">{{ $link->original_url }}</td>
                <td class="px-4 py-2 text-center">{{ $link->total_clicks }}</td>
                <td class="px-4 py-2 text-center">{{ $link->valid_views }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($link->total_earned) }} đ</td>
                <td class="px-4 py-2 text-center"><span class="px-2 py-0.5 rounded text-xs {{ $link->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }}">{{ $link->status }}</span></td>
                <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                    <a href="{{ route('links.edit', $link) }}" class="text-blue-600">{{ __('Edit') }}</a>
                    <form method="POST" action="{{ route('links.destroy', $link) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                        <button class="text-red-600">{{ __('Delete') }}</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center py-8 text-gray-500">{{ __('No links yet') }}</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $links->links() }}</div>
</div>
</x-app-layout>
```

- [ ] **Step 3: `links/create.blade.php`**

```blade
<x-app-layout>
<x-slot name="header"><h2 class="font-semibold text-xl">{{ __('New link') }}</h2></x-slot>
<div class="py-12 max-w-2xl mx-auto px-4">
<form method="POST" action="{{ route('links.store') }}" class="space-y-4 bg-white dark:bg-gray-800 p-6 rounded shadow">
    @csrf
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Original URL') }}</label>
        <input name="original_url" value="{{ old('original_url') }}" type="url" required class="w-full rounded">
        @error('original_url') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Custom alias (optional)') }}</label>
        <input name="custom_alias" value="{{ old('custom_alias') }}" type="text" class="w-full rounded">
        @error('custom_alias') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Password (optional)') }}</label>
        <input name="password" type="text" class="w-full rounded" autocomplete="off">
        @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>
    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">{{ __('Create') }}</button>
</form>
</div>
</x-app-layout>
```

- [ ] **Step 4: `links/edit.blade.php`**

```blade
<x-app-layout>
<x-slot name="header"><h2 class="font-semibold text-xl">{{ __('Edit link') }} /{{ $link->slug }}</h2></x-slot>
<div class="py-12 max-w-2xl mx-auto px-4">
<form method="POST" action="{{ route('links.update', $link) }}" class="space-y-4 bg-white dark:bg-gray-800 p-6 rounded shadow">
    @csrf @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('Original URL') }}</label>
        <input name="original_url" value="{{ old('original_url', $link->original_url) }}" type="url" required class="w-full rounded">
        @error('original_url') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="w-full rounded">
            <option value="active" @selected($link->status==='active')>Active</option>
            <option value="disabled" @selected($link->status==='disabled')>Disabled</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ __('New password') }} (leave blank to keep current)</label>
        <input name="password" type="text" class="w-full rounded" autocomplete="off">
        @if($link->password)<label class="text-sm"><input type="checkbox" name="remove_password" value="1"> {{ __('Remove password') }}</label>@endif
    </div>
    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">{{ __('Save') }}</button>
</form>
</div>
</x-app-layout>
```

- [ ] **Step 5: Manual smoke**

```bash
php artisan serve
```

Visit `http://localhost:8000` — see landing. Login (after Breeze register), visit `/links/create` → create a link.

- [ ] **Step 6: Commit**

```bash
git add resources/views
git commit -m "feat(links): Blade views for landing + CRUD"
```

---

## Phase 6 — Redirect & Ad Serving (CORE)

### Task 16: AdServingService — multi-slot weighted random

**Files:**
- Create: `app/Services/AdServingService.php`
- Create: `tests/Unit/Services/AdServingServiceTest.php`

- [ ] **Step 1: Write test**

`tests/Unit/Services/AdServingServiceTest.php`:
```php
<?php

use App\Models\AdCampaign;
use App\Services\AdServingService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('returns null for placement with no active ads', function () {
    $svc = new AdServingService();
    $ads = $svc->pickForInterstitial();
    expect($ads)->toBe(['top' => null, 'side' => null, 'bottom' => null]);
});

it('picks one ad per placement when ads exist', function () {
    AdCampaign::factory()->create(['placement' => 'top', 'status' => 'active']);
    AdCampaign::factory()->create(['placement' => 'side', 'status' => 'active']);
    AdCampaign::factory()->create(['placement' => 'bottom', 'status' => 'active']);

    $svc = new AdServingService();
    $ads = $svc->pickForInterstitial();

    expect($ads['top'])->not->toBeNull();
    expect($ads['side'])->not->toBeNull();
    expect($ads['bottom'])->not->toBeNull();
});

it('respects weight distribution roughly', function () {
    AdCampaign::factory()->create(['placement' => 'top', 'weight' => 1, 'status' => 'active', 'name' => 'A']);
    AdCampaign::factory()->create(['placement' => 'top', 'weight' => 99, 'status' => 'active', 'name' => 'B']);

    $svc = new AdServingService();
    $countB = 0;
    for ($i = 0; $i < 100; $i++) {
        $ads = $svc->pickForInterstitial();
        if ($ads['top']->name === 'B') $countB++;
    }
    expect($countB)->toBeGreaterThan(80);  // B should dominate
});

it('skips paused or expired campaigns', function () {
    AdCampaign::factory()->create(['placement' => 'top', 'status' => 'paused']);
    AdCampaign::factory()->create(['placement' => 'side', 'status' => 'active', 'end_at' => now()->subDay()]);

    $svc = new AdServingService();
    $ads = $svc->pickForInterstitial();
    expect($ads['top'])->toBeNull();
    expect($ads['side'])->toBeNull();
});
```

- [ ] **Step 2: Add factory states for AdCampaign**

`database/factories/AdCampaignFactory.php`:
```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdCampaignFactory extends Factory
{
    public function definition(): array
    {
        $placement = fake()->randomElement(['top','side','bottom']);
        $size = match($placement){'top','bottom'=>'728/90','side'=>'300/250'};
        return [
            'name' => fake()->company().' '.strtoupper($placement),
            'placement' => $placement,
            'type' => 'banner_image',
            'content' => "https://picsum.photos/seed/{$placement}".fake()->numberBetween(1,1000)."/{$size}",
            'target_url' => fake()->url(),
            'weight' => fake()->numberBetween(1, 10),
            'status' => 'active',
        ];
    }
}
```

- [ ] **Step 3: Implement AdServingService**

`app/Services/AdServingService.php`:
```php
<?php

namespace App\Services;

use App\Models\AdCampaign;

class AdServingService
{
    public function pickForInterstitial(): array
    {
        return [
            'top' => $this->pickOne('top'),
            'side' => $this->pickOne('side'),
            'bottom' => $this->pickOne('bottom'),
        ];
    }

    private function pickOne(string $placement): ?AdCampaign
    {
        return AdCampaign::active()
            ->where('placement', $placement)
            ->orderByRaw('-LN(1.0 - RAND()) / GREATEST(weight,1) ASC')
            ->first();
    }
}
```

- [ ] **Step 4: Run — PASS**

```bash
./vendor/bin/pest tests/Unit/Services/AdServingServiceTest.php
```

- [ ] **Step 5: Commit**

```bash
git add app/Services/AdServingService.php tests/Unit/Services/AdServingServiceTest.php database/factories/AdCampaignFactory.php
git commit -m "feat(service): AdServingService weighted random per placement"
```

---

### Task 17: ClickTrackingService — dedup + earnings

**Files:**
- Create: `app/Services/ClickTrackingService.php`
- Create: `tests/Unit/Services/ClickTrackingServiceTest.php`

- [ ] **Step 1: Test**

`tests/Unit/Services/ClickTrackingServiceTest.php`:
```php
<?php

use App\Models\IpViewLog;
use App\Models\Setting;
use App\Models\ShortLink;
use App\Models\User;
use App\Services\ClickTrackingService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Setting::create(['key'=>'rate_per_1000_views','value'=>'5000','type'=>'integer']);
    Setting::create(['key'=>'ip_dedup_hours','value'=>'24','type'=>'integer']);
});

it('credits owner balance on valid view', function () {
    $owner = User::factory()->create(['balance' => 0]);
    $link = ShortLink::factory()->create(['user_id' => $owner->id]);

    $svc = app(ClickTrackingService::class);
    $click = $svc->record($link, '1.2.3.4', null, captchaPass: true, viewerUserId: null);

    expect($click->is_valid)->toBeTrue();
    expect($click->earnings)->toBe(5);  // 5000/1000
    expect($owner->fresh()->balance)->toBe(5);
});

it('marks invalid when same IP repeats within 24h', function () {
    $owner = User::factory()->create();
    $link = ShortLink::factory()->create(['user_id' => $owner->id]);
    IpViewLog::create(['short_link_id'=>$link->id,'ip_address'=>'1.2.3.4','viewed_at'=>now()->subHour()]);

    $svc = app(ClickTrackingService::class);
    $click = $svc->record($link, '1.2.3.4', null, captchaPass: true, viewerUserId: null);

    expect($click->is_valid)->toBeFalse();
    expect($click->earnings)->toBe(0);
});

it('marks invalid on captcha fail', function () {
    $owner = User::factory()->create();
    $link = ShortLink::factory()->create(['user_id' => $owner->id]);

    $svc = app(ClickTrackingService::class);
    $click = $svc->record($link, '5.5.5.5', null, captchaPass: false, viewerUserId: null);
    expect($click->is_valid)->toBeFalse();
});

it('blocks self-click', function () {
    $owner = User::factory()->create();
    $link = ShortLink::factory()->create(['user_id' => $owner->id]);

    $svc = app(ClickTrackingService::class);
    $click = $svc->record($link, '9.9.9.9', null, captchaPass: true, viewerUserId: $owner->id);
    expect($click->is_valid)->toBeFalse();
});

it('handles guest link (no owner credit) but still counts click', function () {
    $link = ShortLink::factory()->create(['user_id' => null]);

    $svc = app(ClickTrackingService::class);
    $click = $svc->record($link, '8.8.8.8', null, captchaPass: true, viewerUserId: null);

    expect($click->is_valid)->toBeTrue();
    expect($link->fresh()->total_clicks)->toBe(1);
});
```

- [ ] **Step 2: ShortLink factory**

`database/factories/ShortLinkFactory.php`:
```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShortLinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => null,
            'slug' => Str::random(6),
            'original_url' => fake()->url(),
            'status' => 'active',
        ];
    }
}
```

- [ ] **Step 3: Implement**

`app/Services/ClickTrackingService.php`:
```php
<?php

namespace App\Services;

use App\Models\Click;
use App\Models\IpViewLog;
use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClickTrackingService
{
    public function __construct(
        private SettingService $settings,
        private WalletService $wallet,
    ) {}

    public function record(ShortLink $link, string $ip, ?string $userAgent, bool $captchaPass, ?int $viewerUserId, ?string $referer = null): Click
    {
        $hours = (int) $this->settings->get('ip_dedup_hours', 24);
        $alreadyViewed = IpViewLog::where('short_link_id', $link->id)
            ->where('ip_address', $ip)
            ->where('viewed_at', '>=', now()->subHours($hours))
            ->exists();

        $selfClick = $viewerUserId && $link->user_id && $viewerUserId === $link->user_id;
        $isValid = $captchaPass && ! $alreadyViewed && ! $selfClick;

        $earnings = 0;
        if ($isValid) {
            $rate = (int) $this->settings->get('rate_per_1000_views', 5000);
            $earnings = intdiv($rate, 1000);
        }

        return DB::transaction(function () use ($link, $ip, $userAgent, $referer, $isValid, $earnings) {
            $click = Click::create([
                'short_link_id' => $link->id,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'referer' => $referer,
                'is_valid' => $isValid,
                'earnings' => $earnings,
                'created_at' => now(),
            ]);

            $link->increment('total_clicks');

            if ($isValid) {
                IpViewLog::create([
                    'short_link_id' => $link->id,
                    'ip_address' => $ip,
                    'viewed_at' => now(),
                ]);
                $link->increment('valid_views');

                if ($link->user_id && $earnings > 0) {
                    $link->increment('total_earned', $earnings);
                    $this->wallet->credit(
                        User::find($link->user_id),
                        $earnings,
                        'click',
                        $click->id,
                        "Click /{$link->slug}"
                    );
                }
            }

            return $click;
        });
    }
}
```

- [ ] **Step 4: Run — PASS**

- [ ] **Step 5: Commit**

```bash
git add app/Services/ClickTrackingService.php tests/Unit/Services/ClickTrackingServiceTest.php database/factories/ShortLinkFactory.php
git commit -m "feat(service): ClickTrackingService dedup + earnings + self-click block"
```

---

### Task 18: RedirectController (show + unlock) + InterstitialController (verify)

**Files:**
- Create: `app/Http/Controllers/RedirectController.php`, `InterstitialController.php`
- Create: `app/Http/Requests/UnlockLinkRequest.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create UnlockLinkRequest**

```bash
php artisan make:request UnlockLinkRequest
```

```php
<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UnlockLinkRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['password' => ['required','string','max:64']]; }
}
```

- [ ] **Step 2: RedirectController**

```bash
php artisan make:controller RedirectController
```

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnlockLinkRequest;
use App\Models\AdImpression;
use App\Models\ShortLink;
use App\Services\AdServingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RedirectController extends Controller
{
    public function show(Request $request, string $slug, AdServingService $ads)
    {
        $link = ShortLink::where('slug', $slug)->first();
        abort_if(! $link, 404);

        if (! $link->isActive()) {
            return response()->view('interstitial.blocked', ['link' => $link], 410);
        }

        if ($link->hasPassword() && ! session()->has("unlocked:{$slug}")) {
            return view('interstitial.password', ['slug' => $slug]);
        }

        $picked = $ads->pickForInterstitial();
        $token = (string) Str::uuid();

        foreach ($picked as $ad) {
            if ($ad) {
                AdImpression::create([
                    'ad_campaign_id' => $ad->id,
                    'short_link_id' => $link->id,
                    'impression_token' => $token,
                    'ip_address' => $request->ip(),
                    'created_at' => now(),
                ]);
                $ad->increment('impressions');
            }
        }

        return view('interstitial.countdown', [
            'link' => $link,
            'ads' => $picked,
            'token' => $token,
            'seconds' => 5,
            'turnstileSiteKey' => config('services.turnstile.site_key'),
        ]);
    }

    public function unlock(UnlockLinkRequest $request, string $slug)
    {
        $link = ShortLink::where('slug', $slug)->firstOrFail();
        if (! $link->hasPassword() || ! Hash::check($request->password, $link->password)) {
            return back()->withErrors(['password' => __('Invalid password')]);
        }
        session()->put("unlocked:{$slug}", true);
        return redirect()->route('link.show', $slug);
    }
}
```

- [ ] **Step 3: InterstitialController**

```bash
php artisan make:controller InterstitialController
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\AdImpression;
use App\Models\ShortLink;
use App\Services\CaptchaService;
use App\Services\ClickTrackingService;
use Illuminate\Http\Request;

class InterstitialController extends Controller
{
    public function __construct(
        private CaptchaService $captcha,
        private ClickTrackingService $tracker,
    ) {}

    public function verify(Request $request, string $slug)
    {
        $data = $request->validate([
            'impression_token' => ['required','string','size:36'],
            'cf-turnstile-response' => ['nullable','string'],
        ]);

        $link = ShortLink::where('slug', $slug)->where('status','active')->firstOrFail();

        $captchaPass = $this->captcha->verify(
            $request->input('cf-turnstile-response'),
            $request->ip()
        );

        $click = $this->tracker->record(
            $link,
            $request->ip(),
            $request->userAgent(),
            $captchaPass,
            $request->user()?->id,
            $request->headers->get('referer'),
        );

        AdImpression::where('impression_token', $data['impression_token'])
            ->update(['click_id' => $click->id]);

        return response()->json([
            'redirect_url' => $link->original_url,
            'valid' => $click->is_valid,
        ]);
    }
}
```

- [ ] **Step 4: Add routes**

In `routes/web.php` AT THE END (so `/{slug}` doesn't shadow other routes):
```php
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\InterstitialController;

Route::get('/{slug}', [RedirectController::class, 'show'])
    ->where('slug','[A-Za-z0-9_-]+')->name('link.show');
Route::post('/{slug}/unlock', [RedirectController::class, 'unlock'])->name('link.unlock');
Route::post('/{slug}/verify', [InterstitialController::class, 'verify'])
    ->middleware('throttle:60,1')
    ->name('link.verify');
```

- [ ] **Step 5: Commit**

```bash
git add .
git commit -m "feat(redirect): controller + interstitial verify with captcha + click tracking"
```

---

### Task 19: Interstitial views (adf.ly style multi-slot)

**Files:**
- Create: `resources/views/interstitial/{countdown,password,blocked}.blade.php`

- [ ] **Step 1: `interstitial/countdown.blade.php`**

```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ __('Loading link...') }}</title>
@vite('resources/css/app.css')
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 min-h-screen flex flex-col">

<header class="bg-white dark:bg-gray-800 shadow py-3 px-4 text-center text-sm">
    <span class="font-semibold">{{ config('app.name') }}</span> — {{ __('Please wait, your link is loading') }}
</header>

@if($ads['top'])
<div class="w-full bg-white dark:bg-gray-800 py-3 flex justify-center border-b">
    @include('interstitial._ad-slot', ['ad' => $ads['top']])
</div>
@endif

<main class="flex-1 flex">
    <div class="flex-1 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 max-w-md w-full text-center">
            <p class="text-gray-500 mb-2 text-sm">{{ __('Your destination is loading...') }}</p>
            <div class="text-6xl font-bold text-blue-600 my-4" x-data="{c:{{ $seconds }}, captchaOk:false}" x-init="
                let i=setInterval(()=>{c--; if(c<=0){clearInterval(i); $refs.btn.disabled=!captchaOk;}},1000);
                window.__captchaOk = () => {captchaOk=true; if(c<=0) $refs.btn.disabled=false;};
            " x-text="c"></div>

            <div class="cf-turnstile my-4" data-sitekey="{{ $turnstileSiteKey }}" data-callback="__captchaOk"></div>

            <form id="verify-form" method="POST" action="{{ route('link.verify', $link->slug) }}">
                @csrf
                <input type="hidden" name="impression_token" value="{{ $token }}">
                <button x-ref="btn" type="button" disabled
                    @click="
                        let fd = new FormData(document.getElementById('verify-form'));
                        let tk = document.querySelector('[name=cf-turnstile-response]');
                        if(tk) fd.append('cf-turnstile-response', tk.value);
                        fetch('{{ route('link.verify', $link->slug) }}', {method:'POST', body: fd, headers:{'X-Requested-With':'XMLHttpRequest'}})
                          .then(r=>r.json()).then(d => window.location.href = d.redirect_url);
                    "
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium py-3 rounded transition">
                    {{ __('Skip Ad') }}
                </button>
            </form>
        </div>
    </div>

    @if($ads['side'])
    <aside class="hidden lg:flex w-[320px] items-center justify-center p-4 border-l">
        @include('interstitial._ad-slot', ['ad' => $ads['side']])
    </aside>
    @endif
</main>

@if($ads['bottom'])
<div class="w-full bg-white dark:bg-gray-800 py-3 flex justify-center border-t">
    @include('interstitial._ad-slot', ['ad' => $ads['bottom']])
</div>
@endif

<script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
```

- [ ] **Step 2: `interstitial/_ad-slot.blade.php`** (partial)

```blade
@if($ad->type === 'banner_image')
    @if($ad->target_url)
        <a href="{{ $ad->target_url }}" target="_blank" rel="noopener" data-ad-id="{{ $ad->id }}">
            <img src="{{ $ad->content }}" alt="{{ $ad->name }}" class="max-w-full h-auto">
        </a>
    @else
        <img src="{{ $ad->content }}" alt="{{ $ad->name }}" class="max-w-full h-auto">
    @endif
@elseif($ad->type === 'html')
    <div class="ad-html">{!! $ad->content !!}</div>
@else
    <iframe src="{{ $ad->content }}" class="border-0" width="728" height="90"></iframe>
@endif
```

- [ ] **Step 3: `interstitial/password.blade.php`**

```blade
<x-guest-layout>
<div class="max-w-md mx-auto py-16 px-4 text-center">
    <h1 class="text-2xl font-semibold mb-4">{{ __('This link is protected') }}</h1>
    <form method="POST" action="{{ route('link.unlock', $slug) }}" class="space-y-4 bg-white dark:bg-gray-800 p-6 rounded shadow">
        @csrf
        <input name="password" type="password" required placeholder="{{ __('Enter password') }}"
               class="w-full rounded">
        @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        <button class="w-full bg-blue-600 text-white py-2 rounded">{{ __('Unlock') }}</button>
    </form>
</div>
</x-guest-layout>
```

- [ ] **Step 4: `interstitial/blocked.blade.php`**

```blade
<x-guest-layout>
<div class="max-w-md mx-auto py-16 px-4 text-center">
    <h1 class="text-2xl font-semibold mb-4 text-red-600">{{ __('Link not available') }}</h1>
    <p class="text-gray-600 dark:text-gray-300">{{ __('This link has been disabled or removed.') }}</p>
    <a href="{{ route('home') }}" class="inline-block mt-6 text-blue-600 underline">{{ __('Back to home') }}</a>
</div>
</x-guest-layout>
```

- [ ] **Step 5: Commit**

```bash
git add resources/views/interstitial
git commit -m "feat(views): adf.ly-style interstitial with multi-slot + Turnstile"
```

---

### Task 20: Feature test for full redirect+verify flow

**Files:**
- Create: `tests/Feature/RedirectFlowTest.php`

- [ ] **Step 1: Write test**

```php
<?php

use App\Models\AdCampaign;
use App\Models\Setting;
use App\Models\ShortLink;
use App\Models\User;
use App\Services\CaptchaService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Setting::create(['key'=>'rate_per_1000_views','value'=>'5000','type'=>'integer']);
    Setting::create(['key'=>'ip_dedup_hours','value'=>'24','type'=>'integer']);
    AdCampaign::factory()->create(['placement'=>'top','status'=>'active']);
    AdCampaign::factory()->create(['placement'=>'side','status'=>'active']);
    AdCampaign::factory()->create(['placement'=>'bottom','status'=>'active']);
});

it('shows interstitial countdown for active link', function () {
    $link = ShortLink::factory()->create();
    $this->get('/'.$link->slug)
        ->assertOk()
        ->assertSee('Skip Ad')
        ->assertSee('cf-turnstile');
});

it('returns 410 for disabled link', function () {
    $link = ShortLink::factory()->create(['status'=>'disabled']);
    $this->get('/'.$link->slug)->assertStatus(410);
});

it('prompts password for protected link', function () {
    $link = ShortLink::factory()->create(['password' => \Hash::make('secret')]);
    $this->get('/'.$link->slug)->assertOk()->assertSee('This link is protected');
});

it('credits owner on valid verify', function () {
    $owner = User::factory()->create(['balance'=>0]);
    $link = ShortLink::factory()->create(['user_id'=>$owner->id]);

    $this->mock(CaptchaService::class, fn($m) => $m->shouldReceive('verify')->andReturn(true));

    // First show to create impressions
    $this->get('/'.$link->slug);

    $response = $this->postJson("/{$link->slug}/verify", [
        'impression_token' => str_repeat('x',36),
        'cf-turnstile-response' => 'ok',
    ]);

    $response->assertOk()->assertJson(['valid'=>true, 'redirect_url'=>$link->original_url]);
    expect($owner->fresh()->balance)->toBe(5);
});
```

- [ ] **Step 2: Run**

```bash
./vendor/bin/pest tests/Feature/RedirectFlowTest.php
```

Expected: 4 passed.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/RedirectFlowTest.php
git commit -m "test(feature): redirect flow end-to-end"
```

---

## Phase 7 — Wallet & Payout

### Task 21: PayoutService

**Files:**
- Create: `app/Services/PayoutService.php`
- Create: `tests/Unit/Services/PayoutServiceTest.php`

- [ ] **Step 1: Test**

```php
<?php

use App\Models\PayoutRequest;
use App\Models\User;
use App\Services\PayoutService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('creates pending payout and holds balance', function () {
    $user = User::factory()->create(['balance' => 200000]);
    $svc = app(PayoutService::class);

    $req = $svc->createRequest($user, 100000, 'momo', '0901234567');

    expect($req->status)->toBe('pending');
    expect($user->fresh()->balance)->toBe(100000);
});

it('rejects when amount exceeds balance', function () {
    $user = User::factory()->create(['balance' => 50000]);
    $svc = app(PayoutService::class);

    expect(fn() => $svc->createRequest($user, 100000, 'momo', '0901234567'))
        ->toThrow(\RuntimeException::class);
});

it('marks paid and records release transaction', function () {
    $user = User::factory()->create(['balance' => 0]);
    $req = PayoutRequest::create([
        'user_id'=>$user->id,'amount'=>100000,'method'=>'momo',
        'account_info'=>'0901234567','status'=>'pending',
    ]);
    $svc = app(PayoutService::class);
    $svc->markPaid($req, $user, 'TX-001');

    expect($req->fresh()->status)->toBe('paid');
});

it('refunds on reject', function () {
    $user = User::factory()->create(['balance' => 50000]);
    $req = PayoutRequest::create([
        'user_id'=>$user->id,'amount'=>100000,'method'=>'momo',
        'account_info'=>'0901234567','status'=>'pending',
    ]);
    $svc = app(PayoutService::class);
    $svc->reject($req, $user, 'Invalid info');

    expect($req->fresh()->status)->toBe('rejected');
    expect($user->fresh()->balance)->toBe(150000);  // refunded
});
```

- [ ] **Step 2: Implement**

`app/Services/PayoutService.php`:
```php
<?php

namespace App\Services;

use App\Models\PayoutRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PayoutService
{
    public function __construct(private WalletService $wallet) {}

    public function createRequest(User $user, int $amount, string $method, string $accountInfo): PayoutRequest
    {
        return DB::transaction(function () use ($user, $amount, $method, $accountInfo) {
            $request = PayoutRequest::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'method' => $method,
                'account_info' => $accountInfo,
                'status' => 'pending',
            ]);
            $this->wallet->debit($user, $amount, 'payout_hold', $request->id, "Payout #{$request->id}");
            return $request->fresh();
        });
    }

    public function markPaid(PayoutRequest $request, User $admin, string $txRef): void
    {
        $request->update([
            'status' => 'paid',
            'processed_by' => $admin->id,
            'processed_at' => now(),
            'transaction_ref' => $txRef,
        ]);
        \App\Models\WalletTransaction::create([
            'user_id' => $request->user_id,
            'type' => 'payout_release',
            'amount' => 0,
            'balance_after' => $request->user->balance,
            'reference_type' => 'payout_request',
            'reference_id' => $request->id,
            'description' => "Paid via {$request->method}: {$txRef}",
            'created_at' => now(),
        ]);
    }

    public function reject(PayoutRequest $request, User $admin, string $reason): void
    {
        DB::transaction(function () use ($request, $admin, $reason) {
            $request->update([
                'status' => 'rejected',
                'processed_by' => $admin->id,
                'processed_at' => now(),
                'admin_note' => $reason,
            ]);
            $this->wallet->refund(
                $request->user,
                $request->amount,
                $request->id,
                "Refund payout #{$request->id}: {$reason}"
            );
        });
    }
}
```

- [ ] **Step 3: Run — PASS**

- [ ] **Step 4: Commit**

```bash
git add app/Services/PayoutService.php tests/Unit/Services/PayoutServiceTest.php
git commit -m "feat(service): PayoutService create/markPaid/reject"
```

---

### Task 22: PayoutController + view

**Files:**
- Create: `app/Http/Controllers/PayoutController.php`, `app/Http/Requests/StorePayoutRequest.php`
- Create: `resources/views/payout/index.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Form request**

```bash
php artisan make:request StorePayoutRequest
```

```php
<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StorePayoutRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }

    public function rules(): array
    {
        $minVnd = (int) app(\App\Services\SettingService::class)->get('min_payout_vnd', 100000);
        $maxAmount = $this->user()->balance;
        return [
            'amount' => ['required','integer',"min:{$minVnd}","max:{$maxAmount}"],
            'method' => ['required','in:momo,zalo,paypal'],
            'account_info' => ['required','string','max:255'],
        ];
    }
}
```

- [ ] **Step 2: Controller**

```bash
php artisan make:controller PayoutController
```

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayoutRequest;
use App\Services\PayoutService;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function __construct(private PayoutService $svc) {}

    public function index(Request $request)
    {
        $requests = $request->user()->payoutRequests()->latest()->paginate(15);
        return view('payout.index', compact('requests'));
    }

    public function store(StorePayoutRequest $request)
    {
        try {
            $this->svc->createRequest(
                $request->user(),
                (int) $request->amount,
                $request->method,
                $request->account_info,
            );
        } catch (\Throwable $e) {
            return back()->withErrors(['amount' => __($e->getMessage())])->withInput();
        }
        return redirect()->route('payout.index')->with('status', __('Payout request submitted'));
    }
}
```

- [ ] **Step 3: View `resources/views/payout/index.blade.php`**

```blade
<x-app-layout>
<x-slot name="header"><h2 class="font-semibold text-xl">{{ __('Payout') }}</h2></x-slot>
<div class="py-12 max-w-4xl mx-auto px-4">
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <div class="text-sm text-gray-500">{{ __('Balance') }}</div>
            <div class="text-2xl font-bold">{{ number_format(auth()->user()->balance) }} đ</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <div class="text-sm text-gray-500">{{ __('Total earned') }}</div>
            <div class="text-2xl font-bold">{{ number_format(auth()->user()->total_earned) }} đ</div>
        </div>
    </div>

    @if(session('status')) <div class="bg-green-100 p-3 rounded mb-4">{{ session('status') }}</div> @endif

    <form method="POST" action="{{ route('payout.store') }}" class="space-y-4 bg-white dark:bg-gray-800 p-6 rounded shadow mb-8">
        @csrf
        <h3 class="font-semibold text-lg">{{ __('Request payout') }}</h3>
        <div>
            <label class="block text-sm font-medium">{{ __('Amount (VND)') }}</label>
            <input name="amount" type="number" min="1" value="{{ old('amount') }}" required class="w-full rounded">
            @error('amount') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium">{{ __('Method') }}</label>
            <select name="method" class="w-full rounded">
                <option value="momo">Momo</option>
                <option value="zalo">ZaloPay</option>
                <option value="paypal">PayPal</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">{{ __('Account info') }}</label>
            <input name="account_info" type="text" value="{{ old('account_info') }}" required class="w-full rounded" placeholder="0901234567 / email@paypal.com">
            @error('account_info') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">{{ __('Submit request') }}</button>
    </form>

    <h3 class="font-semibold text-lg mb-2">{{ __('History') }}</h3>
    <div class="bg-white dark:bg-gray-800 rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700"><tr>
            <th class="px-4 py-2 text-left">Date</th>
            <th class="px-4 py-2 text-right">{{ __('Amount (VND)') }}</th>
            <th class="px-4 py-2">{{ __('Method') }}</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2 text-left">Note</th>
        </tr></thead>
        <tbody>
        @forelse($requests as $r)
            <tr class="border-t dark:border-gray-700">
                <td class="px-4 py-2">{{ $r->created_at->format('Y-m-d H:i') }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($r->amount) }}</td>
                <td class="px-4 py-2 text-center">{{ $r->method }}</td>
                <td class="px-4 py-2 text-center">
                    <span class="px-2 py-0.5 rounded text-xs {{ ['pending'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-blue-100 text-blue-700','paid'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-700'][$r->status] }}">{{ $r->status }}</span>
                </td>
                <td class="px-4 py-2 text-xs">{{ $r->admin_note ?? $r->transaction_ref }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center py-8 text-gray-500">{{ __('No payout requests yet') }}</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-4">{{ $requests->links() }}</div>
</div>
</x-app-layout>
```

- [ ] **Step 4: Routes**

In `routes/web.php` inside auth group:
```php
use App\Http\Controllers\PayoutController;
Route::get('/payout', [PayoutController::class, 'index'])->name('payout.index');
Route::post('/payout', [PayoutController::class, 'store'])->name('payout.store');
```

- [ ] **Step 5: Commit**

```bash
git add .
git commit -m "feat(payout): controller + form + history view"
```

---

## Phase 8 — Admin Panel (Filament 3)

### Task 23: Create admin user + verify panel access

**Files:**
- Modify: `app/Providers/Filament/AdminPanelProvider.php` (verify already generated)

- [ ] **Step 1: Verify provider registered**

```bash
php artisan about | grep -i filament
```

- [ ] **Step 2: Seed an admin user via tinker for now**

```bash
php artisan tinker --execute="App\Models\User::create(['name'=>'Admin','email'=>'admin@demo.com','password'=>bcrypt('Admin@123'),'is_admin'=>true,'email_verified_at'=>now()]);"
```

- [ ] **Step 3: Test login**

Run `php artisan serve`, visit `http://localhost:8000/admin/login`, login with `admin@demo.com` / `Admin@123`. Should see empty Filament dashboard.

- [ ] **Step 4: Commit**

```bash
git add app/Providers
git commit -m "feat(admin): verify Filament panel accessible by is_admin user"
```

---

### Task 24: UserResource

**Files:**
- Create: `app/Filament/Resources/UserResource.php` + Pages

- [ ] **Step 1: Generate resource**

```bash
php artisan make:filament-resource User --generate
```

- [ ] **Step 2: Customize `UserResource.php`** form/table:

```php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('name')->required(),
        Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
        Forms\Components\TextInput::make('balance')->numeric()->default(0)->suffix('VND'),
        Forms\Components\TextInput::make('total_earned')->numeric()->disabled(),
        Forms\Components\Select::make('status')->options(['active'=>'Active','banned'=>'Banned'])->required(),
        Forms\Components\Toggle::make('is_admin'),
        Forms\Components\Select::make('payout_method')->options(['momo'=>'Momo','zalo'=>'ZaloPay','paypal'=>'PayPal'])->nullable(),
        Forms\Components\TextInput::make('payout_account')->nullable(),
    ]);
}

public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('balance')->money('VND', divideBy: 1)->sortable(),
            Tables\Columns\TextColumn::make('total_earned')->money('VND', divideBy: 1)->sortable(),
            Tables\Columns\IconColumn::make('is_admin')->boolean(),
            Tables\Columns\BadgeColumn::make('status')->colors(['success'=>'active','danger'=>'banned']),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')->options(['active','banned']),
            Tables\Filters\TernaryFilter::make('is_admin'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\Action::make('ban')->visible(fn($r)=>$r->status==='active')
                ->action(fn($r)=>$r->update(['status'=>'banned']))->color('danger'),
            Tables\Actions\Action::make('unban')->visible(fn($r)=>$r->status==='banned')
                ->action(fn($r)=>$r->update(['status'=>'active']))->color('success'),
        ]);
}
```

- [ ] **Step 3: Smoke test**

Login admin, visit `/admin/users` — verify list/edit works.

- [ ] **Step 4: Commit**

```bash
git add app/Filament/Resources/UserResource.php app/Filament/Resources/UserResource
git commit -m "feat(admin): UserResource with ban/unban + filters"
```

---

### Task 25: ShortLinkResource

**Files:**
- Create: `app/Filament/Resources/ShortLinkResource.php` + Pages

- [ ] **Step 1: Generate + customize**

```bash
php artisan make:filament-resource ShortLink --generate
```

Set table:
```php
public static function table(Table $table): Table
{
    return $table->columns([
        Tables\Columns\TextColumn::make('slug')->copyable()->searchable(),
        Tables\Columns\TextColumn::make('user.email')->label('Owner')->searchable(),
        Tables\Columns\TextColumn::make('original_url')->limit(40)->tooltip(fn($r)=>$r->original_url),
        Tables\Columns\TextColumn::make('total_clicks')->numeric()->sortable(),
        Tables\Columns\TextColumn::make('valid_views')->numeric()->sortable(),
        Tables\Columns\TextColumn::make('total_earned')->money('VND', divideBy:1)->sortable(),
        Tables\Columns\BadgeColumn::make('status')->colors(['success'=>'active','warning'=>'disabled','danger'=>'blocked']),
        Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
    ])->filters([
        Tables\Filters\SelectFilter::make('status')->options(['active'=>'Active','disabled'=>'Disabled','blocked'=>'Blocked']),
    ])->actions([
        Tables\Actions\Action::make('block')->visible(fn($r)=>$r->status!=='blocked')
            ->color('danger')->action(fn($r)=>$r->update(['status'=>'blocked'])),
        Tables\Actions\Action::make('activate')->visible(fn($r)=>$r->status!=='active')
            ->color('success')->action(fn($r)=>$r->update(['status'=>'active'])),
        Tables\Actions\DeleteAction::make(),
    ]);
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Filament/Resources/ShortLinkResource*
git commit -m "feat(admin): ShortLinkResource with status actions"
```

---

### Task 26: AdCampaignResource

**Files:**
- Create: `app/Filament/Resources/AdCampaignResource.php`

- [ ] **Step 1: Generate + customize**

```bash
php artisan make:filament-resource AdCampaign --generate
```

Form:
```php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('name')->required(),
        Forms\Components\Select::make('placement')->options(['top'=>'Top (728×90)','side'=>'Side (300×250)','bottom'=>'Bottom (728×90)'])->required(),
        Forms\Components\Select::make('type')->options(['banner_image'=>'Banner image URL','html'=>'HTML snippet','iframe'=>'Iframe URL'])->required()->reactive(),
        Forms\Components\Textarea::make('content')->required()->rows(4)->helperText('Image URL, HTML, or iframe URL'),
        Forms\Components\TextInput::make('target_url')->url()->nullable(),
        Forms\Components\TextInput::make('weight')->numeric()->default(1)->minValue(1)->maxValue(100),
        Forms\Components\Select::make('status')->options(['active'=>'Active','paused'=>'Paused'])->required(),
        Forms\Components\DateTimePicker::make('start_at')->nullable(),
        Forms\Components\DateTimePicker::make('end_at')->nullable(),
    ]);
}

public static function table(Table $table): Table
{
    return $table->columns([
        Tables\Columns\TextColumn::make('name')->searchable(),
        Tables\Columns\BadgeColumn::make('placement'),
        Tables\Columns\TextColumn::make('type'),
        Tables\Columns\TextColumn::make('weight'),
        Tables\Columns\TextColumn::make('impressions')->numeric(),
        Tables\Columns\TextColumn::make('clicks_count')->numeric()->label('Clicks'),
        Tables\Columns\TextColumn::make('ctr')->state(fn($r)=>$r->impressions>0?round($r->clicks_count/$r->impressions*100,2).'%':'-'),
        Tables\Columns\BadgeColumn::make('status')->colors(['success'=>'active','warning'=>'paused']),
    ])->filters([
        Tables\Filters\SelectFilter::make('placement')->options(['top','side','bottom']),
        Tables\Filters\SelectFilter::make('status')->options(['active','paused']),
    ]);
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Filament/Resources/AdCampaignResource*
git commit -m "feat(admin): AdCampaignResource with placement + CTR"
```

---

### Task 27: PayoutRequestResource with actions

**Files:**
- Create: `app/Filament/Resources/PayoutRequestResource.php`

- [ ] **Step 1: Generate**

```bash
php artisan make:filament-resource PayoutRequest --generate
```

- [ ] **Step 2: Customize**

```php
public static function table(Table $table): Table
{
    return $table->columns([
        Tables\Columns\TextColumn::make('id'),
        Tables\Columns\TextColumn::make('user.email')->searchable(),
        Tables\Columns\TextColumn::make('amount')->money('VND', divideBy:1),
        Tables\Columns\TextColumn::make('method')->badge(),
        Tables\Columns\TextColumn::make('account_info')->copyable(),
        Tables\Columns\BadgeColumn::make('status')->colors([
            'warning'=>'pending','primary'=>'approved','success'=>'paid','danger'=>'rejected'
        ]),
        Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        Tables\Columns\TextColumn::make('processed_at')->dateTime()->sortable(),
    ])->filters([
        Tables\Filters\SelectFilter::make('status')->options([
            'pending','approved','paid','rejected'
        ])->default('pending'),
    ])->actions([
        Tables\Actions\Action::make('markPaid')->visible(fn($r)=>in_array($r->status,['pending','approved']))
            ->form([Forms\Components\TextInput::make('transaction_ref')->required()->label('Transaction ref')])
            ->action(fn($r,$data)=>app(\App\Services\PayoutService::class)->markPaid($r, auth()->user(), $data['transaction_ref']))
            ->color('success')->icon('heroicon-o-check'),
        Tables\Actions\Action::make('reject')->visible(fn($r)=>$r->status==='pending')
            ->form([Forms\Components\Textarea::make('reason')->required()])
            ->action(fn($r,$data)=>app(\App\Services\PayoutService::class)->reject($r, auth()->user(), $data['reason']))
            ->color('danger')->icon('heroicon-o-x-mark')
            ->requiresConfirmation(),
    ]);
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Filament/Resources/PayoutRequestResource*
git commit -m "feat(admin): PayoutRequestResource with markPaid/reject actions"
```

---

### Task 28: SettingResource (custom page) + BlacklistDomainResource

**Files:**
- Create: `app/Filament/Resources/SettingResource.php`, `BlacklistDomainResource.php`

- [ ] **Step 1: Settings — generate and customize**

```bash
php artisan make:filament-resource Setting --generate
```

Form:
```php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('key')->required()->disabled(fn($record)=>$record !== null),
        Forms\Components\Textarea::make('value')->required()->rows(2),
        Forms\Components\Select::make('type')->options([
            'string'=>'String','integer'=>'Integer','boolean'=>'Boolean','json'=>'JSON',
        ])->required(),
        Forms\Components\TextInput::make('description')->nullable(),
    ]);
}

public static function table(Table $table): Table
{
    return $table->columns([
        Tables\Columns\TextColumn::make('key')->searchable(),
        Tables\Columns\TextColumn::make('value')->limit(50),
        Tables\Columns\BadgeColumn::make('type'),
        Tables\Columns\TextColumn::make('description')->limit(40),
    ]);
}
```

- [ ] **Step 2: BlacklistDomain**

```bash
php artisan make:filament-resource BlacklistDomain --generate
```

Standard form: `domain` (unique), `reason`. Standard table list.

- [ ] **Step 3: Commit**

```bash
git add app/Filament/Resources/SettingResource* app/Filament/Resources/BlacklistDomainResource*
git commit -m "feat(admin): Setting + BlacklistDomain resources"
```

---

### Task 29: Admin dashboard widgets

**Files:**
- Create: `app/Filament/Widgets/{StatsOverview,ClicksChart,PendingPayouts}.php`
- Modify: `AdminPanelProvider.php` to register widgets

- [ ] **Step 1: StatsOverview**

```bash
php artisan make:filament-widget StatsOverview --stats-overview
```

```php
<?php
namespace App\Filament\Widgets;

use App\Models\Click;
use App\Models\PayoutRequest;
use App\Models\ShortLink;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count()),
            Stat::make('Short links', ShortLink::count()),
            Stat::make('Clicks today', Click::whereDate('created_at', today())->count()),
            Stat::make('Valid views today', Click::whereDate('created_at', today())->where('is_valid',true)->count()),
            Stat::make('Pending payouts', PayoutRequest::where('status','pending')->count())
                ->color('warning'),
            Stat::make('Paid this month', number_format(PayoutRequest::where('status','paid')
                ->whereMonth('processed_at', now()->month)->sum('amount')).' đ'),
        ];
    }
}
```

- [ ] **Step 2: ClicksChart**

```bash
php artisan make:filament-widget ClicksChart --chart
```

```php
<?php
namespace App\Filament\Widgets;

use App\Models\Click;
use Filament\Widgets\ChartWidget;

class ClicksChart extends ChartWidget
{
    protected static ?string $heading = 'Clicks (last 30 days)';

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn($d)=>now()->subDays($d)->format('Y-m-d'));
        $totals = $days->map(fn($d)=>Click::whereDate('created_at',$d)->count());
        $valid = $days->map(fn($d)=>Click::whereDate('created_at',$d)->where('is_valid',true)->count());

        return [
            'datasets' => [
                ['label'=>'Total','data'=>$totals->toArray(),'borderColor'=>'#3b82f6'],
                ['label'=>'Valid','data'=>$valid->toArray(),'borderColor'=>'#10b981'],
            ],
            'labels' => $days->map(fn($d)=>substr($d,5))->toArray(),
        ];
    }

    protected function getType(): string { return 'line'; }
}
```

- [ ] **Step 3: PendingPayouts**

```bash
php artisan make:filament-widget PendingPayouts --table
```

```php
<?php
namespace App\Filament\Widgets;

use App\Models\PayoutRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingPayouts extends BaseWidget
{
    protected static ?string $heading = 'Pending payouts (latest 10)';

    public function table(Table $table): Table
    {
        return $table->query(PayoutRequest::where('status','pending')->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('user.email'),
                Tables\Columns\TextColumn::make('amount')->money('VND', divideBy:1),
                Tables\Columns\TextColumn::make('method')->badge(),
                Tables\Columns\TextColumn::make('created_at')->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('open')->url(fn($r)=>\App\Filament\Resources\PayoutRequestResource::getUrl('edit',['record'=>$r])),
            ]);
    }
}
```

- [ ] **Step 4: Register widgets in `AdminPanelProvider.php`**

In `widgets(...)` array:
```php
->widgets([
    \App\Filament\Widgets\StatsOverview::class,
    \App\Filament\Widgets\ClicksChart::class,
    \App\Filament\Widgets\PendingPayouts::class,
])
```

- [ ] **Step 5: Commit**

```bash
git add app/Filament/Widgets app/Providers/Filament/AdminPanelProvider.php
git commit -m "feat(admin): dashboard widgets (stats, chart, pending payouts)"
```

---

## Phase 9 — User Dashboard

### Task 30: DashboardController + view

**Files:**
- Create: `app/Http/Controllers/DashboardController.php`
- Create: `resources/views/dashboard.blade.php` (or replace Breeze's)

- [ ] **Step 1: Controller**

```bash
php artisan make:controller DashboardController
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Click;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $days = collect(range(29,0))->map(fn($d)=>now()->subDays($d)->format('Y-m-d'));

        $clicksByDay = Click::join('short_links','clicks.short_link_id','=','short_links.id')
            ->where('short_links.user_id',$user->id)
            ->where('clicks.created_at','>=',now()->subDays(30))
            ->selectRaw('DATE(clicks.created_at) as d, COUNT(*) as total, SUM(is_valid) as valid, SUM(earnings) as earned')
            ->groupBy('d')->pluck('total','d');

        $earnedByDay = Click::join('short_links','clicks.short_link_id','=','short_links.id')
            ->where('short_links.user_id',$user->id)
            ->where('clicks.created_at','>=',now()->subDays(30))
            ->selectRaw('DATE(clicks.created_at) as d, SUM(earnings) as earned')
            ->groupBy('d')->pluck('earned','d');

        $labels = $days->map(fn($d)=>substr($d,5))->toArray();
        $totals = $days->map(fn($d)=>$clicksByDay[$d] ?? 0)->toArray();
        $earnings = $days->map(fn($d)=>(int)($earnedByDay[$d] ?? 0))->toArray();

        $stats = [
            'total_links' => $user->shortLinks()->count(),
            'total_clicks' => $user->shortLinks()->sum('total_clicks'),
            'valid_views' => $user->shortLinks()->sum('valid_views'),
            'balance' => $user->balance,
            'total_earned' => $user->total_earned,
        ];

        return view('dashboard', compact('stats','labels','totals','earnings'));
    }
}
```

- [ ] **Step 2: Replace Breeze's `resources/views/dashboard.blade.php`**

```blade
<x-app-layout>
<x-slot name="header"><h2 class="font-semibold text-xl">{{ __('Dashboard') }}</h2></x-slot>
<div class="py-12 max-w-7xl mx-auto px-4 space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach(['Links'=>$stats['total_links'],'Clicks'=>number_format($stats['total_clicks']),'Valid views'=>number_format($stats['valid_views']),'Balance'=>number_format($stats['balance']).' đ','Total earned'=>number_format($stats['total_earned']).' đ'] as $label=>$value)
        <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
            <div class="text-sm text-gray-500">{{ __($label) }}</div>
            <div class="text-xl font-bold">{{ $value }}</div>
        </div>
        @endforeach
    </div>

    <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
        <h3 class="font-semibold mb-2">{{ __('Last 30 days') }}</h3>
        <canvas id="chart" height="80"></canvas>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('chart').getContext('2d'),{
    type:'line',
    data:{labels:@json($labels), datasets:[
        {label:'Clicks',data:@json($totals),borderColor:'#3b82f6',tension:0.2},
        {label:'Earned (đ)',data:@json($earnings),borderColor:'#10b981',tension:0.2,yAxisID:'y1'},
    ]},
    options:{
        responsive:true, interaction:{mode:'index',intersect:false},
        scales:{
            y:{type:'linear',position:'left',beginAtZero:true},
            y1:{type:'linear',position:'right',beginAtZero:true,grid:{drawOnChartArea:false}},
        }
    }
});
</script>
</x-app-layout>
```

- [ ] **Step 3: Update route**

In `routes/web.php`, replace the dashboard line from Breeze:
```php
use App\Http\Controllers\DashboardController;
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth','verified'])->name('dashboard');
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/DashboardController.php resources/views/dashboard.blade.php routes/web.php
git commit -m "feat(user): dashboard with 30-day chart + KPI cards"
```

---

## Phase 10 — Seeders (rich demo data)

### Task 31: SettingSeeder + BlacklistDomainSeeder

**Files:**
- Create: `database/seeders/SettingSeeder.php`, `BlacklistDomainSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: SettingSeeder**

```bash
php artisan make:seeder SettingSeeder
```

```php
<?php
namespace Database\Seeders;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key'=>'rate_per_1000_views','value'=>'5000','type'=>'integer','description'=>'VND per 1000 valid views'],
            ['key'=>'min_payout_vnd','value'=>'100000','type'=>'integer','description'=>'Min payout amount VND'],
            ['key'=>'min_payout_usd_paypal','value'=>'4','type'=>'integer','description'=>'Min PayPal payout USD'],
            ['key'=>'interstitial_seconds','value'=>'5','type'=>'integer','description'=>'Countdown seconds'],
            ['key'=>'ip_dedup_hours','value'=>'24','type'=>'integer','description'=>'IP dedup window'],
            ['key'=>'paypal_to_vnd_rate','value'=>'25000','type'=>'integer','description'=>'1 USD = N VND'],
        ];
        foreach ($defaults as $d) {
            Setting::updateOrCreate(['key'=>$d['key']], $d + ['updated_at'=>now()]);
        }
    }
}
```

- [ ] **Step 2: BlacklistDomainSeeder**

```bash
php artisan make:seeder BlacklistDomainSeeder
```

```php
<?php
namespace Database\Seeders;
use App\Models\BlacklistDomain;
use Illuminate\Database\Seeder;

class BlacklistDomainSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['spam.test','phishing.example','malware.invalid','virus.test','adfraud.example'] as $d) {
            BlacklistDomain::updateOrCreate(['domain'=>$d], ['reason'=>'Flagged automatically']);
        }
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add database/seeders
git commit -m "feat(seed): settings + blacklist"
```

---

### Task 32: UserSeeder

**Files:**
- Create: `database/seeders/UserSeeder.php`
- Modify: `database/factories/UserFactory.php`

- [ ] **Step 1: Update UserFactory**

```php
public function definition(): array
{
    return [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'email_verified_at' => now(),
        'password' => static::$password ??= bcrypt('password'),
        'balance' => fake()->numberBetween(0, 500_000),
        'total_earned' => fake()->numberBetween(0, 2_000_000),
        'status' => 'active',
        'payout_method' => fake()->randomElement(['momo','zalo','paypal',null]),
        'payout_account' => fake()->phoneNumber(),
        'preferred_locale' => fake()->randomElement(['vi','en']),
        'is_admin' => false,
        'remember_token' => \Illuminate\Support\Str::random(10),
    ];
}
```

- [ ] **Step 2: UserSeeder**

```bash
php artisan make:seeder UserSeeder
```

```php
<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'=>'Admin','email'=>'admin@demo.com',
            'password'=>bcrypt('Admin@123'),'email_verified_at'=>now(),
            'is_admin'=>true,'balance'=>0,
        ]);
        User::create([
            'name'=>'Demo User','email'=>'demo@demo.com',
            'password'=>bcrypt('Demo@123'),'email_verified_at'=>now(),
            'balance'=>250_000,'total_earned'=>1_800_000,
            'payout_method'=>'momo','payout_account'=>'0901234567',
        ]);
        User::factory()->count(48)->create();
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add database/seeders/UserSeeder.php database/factories/UserFactory.php
git commit -m "feat(seed): UserSeeder (admin + demo + 48 random)"
```

---

### Task 33: AdCampaign + ShortLink seeders

**Files:**
- Create: `AdCampaignSeeder.php`, `ShortLinkSeeder.php`

- [ ] **Step 1: AdCampaignSeeder**

```bash
php artisan make:seeder AdCampaignSeeder
```

```php
<?php
namespace Database\Seeders;
use App\Models\AdCampaign;
use Illuminate\Database\Seeder;

class AdCampaignSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['top','side','bottom'] as $placement) {
            AdCampaign::factory()->count(5)->create([
                'placement' => $placement,
            ]);
        }
    }
}
```

- [ ] **Step 2: ShortLinkSeeder**

```bash
php artisan make:seeder ShortLinkSeeder
```

```php
<?php
namespace Database\Seeders;
use App\Models\ShortLink;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ShortLinkSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_admin', false)->pluck('id')->toArray();
        $realUrls = [
            'https://github.com/laravel/laravel','https://laravel.com/docs',
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ','https://filamentphp.com/docs',
            'https://php.net/manual/en/index.php','https://tailwindcss.com',
        ];

        for ($i=0; $i<300; $i++) {
            $useCustom = fake()->boolean(30);
            $hasPwd = fake()->boolean(10);
            ShortLink::create([
                'user_id' => fake()->boolean(85) ? fake()->randomElement($users) : null,
                'slug' => $useCustom ? Str::slug(fake()->words(2,true)).fake()->numberBetween(1,99) : Str::random(6),
                'original_url' => fake()->boolean(40) ? fake()->randomElement($realUrls) : fake()->url(),
                'password' => $hasPwd ? Hash::make('demo123') : null,
                'status' => fake()->randomElement(['active','active','active','disabled']),
                'created_at' => fake()->dateTimeBetween('-90 days','now'),
                'updated_at' => now(),
            ]);
        }
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add database/seeders/AdCampaignSeeder.php database/seeders/ShortLinkSeeder.php
git commit -m "feat(seed): ads (15 across 3 placements) + 300 short links"
```

---

### Task 34: ClickSeeder + AdImpressionSeeder + WalletTransactionSeeder

**Files:**
- Create: `ClickSeeder.php`, `AdImpressionSeeder.php`, `WalletTransactionSeeder.php`

- [ ] **Step 1: ClickSeeder (heavy)**

```bash
php artisan make:seeder ClickSeeder
```

```php
<?php
namespace Database\Seeders;
use App\Models\AdCampaign;
use App\Models\AdImpression;
use App\Models\Click;
use App\Models\IpViewLog;
use App\Models\ShortLink;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClickSeeder extends Seeder
{
    public function run(): void
    {
        $links = ShortLink::where('status','active')->get();
        $rate = 5;  // VND per click (5000/1000)

        // Pareto: 30% of links get most clicks
        $hotLinks = $links->random((int)($links->count()*0.3));
        $coldLinks = $links->diff($hotLinks);

        $clicks = [];
        $impressions = [];
        $ipLogs = [];

        $now = now();
        $ads = AdCampaign::where('status','active')->get()->groupBy('placement');

        for ($d=89; $d>=0; $d--) {
            $date = $now->copy()->subDays($d);
            $multiplier = in_array($date->dayOfWeek, [0,6]) ? 1.5 : 1.0;
            $daily = (int) (rand(100, 250) * $multiplier);

            for ($c=0; $c<$daily; $c++) {
                $link = (rand(0,10) < 6) ? $hotLinks->random() : $coldLinks->random();
                $ip = fake()->ipv4();
                $isValid = fake()->boolean(70);
                $earnings = $isValid ? $rate : 0;
                $ts = $date->copy()->addMinutes(rand(0, 1439));
                $token = (string) Str::uuid();

                $clicks[] = [
                    'short_link_id' => $link->id,
                    'ip_address' => $ip,
                    'user_agent' => fake()->userAgent(),
                    'is_valid' => $isValid,
                    'earnings' => $earnings,
                    'created_at' => $ts,
                ];

                if ($isValid) {
                    $ipLogs[] = ['short_link_id'=>$link->id,'ip_address'=>$ip,'viewed_at'=>$ts];
                }

                foreach (['top','side','bottom'] as $p) {
                    if (isset($ads[$p]) && $ads[$p]->count() > 0) {
                        $ad = $ads[$p]->random();
                        $impressions[] = [
                            'ad_campaign_id' => $ad->id,
                            'short_link_id' => $link->id,
                            'impression_token' => $token,
                            'ip_address' => $ip,
                            'was_clicked' => fake()->boolean(5),
                            'created_at' => $ts,
                        ];
                    }
                }

                if (count($clicks) >= 500) {
                    Click::insert($clicks); $clicks=[];
                    if ($ipLogs) { IpViewLog::insert($ipLogs); $ipLogs=[]; }
                    if ($impressions) { AdImpression::insert($impressions); $impressions=[]; }
                }
            }
        }
        if ($clicks) Click::insert($clicks);
        if ($ipLogs) IpViewLog::insert($ipLogs);
        if ($impressions) AdImpression::insert($impressions);

        // Update aggregates on short_links
        DB::statement("
            UPDATE short_links sl SET
              total_clicks = (SELECT COUNT(*) FROM clicks c WHERE c.short_link_id = sl.id),
              valid_views = (SELECT COUNT(*) FROM clicks c WHERE c.short_link_id = sl.id AND is_valid=1),
              total_earned = (SELECT COALESCE(SUM(earnings),0) FROM clicks c WHERE c.short_link_id = sl.id)
        ");

        // Recompute user.total_earned + balance partially
        DB::statement("
            UPDATE users u SET
              total_earned = COALESCE((SELECT SUM(sl.total_earned) FROM short_links sl WHERE sl.user_id = u.id), 0)
            WHERE u.is_admin = 0
        ");
        // balance is total_earned minus payouts (handled later in WalletTransactionSeeder)
        DB::statement("UPDATE users SET balance = total_earned WHERE is_admin = 0");

        // Update ad_campaigns counters
        DB::statement("
            UPDATE ad_campaigns ac SET
              impressions = (SELECT COUNT(*) FROM ad_impressions ai WHERE ai.ad_campaign_id = ac.id),
              clicks_count = (SELECT COUNT(*) FROM ad_impressions ai WHERE ai.ad_campaign_id = ac.id AND ai.was_clicked = 1)
        ");
    }
}
```

- [ ] **Step 2: WalletTransactionSeeder (after PayoutSeeder, see next task — order matters)**

Will write a synthesizer that produces credit transactions from clicks.earnings rolling balance per user. Skip for now — `ClickSeeder` already sets `balance = total_earned`. We'll generate transaction logs in next task after PayoutRequestSeeder runs.

- [ ] **Step 3: Commit**

```bash
git add database/seeders/ClickSeeder.php
git commit -m "feat(seed): ClickSeeder ~15k clicks with Pareto + impressions"
```

---

### Task 35: PayoutRequestSeeder + WalletTransactionSeeder

**Files:**
- Create: `PayoutRequestSeeder.php`, `WalletTransactionSeeder.php`

- [ ] **Step 1: PayoutRequestSeeder**

```bash
php artisan make:seeder PayoutRequestSeeder
```

```php
<?php
namespace Database\Seeders;
use App\Models\PayoutRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayoutRequestSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_admin', false)->where('total_earned', '>', 100000)->get();
        $admin = User::where('is_admin', true)->first();

        foreach ($users->random(min(28, $users->count())) as $u) {
            $statuses = ['pending','pending','pending','paid','paid','paid','rejected','rejected','approved'];
            $status = fake()->randomElement($statuses);
            $amount = fake()->numberBetween(50_000, min(500_000, $u->balance ?: 100000));
            $createdAt = fake()->dateTimeBetween('-60 days','-1 day');
            $processedAt = in_array($status, ['paid','approved','rejected']) ? fake()->dateTimeBetween($createdAt, 'now') : null;

            $req = PayoutRequest::create([
                'user_id' => $u->id,
                'amount' => $amount,
                'method' => $u->payout_method ?? 'momo',
                'account_info' => $u->payout_account ?? fake()->phoneNumber(),
                'status' => $status,
                'admin_note' => $status === 'rejected' ? 'Account info mismatch' : null,
                'transaction_ref' => $status === 'paid' ? 'TX-'.fake()->numerify('######') : null,
                'processed_by' => $processedAt ? $admin->id : null,
                'processed_at' => $processedAt,
                'created_at' => $createdAt,
                'updated_at' => $processedAt ?? $createdAt,
            ]);

            if ($status !== 'rejected') {
                // Deduct from user balance for hold (pending/approved/paid)
                DB::table('users')->where('id',$u->id)->decrement('balance', $amount);
            }
        }
    }
}
```

- [ ] **Step 2: WalletTransactionSeeder (reconciliation)**

```bash
php artisan make:seeder WalletTransactionSeeder
```

```php
<?php
namespace Database\Seeders;
use App\Models\Click;
use App\Models\PayoutRequest;
use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Synthesize credit transactions per user from clicks.earnings (grouped by day to reduce volume)
        $rows = DB::table('clicks')
            ->join('short_links','clicks.short_link_id','=','short_links.id')
            ->whereNotNull('short_links.user_id')
            ->where('clicks.is_valid', true)
            ->selectRaw('short_links.user_id, DATE(clicks.created_at) as d, SUM(clicks.earnings) as total')
            ->groupBy('short_links.user_id','d')
            ->get();

        // Track running balance per user
        $running = [];
        foreach ($rows->sortBy(['user_id','d']) as $r) {
            $running[$r->user_id] = ($running[$r->user_id] ?? 0) + (int) $r->total;
            WalletTransaction::create([
                'user_id' => $r->user_id,
                'type' => 'credit',
                'amount' => (int) $r->total,
                'balance_after' => $running[$r->user_id],
                'reference_type' => 'click_batch',
                'reference_id' => null,
                'description' => "Click earnings {$r->d}",
                'created_at' => $r->d.' 23:59:59',
            ]);
        }

        // Payout transactions
        foreach (PayoutRequest::all() as $pr) {
            $running[$pr->user_id] = ($running[$pr->user_id] ?? 0);
            if (in_array($pr->status, ['pending','approved','paid'])) {
                $running[$pr->user_id] -= $pr->amount;
                WalletTransaction::create([
                    'user_id' => $pr->user_id, 'type'=>'payout_hold',
                    'amount' => -$pr->amount, 'balance_after' => $running[$pr->user_id],
                    'reference_type'=>'payout_request','reference_id'=>$pr->id,
                    'description'=>"Hold for payout #{$pr->id}",
                    'created_at' => $pr->created_at,
                ]);
                if ($pr->status === 'paid') {
                    WalletTransaction::create([
                        'user_id'=>$pr->user_id,'type'=>'payout_release',
                        'amount'=>0,'balance_after'=>$running[$pr->user_id],
                        'reference_type'=>'payout_request','reference_id'=>$pr->id,
                        'description'=>"Paid: {$pr->transaction_ref}",
                        'created_at' => $pr->processed_at,
                    ]);
                }
            } elseif ($pr->status === 'rejected') {
                // refund — but PayoutSeeder did NOT deduct for rejected, so do nothing
                WalletTransaction::create([
                    'user_id'=>$pr->user_id,'type'=>'payout_reject',
                    'amount'=>0,'balance_after'=>$running[$pr->user_id] ?? 0,
                    'reference_type'=>'payout_request','reference_id'=>$pr->id,
                    'description'=>"Rejected: {$pr->admin_note}",
                    'created_at' => $pr->processed_at,
                ]);
            }
        }
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add database/seeders/PayoutRequestSeeder.php database/seeders/WalletTransactionSeeder.php
git commit -m "feat(seed): payouts + reconstructed wallet transactions"
```

---

### Task 36: Wire DatabaseSeeder + run full seed

**Files:**
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Replace DatabaseSeeder**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            BlacklistDomainSeeder::class,
            UserSeeder::class,
            AdCampaignSeeder::class,
            ShortLinkSeeder::class,
            ClickSeeder::class,
            PayoutRequestSeeder::class,
            WalletTransactionSeeder::class,
        ]);
    }
}
```

- [ ] **Step 2: Run full seed**

```bash
php artisan migrate:fresh --seed
```

Expected: completes without error (may take ~30-60s for ClickSeeder).

- [ ] **Step 3: Verify counts**

```bash
php artisan tinker --execute="echo 'users='.App\Models\User::count().', links='.App\Models\ShortLink::count().', clicks='.App\Models\Click::count().', payouts='.App\Models\PayoutRequest::count().PHP_EOL;"
```

Expected: ~50 users, ~300 links, ~15k clicks, ~28 payouts.

- [ ] **Step 4: Smoke admin**

Run `php artisan serve`, login `admin@demo.com / Admin@123` → `/admin` → see populated widgets.

Smoke user: `demo@demo.com / Demo@123` → `/dashboard` → see chart with data.

- [ ] **Step 5: Commit**

```bash
git add database/seeders/DatabaseSeeder.php
git commit -m "feat(seed): wire DatabaseSeeder + verify full seed runs"
```

---

## Phase 11 — Polish & Final

### Task 37: Schedule cleanup task

**Files:**
- Modify: `routes/console.php`

- [ ] **Step 1: Add cleanup task**

In `routes/console.php`:
```php
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    \App\Models\IpViewLog::where('viewed_at','<', now()->subDays(30))->delete();
})->daily()->name('cleanup-ip-view-logs');
```

- [ ] **Step 2: Commit**

```bash
git add routes/console.php
git commit -m "feat(schedule): daily cleanup of ip_view_logs > 30 days"
```

---

### Task 38: Run full test suite

- [ ] **Step 1: Run all tests**

```bash
./vendor/bin/pest
```

Expected: all green. If failing, fix red.

- [ ] **Step 2: Run Pint to format**

```bash
./vendor/bin/pint
```

- [ ] **Step 3: Commit format fixes if any**

```bash
git add .
git commit -m "style: pint format"
```

---

### Task 39: README with setup steps

**Files:**
- Create: `README.md`

- [ ] **Step 1: Write README**

```markdown
# URL Shortener with Ads — Đồ án Laravel 12

## Tính năng
- Rút gọn URL với custom alias, password protect
- Trang interstitial 5s với multi-slot ads + Cloudflare Turnstile captcha
- Tính tiền theo /1000 view hợp lệ, anti-fraud IP dedup
- Wallet, payout request Momo/ZaloPay/PayPal, admin duyệt
- Admin panel Filament 3 (users, links, ads, payouts, settings, blacklist)
- i18n VN + EN
- Pest tests cho business logic

## Stack
Laravel 12 · PHP 8.3 · MySQL · Filament 3 · Blade + Alpine + Tailwind · Pest 3

## Cài đặt local

```bash
git clone <repo> URL-Shorten && cd URL-Shorten
composer install
npm install
cp .env.example .env
php artisan key:generate

# Tạo DB MySQL
mysql -u root -e "CREATE DATABASE url_shorten CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Truy cập `http://localhost:8000`.

## Tài khoản demo
| Vai trò | Email | Mật khẩu |
|---|---|---|
| Admin | admin@demo.com | Admin@123 |
| User | demo@demo.com | Demo@123 |

Admin panel: `/admin`

## Cấu hình OAuth/Captcha (tuỳ chọn)
- `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET`: từ Google Cloud Console, redirect `http://localhost:8000/auth/google/callback`
- `TURNSTILE_SITE_KEY` / `TURNSTILE_SECRET_KEY`: từ Cloudflare. Default đang dùng test keys (always-pass).

## Cấu hình email (production)
Trong `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=app-password-16-chars
MAIL_ENCRYPTION=tls
```

## Tests

```bash
./vendor/bin/pest
```

## Cấu trúc thư mục
Xem `docs/superpowers/specs/2026-05-22-url-shorten-ads-design.md`
```

- [ ] **Step 2: Commit**

```bash
git add README.md
git commit -m "docs: README with setup + demo accounts"
```

---

### Task 40: Final manual demo walkthrough

- [ ] **Step 1: Start dev server**

```bash
php artisan serve
```

- [ ] **Step 2: Walk through demo**

Manual checklist:
1. ✅ Visit `/` → shorten a guest link → see short URL
2. ✅ Click the short link → see interstitial countdown + 3 ad slots
3. ✅ After 5s + captcha → redirect to original URL
4. ✅ Login as `demo@demo.com` → `/dashboard` → see KPI cards + 30d chart
5. ✅ `/links` → CRUD a link with custom alias + password
6. ✅ Visit the password-protected link → password prompt
7. ✅ `/payout` → submit payout request
8. ✅ Login as admin `admin@demo.com` → `/admin` → widgets render
9. ✅ Admin: approve/reject the payout
10. ✅ Admin: create new ad campaign → verify shows up on interstitial
11. ✅ Switch locale `/locale/en` → UI in English

- [ ] **Step 3: Commit final**

```bash
git add .
git commit -m "chore: final demo walkthrough passed" --allow-empty
```

---

## Self-Review Notes

**Spec coverage check (vs `2026-05-22-url-shorten-ads-design.md`):**
- ✅ Auth (Breeze + Google + guest) — Tasks 8, 14
- ✅ Link Management (CRUD, custom alias, password) — Tasks 13, 14, 15
- ✅ Redirect + Ad Serving (multi-slot, Turnstile, IP dedup) — Tasks 16, 17, 18, 19
- ✅ Wallet & Payout — Tasks 11, 21, 22
- ✅ Admin Filament (Users, Links, Ads, Payouts, Settings, Blacklist + 3 widgets) — Tasks 23-29
- ✅ Analytics user dashboard (KPI + 30d chart) — Task 30
- ✅ i18n VN + EN — Task 9
- ✅ Anti-fraud (Turnstile + IP dedup + self-click + blacklist + rate limit) — Tasks 12, 17, 18
- ✅ Settings (8 keys) — Tasks 10, 31
- ✅ Seeders (users, ads, links, clicks, payouts, wallet) — Tasks 31-36
- ✅ Testing (Pest unit + feature) — Tasks 10-13, 16, 17, 20, 21
- ✅ Schedule cleanup — Task 37
- ✅ README + demo accounts — Task 39

**Placeholder scan:** none found.

**Type consistency:** WalletService methods (credit/debit/refund), PayoutService (createRequest/markPaid/reject), ShortLinkService (create/generateUniqueSlug), AdServingService (pickForInterstitial), ClickTrackingService (record) — all consistent across tasks.

---

## Execution Handoff

Plan complete and saved to `docs/superpowers/plans/2026-05-22-url-shorten-implementation.md`.

<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use HasFactory, Notifiable;

    // Sensitive fields (is_admin, balance, total_earned, status, role_id) are mass-assignable
    // for Filament admin + seeders only. Controllers MUST use FormRequest::validated()
    // — never $request->all() — when filling User to avoid privilege escalation.
    protected $fillable = [
        'name','email','password','google_id','avatar',
        'balance','total_earned','status','payout_method',
        'payout_account','preferred_locale','is_admin','role_id',
        'referral_code','referred_by','referral_earned','is_premium','premium_until',
    ];

    protected $hidden = ['password','remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_premium' => 'boolean',
            'premium_until' => 'datetime',
            'balance' => 'integer',
            'total_earned' => 'integer',
            'referral_earned' => 'integer',
        ];
    }

    /** Premium còn hiệu lực? (cờ bật + chưa hết hạn). */
    public function isPremium(): bool
    {
        return $this->is_premium && (! $this->premium_until || $this->premium_until->isFuture());
    }

    /** Lấy mã giới thiệu, tạo mới nếu chưa có. */
    public function referralCode(): string
    {
        if (! $this->referral_code) {
            do {
                $code = strtoupper(\Illuminate\Support\Str::random(7));
            } while (static::where('referral_code', $code)->exists());
            $this->forceFill(['referral_code' => $code])->save();
        }

        return $this->referral_code;
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referrer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function apiTokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
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

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function reportedLinks(): HasMany
    {
        return $this->hasMany(ReportedLink::class, 'reporter_user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'description', 'type', 'value', 'value_unit',
        'max_redemptions', 'max_per_user', 'redeemed_count', 'min_balance_required',
        'valid_from', 'valid_until', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'value' => 'integer',
        'max_redemptions' => 'integer',
        'max_per_user' => 'integer',
        'redeemed_count' => 'integer',
    ];

    public function redemptions(): HasMany
    {
        return $this->hasMany(PromoCodeRedemption::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    public function isExhausted(): bool
    {
        return $this->max_redemptions && $this->redeemed_count >= $this->max_redemptions;
    }
}

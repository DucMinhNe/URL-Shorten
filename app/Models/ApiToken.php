<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    protected $fillable = ['user_id', 'name', 'token', 'last_used_at'];

    protected $casts = ['last_used_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generate(): string
    {
        return 'lp_'.Str::random(48);
    }

    /** Băm token để lưu/đối chiếu (không bao giờ lưu plaintext). 64 hex = vừa cột string(64). */
    public static function hash(string $plain): string
    {
        return hash('sha256', $plain);
    }
}

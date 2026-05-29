<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShortLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','slug','original_url','title','password',
        'status','expires_at','max_clicks','total_clicks','valid_views','total_earned',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function clicks(): HasMany { return $this->hasMany(Click::class); }
    public function adImpressions(): HasMany { return $this->hasMany(AdImpression::class); }
    public function reports(): HasMany { return $this->hasMany(ReportedLink::class); }
    public function tags(): BelongsToMany { return $this->belongsToMany(Tag::class, 'short_link_tag'); }

    public function isActive(): bool { return $this->status === 'active'; }
    public function hasPassword(): bool { return ! empty($this->password); }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isLimitReached(): bool
    {
        return $this->max_clicks !== null && $this->total_clicks >= $this->max_clicks;
    }

    /** Link còn phục vụ redirect được không (active + chưa hết hạn + chưa đạt giới hạn). */
    public function isLive(): bool
    {
        return $this->isActive() && ! $this->isExpired() && ! $this->isLimitReached();
    }

    /** Nhãn trạng thái hiển thị, ưu tiên hết hạn / đạt giới hạn trước. */
    public function displayStatus(): string
    {
        if ($this->status === 'blocked') return 'blocked';
        if ($this->status === 'disabled') return 'disabled';
        if ($this->isExpired()) return 'expired';
        if ($this->isLimitReached()) return 'limit_reached';

        return 'active';
    }
}

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
